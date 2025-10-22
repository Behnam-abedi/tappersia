<?php
// tappersia/admin/ajax/class-yab-ajax-api-handler.php

if (!class_exists('Yab_Ajax_Api_Handler')) {
    class Yab_Ajax_Api_Handler {

        public function register_hooks() {
            add_action('wp_ajax_yab_fetch_hotels_from_api', [$this, 'fetch_hotels_from_api']);
            add_action('wp_ajax_yab_fetch_cities_from_api', [$this, 'fetch_cities_from_api']);
            add_action('wp_ajax_yab_fetch_hotel_details_from_api', [$this, 'fetch_hotel_details_from_api']);
            add_action('wp_ajax_yab_fetch_tours_from_api', [$this, 'fetch_tours_from_api']);
            add_action('wp_ajax_yab_fetch_tour_cities_from_api', [$this, 'fetch_tour_cities_from_api']);
            add_action('wp_ajax_yab_fetch_tour_details_from_api', [$this, 'fetch_tour_details_from_api']);
            add_action('wp_ajax_nopriv_yab_fetch_api_banner_html', [$this, 'fetch_api_banner_html']);
            add_action('wp_ajax_yab_fetch_api_banner_html', [$this, 'fetch_api_banner_html']);
            add_action('wp_ajax_yab_fetch_tour_details_by_ids', [$this, 'fetch_tour_details_by_ids']);
            add_action('wp_ajax_nopriv_yab_fetch_tour_details_by_ids', [$this, 'fetch_tour_details_by_ids']);
            add_action('wp_ajax_yab_fetch_hotel_details_by_ids', [$this, 'fetch_hotel_details_by_ids']); // Added hook for hotels by IDs
            add_action('wp_ajax_nopriv_yab_fetch_hotel_details_by_ids', [$this, 'fetch_hotel_details_by_ids']); // Added nopriv hook for hotels by IDs
            add_action('wp_ajax_yab_fetch_airports_from_api', [$this, 'fetch_airports_from_api']);
        }

        // --- New Method: fetch_hotel_details_by_ids ---
        public function fetch_hotel_details_by_ids() {
            // No nonce check needed if used on frontend potentially
            if (empty($_POST['hotel_ids']) || !is_array($_POST['hotel_ids'])) {
                wp_send_json_error(['message' => 'Invalid or empty hotel IDs provided.'], 400);
                return;
            }

            $hotel_ids = array_map('intval', $_POST['hotel_ids']);
            $hotel_details = [];

            foreach ($hotel_ids as $hotel_id) {
                // Reuse the existing private method to fetch details for a single hotel
                $details = $this->fetch_full_hotel_details_data($hotel_id);
                if ($details) {
                    // Make sure essential data for the card exists
                    $hotel_details[] = [
                        'id' => $details['id'] ?? null,
                        'title' => $details['title'] ?? 'N/A',
                        'star' => $details['star'] ?? 0,
                        'province' => $details['province'] ?? ['name' => 'N/A'],
                        'avgRating' => $details['avgRating'] ?? null,
                        'reviewCount' => $details['reviewCount'] ?? 0,
                        'minPrice' => $details['minPrice'] ?? 0,
                        'coverImage' => $details['coverImage'] ?? null,
                        'detailUrl' => $details['detailUrl'] ?? '#'
                    ];
                }
            }

            if (!empty($hotel_details)) {
                wp_send_json_success($hotel_details);
            } else {
                wp_send_json_error(['message' => 'Could not fetch details for the provided hotel IDs.'], 500);
            }

            wp_die();
        }
        // --- End New Method ---

        // ... (other methods remain the same) ...
        public function fetch_airports_from_api() {
             check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied.'], 403);
                return;
            }

            $api_url = 'https://b2bapi.tapexplore.com/api/variable/airports';
            $response = wp_remote_get($api_url, ['timeout' => 20]);

            if (is_wp_error($response)) {
                wp_send_json_error(['message' => 'Failed to fetch airports from API. ' . $response->get_error_message()], 500);
                return;
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['data'])) {
                wp_send_json_error(['message' => 'Invalid JSON response from airports API.'], 500);
                return;
            }

            wp_send_json_success($data['data']);
            wp_die();
        }

        public function fetch_tour_details_by_ids() {
             // No nonce check needed if used on frontend potentially
            if (empty($_POST['tour_ids']) || !is_array($_POST['tour_ids'])) {
                wp_send_json_error(['message' => 'Invalid or empty tour IDs provided.'], 400);
                return;
            }

            $tour_ids = array_map('intval', $_POST['tour_ids']);
            $tour_details = [];

            foreach ($tour_ids as $tour_id) {
                $details = $this->fetch_full_tour_details_data($tour_id);
                if ($details) {
                    // Ensure necessary fields for thumbnails/cards exist
                     $tour_details[] = [
                         'id' => $details['id'] ?? null,
                         'title' => $details['title'] ?? 'N/A',
                         'bannerImage' => $details['bannerImage'] ?? ['url' => ''], // Provide default url
                         'detailUrl' => $details['detailUrl'] ?? '#',
                         'startProvince' => $details['startProvince'] ?? ['name' => 'N/A'],
                         'durationDays' => $details['durationDays'] ?? 0,
                         'rate' => $details['rate'] ?? null,
                         'rateCount' => $details['rateCount'] ?? 0,
                         'price' => $details['price'] ?? 0,
                         'salePrice' => $details['salePrice'] ?? null
                     ];
                }
            }

            if (!empty($tour_details)) {
                wp_send_json_success($tour_details);
            } else {
                wp_send_json_error(['message' => 'Could not fetch details for the provided tour IDs.'], 500);
            }

            wp_die();
        }

         public function fetch_api_banner_html() {
            if (empty($_POST['banner_id']) || !is_numeric($_POST['banner_id'])) {
                wp_send_json_error(['message' => 'Invalid Banner ID.'], 400);
                return;
            }

            $banner_id = intval($_POST['banner_id']);
            $banner_post = get_post($banner_id);

            if (!$banner_post || $banner_post->post_type !== 'yab_banner' || $banner_post->post_status !== 'publish') {
                wp_send_json_error(['message' => 'Banner not found or not active.'], 404);
                return;
            }

            $data = get_post_meta($banner_id, '_yab_banner_data', true);
            if (empty($data) || empty($data['api'])) {
                 wp_send_json_error(['message' => 'Banner data is incomplete.'], 500);
                return;
            }

            require_once YAB_PLUGIN_DIR . 'public/Renderers/class-api-banner-renderer.php';
            $renderer = new Yab_Api_Banner_Renderer($data, $banner_id);
            $html = $renderer->render_live_html();

            wp_send_json_success(['html' => $html]);
            wp_die();
        }

        public function fetch_cities_from_api() {
             check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }

            $api_url = 'https://b2bapi.tapexplore.com/api/b2b/hotel/cities';
            $response = wp_remote_get($api_url, ['timeout' => 15]);

            if (is_wp_error($response)) { wp_send_json_error(['message' => 'Failed to fetch cities from API. ' . $response->get_error_message()], 500); return; }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['data'])) { wp_send_json_error(['message' => 'Invalid JSON response from cities API.'], 500); return; }

            $cities = $data['data'];
            usort($cities, fn($a, $b) => ($a['hotelCount'] ?? 0) <=> ($b['hotelCount'] ?? 0)); // Sort by hotelCount might be better

            wp_send_json_success($cities);
            wp_die();
        }

        public function fetch_hotel_details_from_api() {
             check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }
            if (empty($_POST['hotel_id']) || !is_numeric($_POST['hotel_id'])) {
                wp_send_json_error(['message' => 'Invalid Hotel ID provided.'], 400);
                return;
            }

            $hotel_id = intval($_POST['hotel_id']);
            $hotel_details = $this->fetch_full_hotel_details_data($hotel_id);

            if ($hotel_details) {
                wp_send_json_success($hotel_details);
            } else {
                wp_send_json_error(['message' => 'Failed to fetch hotel details or invalid API response.'], 500);
            }
            wp_die();
        }

        public function fetch_tour_details_from_api() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }
            if (empty($_POST['tour_id']) || !is_numeric($_POST['tour_id'])) {
                wp_send_json_error(['message' => 'Invalid Tour ID provided.'], 400);
                return;
            }

            $tour_id = intval($_POST['tour_id']);
            $tour_details = $this->fetch_full_tour_details_data($tour_id);

            if ($tour_details) {
                wp_send_json_success($tour_details);
            } else {
                 wp_send_json_error(['message' => 'Failed to fetch tour details or invalid API response.'], 500);
            }
            wp_die();
        }

        public function fetch_hotels_from_api() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }

            $api_key = '0963b596-1f23-4188-b46c-d7d671028940';
            $base_url = 'https://b2bapi.tapexplore.com/api/b2b/hotel/filter';

            $params = [];
            if (!empty($_POST['keyword'])) { $params['keyword'] = sanitize_text_field($_POST['keyword']); }
            if (!empty($_POST['page'])) { $params['page'] = intval($_POST['page']); }
            if (!empty($_POST['size'])) { $params['size'] = intval($_POST['size']); }
            if (!empty($_POST['types'])) { $params['types'] = sanitize_text_field($_POST['types']); }
            if (isset($_POST['minPrice']) && is_numeric($_POST['minPrice'])) { $params['minPrice'] = $_POST['minPrice']; }
            if (!empty($_POST['maxPrice'])) { $params['maxPrice'] = $_POST['maxPrice']; }
            if (!empty($_POST['province'])) { $params['province'] = intval($_POST['province']); }
            // Corrected stars param handling
            if (!empty($_POST['stars']) && is_numeric($_POST['stars']) && $_POST['stars'] > 0) {
                 $stars_array = range(1, intval($_POST['stars']));
                 $params['stars'] = implode(',', $stars_array);
            }
            if (!empty($_POST['sort'])) { $params['sort'] = sanitize_text_field($_POST['sort']); }

            $api_url = add_query_arg($params, $base_url);

            $response = wp_remote_get($api_url, [
                'headers' => [ 'api-key' => $api_key, 'Content-Type' => 'application/json' ],
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

         private function fetch_full_hotel_details_data($hotel_id) {
            $api_url = "https://b2bapi.tapexplore.com/api/b2b/hotel/{$hotel_id}";
            $api_key = '0963b596-1f23-4188-b46c-d7d671028940';
            $response = wp_remote_get($api_url, ['headers' => ['api-key' => $api_key], 'timeout' => 15]);
            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) return null;
            $data = json_decode(wp_remote_retrieve_body($response), true);
            // Ensure data structure matches expected format
            return ($data['success'] ?? false) && isset($data['data']) ? $data['data'] : null;
        }


        private function fetch_full_tour_details_data($tour_id) {
             $api_url = "https://b2bapi.tapexplore.com/api/b2b/tour/{$tour_id}";
             $api_key = '0963b596-1f23-4188-b46c-d7d671028940';
             $response = wp_remote_get($api_url, ['headers' => ['api-key' => $api_key], 'timeout' => 15]);
             if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) return null;
             $data = json_decode(wp_remote_retrieve_body($response), true);
            // Ensure data structure matches expected format
            return ($data['success'] ?? false) && isset($data['data']) ? $data['data'] : null;
        }
    }
}
?>