<?php
class Yab_Main {

    protected $plugin_name;
    protected $version;

    private $license_manager;
    private $license_page_handler;
    private $admin_menu; // <-- این برای بارگذاری اسکریپت‌ها لازم است

    public function __construct() {
        $this->plugin_name = 'tappersia';
        $this->version = YAB_VERSION;
        $this->load_dependencies();

        // بارگذاری سیستم لایسنس
        require_once YAB_PLUGIN_DIR . 'includes/license/class-yab-license-manager.php';
        $this->license_manager = new Yab_License_Manager();

        // بارگذاری هندلر صفحه لایسنس
        require_once YAB_PLUGIN_DIR . 'admin/class-yab-license-page.php';
        $this->license_page_handler = new Yab_License_Page($this->license_manager);
        
        // --- START: این خط مهم است ---
        // ما به یک نمونه از Admin_Menu نیاز داریم تا اسکریپت‌ها را *همیشه* بارگذاری کنیم
        $this->admin_menu = new Yab_Admin_Menu( $this->get_plugin_name(), $this->get_version() );
        // --- END: خط مهم ---
    }

    private function load_dependencies() {
        require_once YAB_PLUGIN_DIR . 'admin/class-admin-menu.php';
        require_once YAB_PLUGIN_DIR . 'admin/class-ajax-handler.php';
        require_once YAB_PLUGIN_DIR . 'public/class-shortcode-handler.php';
    }

    public function run() {
        
        // --- START: این بلاک اصلاح شد ---
        // این هوک باید *همیشه* در پنل ادمین اجرا شود (قبل از چک کردن لایسنس)
        // تا فایل 'admin-global.css' (مخصوص لوگوی منو) همیشه بارگذاری شود.
        if (is_admin()) {
            add_action( 'admin_enqueue_scripts', array( $this->admin_menu, 'enqueue_styles_and_scripts' ) );
        }
        // --- END: بلاک اصلاح شد ---

        // حالا لایسنس را چک می‌کنیم
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
        // از نمونه‌ای که در constructor ساختیم استفاده می‌کنیم
        add_action( 'admin_menu', array( $this->admin_menu, 'add_plugin_admin_menu' ) );
        
        // نکته: هوک 'admin_enqueue_scripts' قبلاً به متد run() منتقل شده است
        
        new Yab_Ajax_Handler();
        
        // هوک برای فرم غیرفعال‌سازی
        add_action('admin_post_yab_deactivate_license', [$this, 'handle_deactivate_license']);
    }
    
    private function define_activation_hooks() {
        // اضافه کردن منوی صفحه فعال‌سازی
        add_action('admin_menu', array($this, 'register_activation_page'));
        
        // منطق ریدایرکت
        add_action('admin_init', array($this, 'redirect_to_activation_page'));
        
        // نوتیس ادمین
        add_action('admin_notices', array($this, 'show_activation_notice'));
        add_action('network_admin_notices', array($this, 'show_activation_notice'));

        // پردازش فرم فعال‌سازی
        add_action('admin_init', array($this->license_page_handler, 'process_activation_submission'));
    }

    /**
     * منوی صفحه فعال‌سازی را ثبت می‌کند (زمانی که لایسنس فعال نیست)
     */
    public function register_activation_page() {
        // --- START: این بلاک اصلاح شد ---
        // ما باید در اینجا نیز URL لوگو را تعریف کنیم
        $logo_url = YAB_PLUGIN_URL . 'assets/image/logo.png';
        
        add_menu_page(
            'Activate Tappersia',
            'Tappersia',
            'manage_options',
            'tappersia-activate', // اسلاگ صفحه فعال‌سازی
            array($this->license_page_handler, 'render_page'),
            $logo_url, // <-- استفاده از لوگو به جای dashicon
            25
        );
        // --- END: بلاک اصلاح شد ---
    }

    public function redirect_to_activation_page() {
        // فقط در صورتی ریدایرکت کن که در صفحه فعال‌سازی نباشیم
        if (get_option(Yab_License_Manager::NEEDS_ACTIVATION_OPTION, false) &&
            (!isset($_GET['page']) || $_GET['page'] !== 'tappersia-activate'))
        {
            // اگر در حال پردازش فرم خودمان هستیم، ریدایرکت نکن
            if (isset($_POST['yab_activate_license'])) {
                return;
            }
            
            delete_option(Yab_License_Manager::NEEDS_ACTIVATION_OPTION); // فقط یکبار ریدایرکت کن
            wp_safe_redirect(admin_url('admin.php?page=tappersia-activate'));
            exit;
        }
    }

    public function show_activation_notice() {
        // در خود صفحه فعال‌سازی، نوتیس را نشان نده
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

    // ... (بقیه متدهای کلاس: define_public_hooks, display_sticky_banner_in_footer, و غیره) ...
    
    private function define_public_hooks() {
        $shortcode_handler = new Yab_Shortcode_Handler();
        add_action('init', array($shortcode_handler, 'register_shortcodes'));
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_styles_and_scripts'));

        // START: Add action to render sticky banner in the footer
        add_action('wp_footer', array($this, 'display_sticky_banner_in_footer'));
        // END: Add action
    }
    
    // START: New function to display sticky banner
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

        if (empty($banners)) {
            return;
        }

        $banner_post = $banners[0];
        $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
        
        if (empty($data['displayOn'])) {
            return;
        }

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
    // END: New function

    public function enqueue_public_styles_and_scripts() {
        wp_enqueue_style( 'yab-roboto-font', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap', array(), null );
        wp_enqueue_style( 'yab-public-style', YAB_PLUGIN_URL . 'assets/css/public-style.css', array('yab-roboto-font'), $this->version, 'all' );
        
        // Enqueue Swiper for frontend if a tour-carousel might be present
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