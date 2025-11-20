<?php
class Yab_Admin_Menu {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function add_plugin_admin_menu() {
        
        $logo_url = YAB_PLUGIN_URL . 'assets/image/logo.png';

        add_menu_page('Tappersia', 'Tappersia', 'manage_options', $this->plugin_name, array( $this, 'display_add_new_page' ), $logo_url, 25 );

        add_submenu_page($this->plugin_name, 'Add New', 'Add New', 'manage_options', $this->plugin_name, array( $this, 'display_add_new_page' ));
        add_submenu_page($this->plugin_name, 'All Banners', 'All Banners', 'manage_options', $this->plugin_name . '-list', array( $this, 'display_list_page' ));
        
        add_submenu_page(
            $this->plugin_name,
            'License Settings',
            'License',
            'manage_options',
            $this->plugin_name . '-license',
            array($this, 'display_license_settings_page')
        );
    }

    public function display_add_new_page() {
        require_once YAB_PLUGIN_DIR . 'admin/views/view-add-new-banner.php';
    }

    public function display_list_page() {
        require_once YAB_PLUGIN_DIR . 'admin/views/view-list-banners.php';
    }

    public function display_license_settings_page() {
        require_once YAB_PLUGIN_DIR . 'admin/views/view-license-settings.php';
    }


    public function enqueue_styles_and_scripts( $hook ) {
        
        // این فایل CSS *همیشه* بارگذاری می‌شود (از طریق هوکی که در class-main.php ثبت شد)
        wp_enqueue_style( 
            'yab-admin-global-style', // اسم جدید
            YAB_PLUGIN_URL . 'assets/css/admin-global.css', // فایل جدید
            array(), 
            $this->version, 
            'all' 
        );

        // این چک کردن مثل قبل باقی می‌ماند تا اسکریپت‌های سنگین فقط در صفحات پلاگین لود شوند
        if ( strpos($hook, $this->plugin_name) === false && strpos($hook, 'tappersia-activate') === false ) {
            return;
        }

        // این فایل فقط در صفحات لایسنس و فعال‌سازی بارگذاری می‌شود (درست است)
        if (strpos($hook, 'tappersia-activate') !== false || strpos($hook, $this->plugin_name . '-license') !== false) {
             wp_enqueue_style( 'yab-license-style', YAB_PLUGIN_URL . 'assets/css/yab-license-style.css', array(), $this->version, 'all' );
        }

        // اگر هوک شامل نام پلاگین نباشد، اسکریپت‌های اصلی (Vue.js و ...) را بارگذاری نکن
        // (این کار مانع بارگذاری Vue در صفحه لایسنس می‌شود)
        if ( strpos($hook, $this->plugin_name) === false ) {
            return;
        }

        // Remove all admin notices for plugin pages
        add_action('admin_print_scripts', array($this, 'remove_admin_notices'), 999);

        // Enqueue styles and scripts
        wp_enqueue_style( 'yab-roboto-font', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap', array(), null );

        // Enqueue Swiper
        wp_enqueue_style( 'swiper-css', YAB_PLUGIN_URL . 'assets/vendor/swiper/swiper-bundle.min.css', array(), '12.0.2' );
        wp_enqueue_script( 'swiper-js', YAB_PLUGIN_URL . 'assets/vendor/swiper/swiper-bundle.min.js', array(), '12.0.2', true );

        // Enqueue SortableJS
        wp_enqueue_script( 'sortable-js', 'https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js', array(), '1.15.0', true );

        // Enqueue Coloris library
        wp_enqueue_style( 'yab-coloris-css', YAB_PLUGIN_URL . 'assets/vendor/coloris/coloris.min.css', array(), '0.23.0', 'all' );
        wp_enqueue_script( 'yab-coloris-js', YAB_PLUGIN_URL . 'assets/vendor/coloris/coloris.min.js', array(), '0.23.0', true );

        // Enqueue WordPress Media Uploader scripts
        wp_enqueue_media();

        // Enqueue Tailwind (consider removing if you compile your own CSS)
        wp_enqueue_script( 'yab-tailwind', 'https://cdn.tailwindcss.com', array(), null, false );

        // Enqueue Vue.js
        wp_enqueue_script( 'yab-vue', 'https://unpkg.com/vue@3/dist/vue.global.js', array(), '3.4.27', true );

        // Enqueue Admin Style
        wp_enqueue_style( 'yab-admin-style', YAB_PLUGIN_URL . 'assets/css/admin-style.css', array('yab-coloris-css'), $this->version, 'all' );

        // Enqueue Modal Component
        wp_enqueue_script( 'yab-modal-component', YAB_PLUGIN_URL . 'assets/js/admin-modal-component.js', array( 'yab-vue' ), $this->version, true );

        // Enqueue Main App or List App based on page
        $page_slug = $this->plugin_name;
        if (strpos($hook, $page_slug) !== false) {
             if (strpos($hook, $page_slug . '-list') !== false) {
                // Enqueue List App
                wp_enqueue_script( 'yab-list-app', YAB_PLUGIN_URL . 'assets/js/admin-list-app.js', array( 'yab-vue', 'jquery', 'yab-modal-component' ), $this->version, true );
                wp_localize_script( 'yab-list-app', 'yab_list_data', $this->get_list_page_data() );
            } 
            else if (strpos($hook, $page_slug . '-license') === false) { 
                
                // Enqueue Main Editor App
                wp_enqueue_script( 'yab-admin-app-main', YAB_PLUGIN_URL . 'assets/js/admin/app.js', array( 'yab-vue', 'jquery', 'yab-modal-component', 'swiper-js', 'sortable-js', 'yab-coloris-js' ), $this->version, true );

                // Make the main app script a module
                add_filter( 'script_loader_tag', function ( $tag, $handle, $src ) {
                    if ( 'yab-admin-app-main' === $handle ) {
                        $tag = '<script type="module" src="' . esc_url( $src ) . '" id="yab-admin-app-main-js"></script>';
                    }
                    return $tag;
                }, 10, 3 );

                // Localize data for the main app
                wp_localize_script( 'yab-admin-app-main', 'yab_data', $this->get_add_new_page_data() );
            }
        }
    }

    public function remove_admin_notices() {
        remove_all_actions('admin_notices');
        remove_all_actions('user_admin_notices');
        remove_all_actions('network_admin_notices');
        remove_all_actions('all_admin_notices');
    }

    private function get_list_page_data() {
        $all_banners = [];
        $query = new WP_Query([
            'post_type' => 'yab_banner',
            'posts_per_page' => -1,
            'post_status' => ['publish', 'draft']
        ]);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $banner_id = get_the_ID();
                $banner_data = get_post_meta($banner_id, '_yab_banner_data', true);
                $banner_type = get_post_meta($banner_id, '_yab_banner_type', true) ?: 'double-banner'; 

                $display_method = isset($banner_data['displayMethod']) ? $banner_data['displayMethod'] : 'Fixed'; 

                $shortcode = '[unknown_banner]';
                $base_shortcode = str_replace('-', '', $banner_type);
                $base_shortcode = str_replace('contenthtmlbanner', 'contenthtml', $base_shortcode);
                $base_shortcode = str_replace('contenthtmlsidebarbanner', 'contenthtmlsidebar', $base_shortcode);
                $base_shortcode = str_replace('welcomepackagebanner', 'welcomepackage', $base_shortcode); 


                if ($display_method === 'Embeddable') {
                    $shortcode = '[' . $base_shortcode . ' id="' . $banner_id . '"]';
                } else { 
                     $shortcode = '[' . $base_shortcode . '_fixed]';
                }


                $all_banners[] = [
                    'id' => $banner_id,
                    'title' => get_the_title(),
                    'date' => get_the_date('Y/m/d'),
                    'is_active' => isset($banner_data['isActive']) ? $banner_data['isActive'] : false, 
                    'display_method' => $display_method,
                    'shortcode' => $shortcode,
                    'type' => $banner_type,
                    'edit_url' => admin_url('admin.php?page=' . $this->plugin_name . '&action=edit&banner_id=' . $banner_id),
                ];
            }
        }
        wp_reset_postdata();

        usort($all_banners, function($a, $b) {
            return $b['id'] <=> $a['id'];
        });

        return [
            'banners' => $all_banners,
            'addNewURL' => admin_url('admin.php?page=' . $this->plugin_name),
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('yab_nonce'),
        ];
    }

     private function get_add_new_page_data() {
        $initial_posts = get_posts(['numberposts' => 50, 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC']);
        $initial_categories = get_categories(['hide_empty' => false, 'number' => 50, 'orderby' => 'name', 'order' => 'ASC']);
        $initial_pages = get_pages(['number' => 50, 'sort_column' => 'post_title', 'sort_order' => 'ASC']);

        $localized_data = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('yab_nonce'),
            'posts' => array_map(fn($p) => ['ID' => $p->ID, 'post_title' => $p->post_title], $initial_posts),
            'categories' => array_map(fn($c) => ['term_id' => $c->term_id, 'name' => $c->name], $initial_categories),
            'pages' => array_map(fn($p) => ['ID' => $p->ID, 'post_title' => $p->post_title], $initial_pages),
            'existing_banner' => null
        );

        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['banner_id'])) {
            $banner_id = intval($_GET['banner_id']);
            $banner_post = get_post($banner_id);
            $banner_data = get_post_meta($banner_id, '_yab_banner_data', true); 
            $banner_type = get_post_meta($banner_id, '_yab_banner_type', true); 

            if ($banner_post && $banner_data && $banner_type) {
                $banner_data['name'] = $banner_post->post_title; 
                $banner_data['id'] = $banner_id; 
                $banner_data['type'] = $banner_type; 

                $localized_data['existing_banner'] = $banner_data;
                
                $displayOn = $banner_data['displayOn'] ?? ['posts' => [], 'pages' => [], 'categories' => []];

                if (!empty($displayOn['posts'])) {
                    $selected_posts = get_posts(['post__in' => $displayOn['posts'], 'numberposts' => -1, 'post_type' => 'post', 'post_status' => 'publish']);
                    $localized_data['posts'] = array_values(array_unique(array_merge($localized_data['posts'], array_map(fn($p) => ['ID' => $p->ID, 'post_title' => $p->post_title], $selected_posts)), SORT_REGULAR));
                }
                if (!empty($displayOn['pages'])) {
                    $selected_pages = get_pages(['include' => $displayOn['pages'], 'post_status' => 'publish']);
                    $localized_data['pages'] = array_values(array_unique(array_merge($localized_data['pages'], array_map(fn($p) => ['ID' => $p->ID, 'post_title' => $p->post_title], $selected_pages)), SORT_REGULAR));
                }
                if (!empty($displayOn['categories'])) {
                    $selected_cats = get_categories(['include' => $displayOn['categories'], 'hide_empty' => false]);
                    $localized_data['categories'] = array_values(array_unique(array_merge($localized_data['categories'], array_map(fn($c) => ['term_id' => $c->term_id, 'name' => $c->name], $selected_cats)), SORT_REGULAR));
                }
            }
        }

        return $localized_data;
    }
}
?>