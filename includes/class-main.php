<?php
class Yab_Main {

    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->plugin_name = 'your-awesome-banner';
        $this->version = YAB_VERSION;
    }

    public function run() {
        $this->define_cpt();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function define_cpt() {
        add_action('init', array($this, 'register_banner_cpt'));
    }

    private function define_admin_hooks() {
        require_once YAB_PLUGIN_DIR . 'admin/class-admin-menu.php';
        $admin_menu = new Yab_Admin_Menu( $this->get_plugin_name(), $this->get_version() );
        add_action( 'admin_menu', array( $admin_menu, 'add_plugin_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $admin_menu, 'enqueue_styles_and_scripts' ) );

        require_once YAB_PLUGIN_DIR . 'admin/class-ajax-handler.php';
        $ajax_handler = new Yab_Ajax_Handler();
        add_action( 'wp_ajax_yab_save_double_banner', array( $ajax_handler, 'save_double_banner' ) );
    }
    
    private function define_public_hooks() {
        require_once YAB_PLUGIN_DIR . 'public/class-shortcode-handler.php';
        $shortcode_handler = new Yab_Shortcode_Handler();
        add_action('init', array($shortcode_handler, 'register_shortcodes'));
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