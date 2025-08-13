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
        add_submenu_page($this->plugin_name, 'All Elements', 'All Elements', 'manage_options', $this->plugin_name . '-list', array( $this, 'display_list_page' ));
    }

    public function display_add_new_page() {
        require_once YAB_PLUGIN_DIR . 'admin/views/view-add-new-banner.php';
    }

    public function display_list_page() {
        require_once YAB_PLUGIN_DIR . 'admin/views/view-list-banners.php';
    }

    public function enqueue_styles_and_scripts( $hook ) {
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
        $posts = get_posts(array('numberposts' => 200, 'post_status' => 'publish'));
        $categories = get_categories(array('hide_empty' => false, 'number' => 200));
        $pages = get_pages(array('number' => 200));

        $localized_data = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('yab_nonce'),
            'posts' => array_map(function($post) { return ['ID' => $post->ID, 'post_title' => $post->post_title]; }, $posts),
            'categories' => array_map(function($cat) { return ['term_id' => $cat->term_id, 'name' => $cat->name]; }, $categories),
            'pages' => array_map(function($page) { return ['ID' => $page->ID, 'post_title' => $page->post_title]; }, $pages),
            'existing_banner' => null
        );

        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['banner_id'])) {
            $banner_id = intval($_GET['banner_id']);
            $banner_data = get_post_meta($banner_id, '_yab_banner_data', true);
            if ($banner_data) {
                $banner_post = get_post($banner_id);
                $banner_data['name'] = $banner_post->post_title;
                $banner_data['id'] = $banner_id;
                $localized_data['existing_banner'] = $banner_data;
            }
        }
        
        return $localized_data;
    }
}