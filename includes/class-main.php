<?php
class Yab_Main {

    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->plugin_name = 'tappersia';
        $this->version = YAB_VERSION;
        $this->load_dependencies();
    }

    private function load_dependencies() {
        require_once YAB_PLUGIN_DIR . 'admin/class-admin-menu.php';
        require_once YAB_PLUGIN_DIR . 'admin/class-ajax-handler.php';
        require_once YAB_PLUGIN_DIR . 'public/class-shortcode-handler.php';
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
        $admin_menu = new Yab_Admin_Menu( $this->get_plugin_name(), $this->get_version() );
        add_action( 'admin_menu', array( $admin_menu, 'add_plugin_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $admin_menu, 'enqueue_styles_and_scripts' ) );

        $ajax_handler = new Yab_Ajax_Handler();
        add_action( 'wp_ajax_yab_save_banner', array( $ajax_handler, 'save_banner' ) );
        add_action( 'wp_ajax_yab_search_content', array( $ajax_handler, 'search_content' ) );
        add_action( 'wp_ajax_yab_fetch_hotels_from_api', array( $ajax_handler, 'fetch_hotels_from_api' ) );
        add_action( 'wp_ajax_yab_delete_banner', array( $ajax_handler, 'delete_banner' ) );
        add_action( 'wp_ajax_yab_fetch_cities_from_api', array( $ajax_handler, 'yab_fetch_cities_from_api' ) );
        add_action( 'wp_ajax_yab_fetch_hotel_details_from_api', array( $ajax_handler, 'yab_fetch_hotel_details_from_api' ) );
        add_action( 'wp_ajax_yab_fetch_tours_from_api', array( $ajax_handler, 'fetch_tours_from_api' ) );
        add_action( 'wp_ajax_yab_fetch_tour_cities_from_api', array( $ajax_handler, 'fetch_tour_cities_from_api' ) );
        add_action( 'wp_ajax_yab_fetch_tour_details_from_api', array( $ajax_handler, 'yab_fetch_tour_details_from_api' ) );
    }
    
    private function define_public_hooks() {
        $shortcode_handler = new Yab_Shortcode_Handler();
        add_action('init', array($shortcode_handler, 'register_shortcodes'));
        
        // Add hook for public-facing scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_styles_and_scripts'));
    }
    
    public function enqueue_public_styles_and_scripts() {
        // Enqueue Google Fonts
        wp_enqueue_style( 'yab-roboto-font', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap', array(), null );

        // Enqueue a new public CSS file for global banner styles
        wp_enqueue_style( 'yab-public-style', YAB_PLUGIN_URL . 'assets/css/public-style.css', array('yab-roboto-font'), $this->version, 'all' );
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