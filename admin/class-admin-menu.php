<?php
class Yab_Admin_Menu {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function add_plugin_admin_menu() {
        add_menu_page('Awesome Banner', 'Awesome Banner', 'manage_options', $this->plugin_name, array( $this, 'display_add_new_page' ), 'dashicons-art', 25 );
        add_submenu_page($this->plugin_name, 'Add New', 'Add New', 'manage_options', $this->plugin_name, array( $this, 'display_add_new_page' ));
        add_submenu_page($this->plugin_name, 'All Banners', 'All Banners', 'manage_options', $this->plugin_name . '-list', array( $this, 'display_list_page' ));
    }

    public function display_add_new_page() {
        require_once YAB_PLUGIN_DIR . 'admin/views/view-add-new-banner.php';
    }

    public function display_list_page() {
        require_once YAB_PLUGIN_DIR . 'admin/views/view-list-banners.php';
    }

    public function enqueue_styles_and_scripts( $hook ) {
        // Only load on plugin pages
        if ( strpos($hook, $this->plugin_name) === false ) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script( 'yab-tailwind', 'https://cdn.tailwindcss.com', array(), null, false );
        wp_enqueue_script( 'yab-vue', 'https://unpkg.com/vue@3/dist/vue.global.js', array(), '3.4.27', true );
        wp_enqueue_style( 'yab-admin-style', YAB_PLUGIN_URL . 'assets/css/admin-style.css', array(), $this->version, 'all' );
        wp_enqueue_script( 'yab-admin-app', YAB_PLUGIN_URL . 'assets/js/admin-app.js', array( 'yab-vue', 'jquery' ), $this->version, true );
        
        wp_localize_script( 'yab-admin-app', 'yab_data', $this->get_localized_data() );
    }

    private function get_localized_data() {
        $localized_data = [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('yab_nonce'),
            'posts' => [],
            'categories' => [],
            'pages' => [],
            'existing_banner' => null
        ];

        // Only provide initial data if editing an existing banner
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['banner_id'])) {
            $banner_id = intval($_GET['banner_id']);
            $banner_post = get_post($banner_id);
            $banner_data = get_post_meta($banner_id, '_yab_banner_data', true);

            if ($banner_post && $banner_data) {
                // Combine post data with meta data
                $banner_data['id'] = $banner_id;
                $banner_data['name'] = $banner_post->post_title;
                $localized_data['existing_banner'] = $banner_data;
                
                // Pre-load the selected items so they appear on page load
                if (!empty($banner_data['displayOn']['posts'])) {
                    $posts = get_posts(['post__in' => $banner_data['displayOn']['posts'], 'numberposts' => -1]);
                    $localized_data['posts'] = array_map(fn($p) => ['ID' => $p->ID, 'post_title' => $p->post_title], $posts);
                }
                if (!empty($banner_data['displayOn']['pages'])) {
                    $pages = get_pages(['include' => $banner_data['displayOn']['pages']]);
                    $localized_data['pages'] = array_map(fn($p) => ['ID' => $p->ID, 'post_title' => $p->post_title], $pages);
                }
                if (!empty($banner_data['displayOn']['categories'])) {
                    $cats = get_categories(['include' => $banner_data['displayOn']['categories'], 'hide_empty' => false]);
                    $localized_data['categories'] = array_map(fn($c) => ['term_id' => $c->term_id, 'name' => $c->name], $cats);
                }
            }
        }
        
        return $localized_data;
    }
}