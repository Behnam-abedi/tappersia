<?php
if (!class_exists('Yab_Ajax_Handler')) :
    class Yab_Ajax_Handler {

        private function get_banner_type_handler($banner_type_slug) {
            // A simple factory to get the correct handler instance
            switch ($banner_type_slug) {
                case 'double-banner':
                    require_once YAB_PLUGIN_DIR . 'includes/BannerTypes/DoubleBanner/DoubleBanner.php';
                    return new Yab_Double_Banner();
                case 'single-banner':
                    require_once YAB_PLUGIN_DIR . 'includes/BannerTypes/SingleBanner/SingleBanner.php';
                    return new Yab_Single_Banner();
                default:
                    return null;
            }
        }
        
        public function save_banner() {
            check_ajax_referer('yab_nonce', 'nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied.'], 403);
                return;
            }

            if (!isset($_POST['banner_data']) || !isset($_POST['banner_type'])) {
                wp_send_json_error(['message' => 'Incomplete data received.']);
                return;
            }
            
            $banner_data = json_decode(stripslashes($_POST['banner_data']), true);
            $banner_type = sanitize_text_field($_POST['banner_type']);

            $handler = $this->get_banner_type_handler($banner_type);

            if (!$handler) {
                 wp_send_json_error(['message' => 'Invalid banner type specified.']);
                 return;
            }
            
            // Delegate the save operation to the specific handler
            $handler->save($banner_data);
        }

        public function search_content() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied.'], 403);
                return;
            }

            $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
            $content_type = isset($_POST['content_type']) ? sanitize_text_field($_POST['content_type']) : 'posts';

            $results = [];

            switch ($content_type) {
                case 'posts':
                    $query = new WP_Query([
                        'post_type' => 'post',
                        'posts_per_page' => 50,
                        's' => $search_term,
                        'post_status' => 'publish'
                    ]);
                    foreach ($query->posts as $post) {
                        $results[] = ['ID' => $post->ID, 'post_title' => $post->post_title];
                    }
                    break;
                case 'pages':
                    $query = new WP_Query([
                        'post_type' => 'page',
                        'posts_per_page' => 50,
                        's' => $search_term,
                        'post_status' => 'publish'
                    ]);
                    foreach ($query->posts as $page) {
                        $results[] = ['ID' => $page->ID, 'post_title' => $page->post_title];
                    }
                    break;
                case 'categories':
                    $terms = get_terms([
                        'taxonomy' => 'category',
                        'name__like' => $search_term,
                        'hide_empty' => false,
                        'number' => 50
                    ]);
                    foreach ($terms as $term) {
                        $results[] = ['term_id' => $term->term_id, 'name' => $term->name];
                    }
                    break;
            }
            wp_send_json_success($results);
            wp_die();
        }

        public function delete_banner() {
            check_ajax_referer('yab_nonce', 'nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied.'], 403);
                return;
            }

            if (!isset($_POST['banner_id']) || !is_numeric($_POST['banner_id'])) {
                wp_send_json_error(['message' => 'Invalid banner ID.']);
                return;
            }

            $banner_id = intval($_POST['banner_id']);
            $post = get_post($banner_id);

            if (!$post || $post->post_type !== 'yab_banner') {
                wp_send_json_error(['message' => 'Banner not found.']);
                return;
            }

            $result = wp_delete_post($banner_id, true);

            if ($result === false) {
                wp_send_json_error(['message' => 'Failed to delete the banner.']);
            } else {
                wp_send_json_success(['message' => 'Banner deleted successfully.']);
            }

            wp_die();
        }
    }
endif;