<?php
if (!class_exists('Yab_Ajax_Handler')) :
    class Yab_Ajax_Handler {

        private function get_banner_type_handler($banner_type_slug) {
            switch ($banner_type_slug) {
                case 'double-banner':
                    require_once YAB_PLUGIN_DIR . 'includes/BannerTypes/DoubleBanner/DoubleBanner.php';
                    return new Yab_Double_Banner();
                case 'single-banner':
                    require_once YAB_PLUGIN_DIR . 'includes/BannerTypes/SingleBanner/SingleBanner.php';
                    return new Yab_Single_Banner();
                case 'api-banner':
                    require_once YAB_PLUGIN_DIR . 'includes/BannerTypes/ApiBanner/ApiBanner.php';
                    return new Yab_Api_Banner();
                case 'simple-banner':
                    require_once YAB_PLUGIN_DIR . 'includes/BannerTypes/SimpleBanner/SimpleBanner.php';
                    return new Yab_Simple_Banner();
                case 'sticky-simple-banner':
                    require_once YAB_PLUGIN_DIR . 'includes/BannerTypes/StickySimpleBanner/StickySimpleBanner.php';
                    return new Yab_Sticky_Simple_Banner();
                default:
                    return null;
            }
        }
        
        public function save_banner() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }
            if (!isset($_POST['banner_data']) || !isset($_POST['banner_type'])) { wp_send_json_error(['message' => 'Incomplete data received.']); return; }
            $banner_data = json_decode(stripslashes($_POST['banner_data']), true);
            $banner_type = sanitize_text_field($_POST['banner_type']);
            $handler = $this->get_banner_type_handler($banner_type);
            if (!$handler) { wp_send_json_error(['message' => 'Invalid banner type specified.']); return; }
            $handler->save($banner_data);
        }

        public function search_content() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }
            $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
            $content_type = isset($_POST['content_type']) ? sanitize_text_field($_POST['content_type']) : 'posts';
            $results = [];
            switch ($content_type) {
                case 'posts':
                    $query = new WP_Query(['post_type' => 'post', 'posts_per_page' => 50, 's' => $search_term, 'post_status' => 'publish']);
                    foreach ($query->posts as $post) { $results[] = ['ID' => $post->ID, 'post_title' => $post->post_title]; }
                    break;
                case 'pages':
                    $query = new WP_Query(['post_type' => 'page', 'posts_per_page' => 50, 's' => $search_term, 'post_status' => 'publish']);
                    foreach ($query->posts as $page) { $results[] = ['ID' => $page->ID, 'post_title' => $page->post_title]; }
                    break;
                case 'categories':
                    $terms = get_terms(['taxonomy' => 'category', 'name__like' => $search_term, 'hide_empty' => false, 'number' => 50]);
                    foreach ($terms as $term) { $results[] = ['term_id' => $term->term_id, 'name' => $term->name]; }
                    break;
            }
            wp_send_json_success($results);
            wp_die();
        }

        public function yab_fetch_cities_from_api() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }

            $api_url = 'https://b2bapi.tapexplore.com/api/b2b/hotel/cities';
            $response = wp_remote_get($api_url, ['timeout' => 15]);

            if (is_wp_error($response)) { wp_send_json_error(['message' => 'Failed to fetch cities from API. ' . $response->get_error_message()], 500); return; }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['data'])) { wp_send_json_error(['message' => 'Invalid JSON response from cities API.'], 500); return; }

            $cities = $data['data'];
            usort($cities, fn($a, $b) => $a['id'] <=> $b['id']);

            wp_send_json_success($cities);
            wp_die();
        }
        
        public function yab_fetch_hotel_details_from_api() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }
            if (empty($_POST['hotel_id']) || !is_numeric($_POST['hotel_id'])) {
                wp_send_json_error(['message' => 'Invalid Hotel ID provided.'], 400);
                return;
            }
            
            $hotel_id = intval($_POST['hotel_id']);
            $api_url = "https://b2bapi.tapexplore.com/api/b2b/hotel/{$hotel_id}";
             $api_key = '0963b596-1f23-4188-b46c-d7d671028940';

            $response = wp_remote_get($api_url, [
                'headers' => [ 'api-key' => $api_key ],
                'timeout' => 15
            ]);

            if (is_wp_error($response)) {
                wp_send_json_error(['message' => 'Failed to fetch hotel details. ' . $response->get_error_message()], 500);
                return;
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['success']) || $data['success'] !== true) {
                wp_send_json_error(['message' => 'Invalid API response or failed request.'], 500);
                return;
            }

            wp_send_json_success($data['data']);
            wp_die();
        }
        
        public function yab_fetch_tour_details_from_api() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }
            if (empty($_POST['tour_id']) || !is_numeric($_POST['tour_id'])) {
                wp_send_json_error(['message' => 'Invalid Tour ID provided.'], 400);
                return;
            }
        
            $tour_id = intval($_POST['tour_id']);
            $api_url = "https://b2bapi.tapexplore.com/api/b2b/tour/{$tour_id}";
            $api_key = '0963b596-1f23-4188-b46c-d7d671028940';
        
            $response = wp_remote_get($api_url, [
                'headers' => [ 'api-key' => $api_key ],
                'timeout' => 15
            ]);
        
            if (is_wp_error($response)) {
                wp_send_json_error(['message' => 'Failed to fetch tour details. ' . $response->get_error_message()], 500);
                return;
            }
        
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
        
            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['success']) || $data['success'] !== true) {
                wp_send_json_error(['message' => 'Invalid API response or failed request for tour details.'], 500);
                return;
            }
        
            wp_send_json_success($data['data']);
            wp_die();
        }

        public function fetch_hotels_from_api() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }

            $api_key = '0963b596-1f23-4188-b46c-d7d671028940';
            $base_url = 'https://b2bapi.tapexplore.com/api/b2b/hotel/filter';
            
            $params = [];
            if (!empty($_POST['keyword'])) {
                $params['keyword'] = sanitize_text_field($_POST['keyword']);
            }
            if (!empty($_POST['page'])) {
                $params['page'] = intval($_POST['page']);
            }
            if (!empty($_POST['size'])) {
                $params['size'] = intval($_POST['size']);
            }
            if (!empty($_POST['types'])) {
                $params['types'] = sanitize_text_field($_POST['types']);
            }
            if (isset($_POST['minPrice']) && is_numeric($_POST['minPrice'])) {
                $params['minPrice'] = $_POST['minPrice'];
            }
            if (!empty($_POST['maxPrice'])) {
                $params['maxPrice'] = $_POST['maxPrice'];
            }
            if (!empty($_POST['province'])) {
                $params['province'] = intval($_POST['province']);
            }
            if (!empty($_POST['stars'])) {
                $params['stars'] = sanitize_text_field($_POST['stars']);
            }
            
            $api_url = add_query_arg($params, $base_url);

            $response = wp_remote_get($api_url, [
                'headers' => [
                    'api-key' => $api_key,
                    'Content-Type' => 'application/json'
                ],
                'timeout' => 20,
            ]);

            if (is_wp_error($response)) { wp_send_json_error(['message' => 'Failed to fetch data from API. ' . $response->get_error_message()], 500); return; }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) { wp_send_json_error(['message' => 'Invalid JSON response from API.'], 500); return; }

            wp_send_json_success($data);
            wp_die();
        }

        public function fetch_tours_from_api() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }
        
            $api_key = '0963b596-1f23-4188-b46c-d7d671028940';
            $base_url = 'https://b2bapi.tapexplore.com/api/b2b/tour/filter';
            
            $params = [];
            if (!empty($_POST['keyword'])) { $params['keyword'] = sanitize_text_field($_POST['keyword']); }
            if (!empty($_POST['page'])) { $params['page'] = intval($_POST['page']); }
            if (!empty($_POST['size'])) { $params['size'] = intval($_POST['size']); }
            if (!empty($_POST['types'])) { $params['types'] = sanitize_text_field($_POST['types']); }
            if (isset($_POST['minPrice']) && is_numeric($_POST['minPrice'])) { $params['minPrice'] = $_POST['minPrice']; }
            if (!empty($_POST['maxPrice'])) { $params['maxPrice'] = $_POST['maxPrice']; }
            if (!empty($_POST['province'])) { $params['province'] = intval($_POST['province']); }
            
            $api_url = add_query_arg($params, $base_url);
        
            $response = wp_remote_get($api_url, [
                'headers' => [ 'api-key' => $api_key, 'Content-Type' => 'application/json' ],
                'timeout' => 20,
            ]);
        
            if (is_wp_error($response)) { wp_send_json_error(['message' => 'Failed to fetch tours from API. ' . $response->get_error_message()], 500); return; }
        
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
        
            if (json_last_error() !== JSON_ERROR_NONE) { wp_send_json_error(['message' => 'Invalid JSON response from tours API.'], 500); return; }
        
            wp_send_json_success($data);
            wp_die();
        }
        
        public function fetch_tour_cities_from_api() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }
        
            $api_url = 'https://b2bapi.tapexplore.com/api/b2b/tour/cities';
            $response = wp_remote_get($api_url, ['timeout' => 15]);
        
            if (is_wp_error($response)) { wp_send_json_error(['message' => 'Failed to fetch tour cities. ' . $response->get_error_message()], 500); return; }
        
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
        
            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['data'])) { wp_send_json_error(['message' => 'Invalid JSON response from tour cities API.'], 500); return; }
        
            wp_send_json_success($data['data']);
            wp_die();
        }

        public function delete_banner() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }
            if (!isset($_POST['banner_id']) || !is_numeric($_POST['banner_id'])) { wp_send_json_error(['message' => 'Invalid banner ID.']); return; }
            $banner_id = intval($_POST['banner_id']);
            $post = get_post($banner_id);
            if (!$post || $post->post_type !== 'yab_banner') { wp_send_json_error(['message' => 'Banner not found.']); return; }
            $result = wp_delete_post($banner_id, true);
            if ($result === false) { wp_send_json_error(['message' => 'Failed to delete the banner.']); } 
            else { wp_send_json_success(['message' => 'Banner deleted successfully.']); }
            wp_die();
        }
    }
endif;