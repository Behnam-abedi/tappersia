<?php
class Yab_Admin_Menu {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function add_plugin_admin_menu() {
        add_menu_page('Tappersia', 'Tappersia', 'manage_options', $this->plugin_name, array( $this, 'display_add_new_page' ), 'dashicons-art', 25 );
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
        if ( strpos($hook, $this->plugin_name) === false ) {
            return;
        }
        
        // Enqueue Roboto font for admin panel
        wp_enqueue_style( 'yab-roboto-font', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap', array(), null );

        // Enqueue Swiper JS and CSS
        wp_enqueue_style( 'swiper-css', YAB_PLUGIN_URL . 'assets/vendor/swiper/swiper-bundle.min.css', array(), '12.0.2' );
        wp_enqueue_script( 'swiper-js', YAB_PLUGIN_URL . 'assets/vendor/swiper/swiper-bundle.min.js', array(), '12.0.2', true );

        wp_enqueue_media();
        wp_enqueue_script( 'yab-tailwind', 'https://cdn.tailwindcss.com', array(), null, false );
        wp_enqueue_script( 'yab-vue', 'https://unpkg.com/vue@3/dist/vue.global.js', array(), '3.4.27', true );
        wp_enqueue_style( 'yab-admin-style', YAB_PLUGIN_URL . 'assets/css/admin-style.css', array(), $this->version, 'all' );
        
        // This component is loaded globally as it's used by both apps
        wp_enqueue_script( 'yab-modal-component', YAB_PLUGIN_URL . 'assets/js/admin-modal-component.js', array( 'yab-vue' ), $this->version, true );

        $page_slug = $this->plugin_name;
        if (strpos($hook, $page_slug) !== false) {
             if (strpos($hook, $page_slug . '-list') !== false) {
                // The list app remains a simple, single file for now
                wp_enqueue_script( 'yab-list-app', YAB_PLUGIN_URL . 'assets/js/admin-list-app.js', array( 'yab-vue', 'jquery', 'yab-modal-component' ), $this->version, true );
                wp_localize_script( 'yab-list-app', 'yab_list_data', $this->get_list_page_data() );
            } else {
                // **MODIFIED SECTION FOR MODULAR JS**
                // 1. Enqueue the new main app file. The handle must be unique.
                wp_enqueue_script( 'yab-admin-app-main', YAB_PLUGIN_URL . 'assets/js/admin/app.js', array( 'yab-vue', 'jquery', 'yab-modal-component', 'swiper-js' ), $this->version, true );
                
                // 2. Use a filter to add `type="module"` to our specific script tag.
                // This is the standard and safe way to load ES modules in WordPress.
                add_filter( 'script_loader_tag', function ( $tag, $handle, $src ) {
                    // Check for our specific script handle
                    if ( 'yab-admin-app-main' === $handle ) {
                        // Replace the standard script tag with one that has type="module"
                        $tag = '<script type="module" src="' . esc_url( $src ) . '" id="yab-admin-app-main-js"></script>';
                    }
                    return $tag;
                }, 10, 3 );
                
                // 3. Localize the script with the same handle to pass PHP data.
                wp_localize_script( 'yab-admin-app-main', 'yab_data', $this->get_add_new_page_data() );
            }
        }
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
                $banner_type = get_post_meta($banner_id, '_yab_banner_type', true) ?: 'double-banner'; // Default for safety

                $display_method = isset($banner_data['displayMethod']) ? $banner_data['displayMethod'] : 'Fixed';
                
                $shortcode = '[unknown_banner]';
                $base_shortcode = str_replace('-', '', $banner_type);

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
                    'edit_url' => admin_url('admin.php?page=tappersia&action=edit&banner_id=' . $banner_id),
                ];
            }
        }
        wp_reset_postdata();

        return [
            'banners' => $all_banners,
            'addNewURL' => admin_url('admin.php?page=tappersia'),
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('yab_nonce'),
        ];
    }

    private function get_add_new_page_data() {
        $initial_posts = get_posts(['numberposts' => 50, 'post_status' => 'publish']);
        $initial_categories = get_categories(['hide_empty' => false, 'number' => 50]);
        $initial_pages = get_pages(['number' => 50]);

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


            if ($banner_post && $banner_data) {
                $banner_data['name'] = $banner_post->post_title;
                $banner_data['id'] = $banner_id;
                $banner_data['type'] = $banner_type ?: 'double-banner'; // Default for safety

                $localized_data['existing_banner'] = $banner_data;
                
                if (!empty($banner_data['displayOn']['posts'])) {
                    $selected_posts = get_posts(['post__in' => $banner_data['displayOn']['posts'], 'numberposts' => -1]);
                    $localized_data['posts'] = array_unique(array_merge($localized_data['posts'], array_map(fn($p) => ['ID' => $p->ID, 'post_title' => $p->post_title], $selected_posts)), SORT_REGULAR);
                }
                if (!empty($banner_data['displayOn']['pages'])) {
                    $selected_pages = get_pages(['include' => $banner_data['displayOn']['pages']]);
                    $localized_data['pages'] = array_unique(array_merge($localized_data['pages'], array_map(fn($p) => ['ID' => $p->ID, 'post_title' => $p->post_title], $selected_pages)), SORT_REGULAR);
                }
                if (!empty($banner_data['displayOn']['categories'])) {
                    $selected_cats = get_categories(['include' => $banner_data['displayOn']['categories'], 'hide_empty' => false]);
                    $localized_data['categories'] = array_unique(array_merge($localized_data['categories'], array_map(fn($c) => ['term_id' => $c->term_id, 'name' => $c->name], $selected_cats)), SORT_REGULAR);
                }
            }
        }
        
        return $localized_data;
    }
}