<?php
// includes/class-main.php

class Yab_Main {

    protected $plugin_name;
    protected $version;
    protected $plugin_file; // <-- اضافه شده
    private $updater; // <-- اضافه شده

    private $license_manager;
    private $license_page_handler;
    private $admin_menu;

    /**
     * Constructor.
     *
     * @param string $plugin_file The full path to the main plugin file.
     */
    public function __construct( $plugin_file ) { // <-- ویرایش شده
        $this->plugin_file = $plugin_file; // <-- اضافه شده
        $this->plugin_name = 'tappersia';
        $this->version = YAB_VERSION;
        $this->load_dependencies();

        // بارگذاری سیستم لایسنس
        require_once YAB_PLUGIN_DIR . 'includes/license/class-yab-license-manager.php';
        $this->license_manager = new Yab_License_Manager();

        // بارگذاری هندلر صفحه لایسنس
        require_once YAB_PLUGIN_DIR . 'admin/class-yab-license-page.php';
        $this->license_page_handler = new Yab_License_Page($this->license_manager);
        
        $this->admin_menu = new Yab_Admin_Menu( $this->get_plugin_name(), $this->get_version() );
    }

    private function load_dependencies() {
        require_once YAB_PLUGIN_DIR . 'admin/class-admin-menu.php';
        require_once YAB_PLUGIN_DIR . 'admin/class-ajax-handler.php';
        require_once YAB_PLUGIN_DIR . 'public/class-shortcode-handler.php';
        
        // <-- اضافه شده -->
        // بارگذاری فکتوری آپدیتر گیت‌هاب
        require_once YAB_PLUGIN_DIR . 'includes/updater/class-yab-updater-factory.php';
    }

    public function run() {
        
        if (is_admin()) {
            add_action( 'admin_enqueue_scripts', array( $this->admin_menu, 'enqueue_styles_and_scripts' ) );
        }

        // چک کردن لایسنس
        if ($this->license_manager->is_license_valid()) {
            // لایسنس معتبر است: پلاگین کامل را اجرا کن
            $this->define_cpt();
            $this->define_admin_hooks();
            $this->define_public_hooks();
        } else {
            // لایسنس نامعتبر است: فقط هوک‌های فعال‌سازی را اجرا کن
            if (is_admin()) {
                $this->define_activation_hooks();
            }
        }
    }

    private function define_cpt() {
        add_action('init', array($this, 'register_banner_cpt'));
    }

    private function define_admin_hooks() {
        add_action( 'admin_menu', array( $this->admin_menu, 'add_plugin_admin_menu' ) );
        
        new Yab_Ajax_Handler();
        
        add_action('admin_post_yab_deactivate_license', [$this, 'handle_deactivate_license']);

        // --- START: بلوک آپدیتر (ویرایش شده برای ای‌جکس) ---
        // 1. راه‌اندازی آپدیتر (بدون تغییر)
        $this->updater = Yab_Updater_Factory::build(
            $this->plugin_file, // مسیر فایل اصلی پلاگین
            $this->version      // نسخه فعلی
        );
        $this->updater->init(); // ثبت هوک‌های 'pre_set_site_transient_update_plugins' و 'plugins_api'

        // 2. هوک‌های ای‌جکس جدید را اضافه کنید
        add_action('wp_ajax_yab_ajax_check_for_updates', [$this, 'ajax_check_for_updates']);
        add_action('wp_ajax_yab_ajax_install_update', [$this, 'ajax_install_update']);
        // --- END: بلوک آپدیتر ---
    }
    
    // --- START: NEW AJAX HANDLER FUNCTIONS ---

    /**
     * هندلر ای‌جکس برای "بررسی آپدیت"
     */
    public function ajax_check_for_updates() {
        // 1. بررسی‌های امنیتی
        check_ajax_referer('yab_update_nonce', 'nonce');
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'دسترسی مجاز نیست.' ] );
        }

        // 2. پاک کردن کش‌ها
        if ( $this->updater ) {
            $this->updater->force_check();
        }
        delete_site_transient( 'update_plugins' );

        // 3. وادار کردن وردپرس به بررسی مجدد آپدیت‌ها (این تابع هوک ما را صدا می‌زند)
        wp_update_plugins();

        // 4. دریافت نتیجه بررسی
        $transient = get_site_transient( 'update_plugins' );
        $slug = $this->updater->get_plugin_slug();

        if ( ! empty( $transient->response[ $slug ] ) ) {
            // آپدیت موجود است
            $update_data = $transient->response[ $slug ];
            wp_send_json_success( [
                'update_available' => true,
                'new_version' => $update_data->new_version,
                'message' => 'نسخه ' . $update_data->new_version . ' آماده نصب است.',
            ] );
        } else {
            // آپدیتی موجود نیست
            wp_send_json_success( [
                'update_available' => false,
                'message' => 'شما از آخرین نسخه استفاده می‌کنید (' . $this->version . ').',
            ] );
        }
    }

    /**
     * هندلر ای‌جکس برای "نصب آپدیت"
     */
    public function ajax_install_update() {
        // 1. بررسی‌های امنیتی
        check_ajax_referer('yab_update_nonce', 'nonce');
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'دسترسی مجاز نیست.' ] );
        }

        // 2. بارگذاری فایل‌های مورد نیاز وردپرس برای آپگرید
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';
        // این فایل جدید و ضروری است
        require_once YAB_PLUGIN_DIR . 'includes/updater/class-yab-silent-upgrader-skin.php';

        // 3. آماده‌سازی و اجرای آپگرید
        $slug = $this->updater->get_plugin_slug();
        $skin = new Yab_Silent_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader( $skin );

        $result = $upgrader->upgrade( $slug );

        // 4. بررسی نتیجه
        if ( is_wp_error( $result ) ) {
            // خطای WP Error
            wp_send_json_error( [ 'message' => 'خطا در آپگرید: ' . $result->get_error_message() ] );
        } elseif ( $skin->get_errors() ) {
            // خطاهای ثبت شده توسط Skin
            wp_send_json_error( [ 'message' => 'خطا در پوسته آپگرید: ' . implode( ', ', $skin->get_errors() ) ] );
        } elseif ( $result === null ) {
            // خطای احتمالی در اتصال یا فایل سیستم
            wp_send_json_error( [ 'message' => 'خطا در نصب. ممکن است دسترسی فایل (Permissions) صحیح نباشد.' ] );
        }

        // 5. فعال‌سازی مجدد پلاگین (وردپرس پس از آپدیت آن را غیرفعال می‌کند)
        activate_plugin( $slug );
        
        // 6. ارسال پیام موفقیت
        wp_send_json_success( [
            'reload' => true,
            'message' => 'آپدیت با موفقیت نصب شد. صفحه در حال بارگذاری مجدد است...',
        ] );
    }

    // --- END: NEW AJAX HANDLER FUNCTIONS ---
    
    // ... (بقیه متدهای کلاس بدون تغییر هستند) ...

    private function define_activation_hooks() {
        add_action('admin_menu', array($this, 'register_activation_page'));
        add_action('admin_init', array($this, 'redirect_to_activation_page'));
        add_action('admin_notices', array($this, 'show_activation_notice'));
        add_action('network_admin_notices', array($this, 'show_activation_notice'));
        add_action('admin_init', array($this->license_page_handler, 'process_activation_submission'));
    }

    public function register_activation_page() {
        $logo_url = YAB_PLUGIN_URL . 'assets/image/logo.png';
        
        add_menu_page(
            'Activate Tappersia',
            'Tappersia',
            'manage_options',
            'tappersia-activate',
            array($this->license_page_handler, 'render_page'),
            $logo_url,
            25
        );
    }

    public function redirect_to_activation_page() {
        if (get_option(Yab_License_Manager::NEEDS_ACTIVATION_OPTION, false) &&
            (!isset($_GET['page']) || $_GET['page'] !== 'tappersia-activate'))
        {
            if (isset($_POST['yab_activate_license'])) {
                return;
            }
            
            delete_option(Yab_License_Manager::NEEDS_ACTIVATION_OPTION);
            wp_safe_redirect(admin_url('admin.php?page=tappersia-activate'));
            exit;
        }
    }

    public function show_activation_notice() {
        if (isset($_GET['page']) && $_GET['page'] === 'tappersia-activate') {
            return;
        }
        
        $activation_url = esc_url(admin_url('admin.php?page=tappersia-activate'));
        echo '<div class="notice notice-error is-dismissible">
            <p><strong>Tappersia Plugin is not active.</strong> Please <a href="' . $activation_url . '">enter your API key</a> to activate the plugin.</p>
        </div>';
    }
    
    public function handle_deactivate_license() {
        if (!isset($_POST['yab_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['yab_nonce']), 'yab_deactivate_license_nonce')) {
            wp_die('Security check failed.');
        }
        if (!current_user_can('manage_options')) {
            wp_die('You do not have permission to do this.');
        }

        $this->license_manager->deactivate_license();
        wp_safe_redirect(admin_url('admin.php?page=tappersia-activate&message=deactivated'));
        exit;
    }

    private function define_public_hooks() {
        $shortcode_handler = new Yab_Shortcode_Handler();
        add_action('init', array($shortcode_handler, 'register_shortcodes'));
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_styles_and_scripts'));
        add_action('wp_footer', array($this, 'display_sticky_banner_in_footer'));
    }
    
    public function display_sticky_banner_in_footer() {
        global $post;
        if (!is_singular() && !is_category() && !is_archive()) {
            return;
        }

        $args = [
            'post_type' => 'yab_banner',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_query' => [
                'relation' => 'AND',
                ['key' => '_yab_display_method', 'value' => 'Fixed'],
                ['key' => '_yab_is_active', 'value' => true],
                ['key' => '_yab_banner_type', 'value' => 'sticky-simple-banner']
            ]
        ];

        $banners = get_posts($args);
        if (empty($banners)) return;

        $banner_post = $banners[0];
        $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
        if (empty($data['displayOn'])) return;

        $cond = $data['displayOn'];
        $post_ids = !empty($cond['posts']) ? array_map('intval', $cond['posts']) : [];
        $page_ids = !empty($cond['pages']) ? array_map('intval', $cond['pages']) : [];
        $cat_ids  = !empty($cond['categories']) ? array_map('intval', $cond['categories']) : [];
        
        $queried_object_id = get_queried_object_id();
        $should_display = false;

        if (is_singular('post') && in_array($queried_object_id, $post_ids)) $should_display = true;
        if (is_page() && in_array($queried_object_id, $page_ids)) $should_display = true;
        if (!empty($cat_ids) && (is_category($cat_ids) || (is_singular('post') && has_category($cat_ids, $post)))) $should_display = true;

        if ($should_display) {
            require_once YAB_PLUGIN_DIR . 'public/Renderers/class-sticky-simple-banner-renderer.php';
            $renderer = new Yab_Sticky_Simple_Banner_Renderer($data, $banner_post->ID);
            echo $renderer->render();
        }
    }

    public function enqueue_public_styles_and_scripts() {
        wp_enqueue_style( 'yab-roboto-font', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap', array(), null );
        wp_enqueue_style( 'yab-public-style', YAB_PLUGIN_URL . 'assets/css/public-style.css', array('yab-roboto-font'), $this->version, 'all' );
        
        wp_enqueue_style( 'swiper-css', YAB_PLUGIN_URL . 'assets/vendor/swiper/swiper-bundle.min.css', array(), '12.0.2' );
        wp_enqueue_script( 'swiper-js', YAB_PLUGIN_URL . 'assets/vendor/swiper/swiper-bundle.min.js', array(), '12.0.2', true );
    }

    public function register_banner_cpt() {
        $labels = array(
            'name'                  => _x( 'Banners', 'Post type general name', 'yab' ),
            'singular_name'         => _x( 'Banner', 'Post type singular name', 'yab' ),
        );
        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => false,
            'show_in_menu'       => false,
            'query_var'          => false,
            'rewrite'            => array( 'slug' => 'yab_banner' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'supports'           => array( 'title' ),
        );
        register_post_type( 'yab_banner', $args );
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }
}