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

        new Yab_Ajax_Handler();
    }
    
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