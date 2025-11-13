<?php
// tappersia/admin/ajax/traits/trait-yab-ajax-hotel-handler.php

if (!trait_exists('Yab_Ajax_Hotel_Handler')) {
    trait Yab_Ajax_Hotel_Handler {

        public function fetch_hotel_details_by_ids() {
            // *** ADD THIS: License check ***
            if (empty($this->api_key)) {
                wp_send_json_error(['message' => 'Invalid or missing API license.'], 403);
                return;
            }
            // *** END ADD ***

            // No nonce check needed if used on frontend potentially
            if (empty($_POST['hotel_ids']) || !is_array($_POST['hotel_ids'])) {
            // ... (rest of method)
// ... (rest of fetch_hotel_details_by_ids)
                wp_send_json_error(['message' => 'Invalid or empty hotel IDs provided.'], 400);
                return;
            }

            $hotel_ids = array_map('intval', $_POST['hotel_ids']);
            $hotel_details = [];

            foreach ($hotel_ids as $hotel_id) {
                if ($hotel_id <= 0) continue; // Skip invalid IDs

                $api_url = "https://b2bapi.tapexplore.com/api/b2b/hotel/{$hotel_id}";
                $response = wp_remote_get($api_url, ['headers' => ['api-key' => $this->api_key], 'timeout' => 15]);

                if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                    $body = wp_remote_retrieve_body($response);
                    $data = json_decode($body, true);

                    if (($data['success'] ?? false) && isset($data['data'])) {
                        $details = $data['data'];
                        // Return *all* required fields for the new card
                        $hotel_details[] = [
                            'id' => $details['id'] ?? null,
                            'title' => $details['title'] ?? 'N/A',
                            'star' => $details['star'] ?? 0,
                            'province' => $details['province'] ?? ['id' => null, 'name' => 'N/A'], // Ensure province is an object
                            'avgRating' => isset($details['avgRating']) ? (float)$details['avgRating'] : null, // Cast to float or null
                            'reviewCount' => $details['reviewCount'] ?? 0,
                            'minPrice' => isset($details['minPrice']) ? (float)$details['minPrice'] : 0, // Cast to float
                            'discount' => isset($details['discount']) ? (float)$details['discount'] : 0, // Add discount field, cast to float
                            'coverImage' => $details['coverImage'] ?? null,
                            'detailUrl' => $details['detailUrl'] ?? '#',
                            'isFeatured' => $details['isFeatured'] ?? false, // Add isFeatured field
                            'customTags' => $details['customTags'] ?? [], // Add customTags field, default to empty array
                        ];
                    } else {
                        error_log("Tappersia Plugin: Invalid API response for hotel ID {$hotel_id}: " . $body);
                    }
                } else {
                    $error_message = is_wp_error($response) ? $response->get_error_message() : wp_remote_retrieve_response_message($response);
                    error_log("Tappersia Plugin: Failed to fetch API for hotel ID {$hotel_id}. Error: " . $error_message);
                }
                 // Add a small delay between requests to avoid potential rate limiting
                 usleep(100000); // 100 milliseconds
            }

            if (!empty($hotel_details)) {
                wp_send_json_success($hotel_details);
            } else {
                 wp_send_json_success([]);
            }

            wp_die();
        }

        public function fetch_cities_from_api() {
            // *** ADD THIS: License check ***
            if (empty($this->api_key)) {
                wp_send_json_error(['message' => 'Invalid or missing API license.'], 403);
                return;
            }
            // *** END ADD ***

            check_ajax_referer('yab_nonce', 'nonce');
            // ... (rest of method)
// ... (rest of fetch_cities_from_api)
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }

            $api_url = 'https://b2bapi.tapexplore.com/api/b2b/hotel/cities';
            $response = wp_remote_get($api_url, ['timeout' => 15]);

            if (is_wp_error($response)) { wp_send_json_error(['message' => 'Failed to fetch cities from API. ' . $response->get_error_message()], 500); return; }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['data'])) { wp_send_json_error(['message' => 'Invalid JSON response from cities API.'], 500); return; }

            $cities = $data['data'];
             usort($cities, fn($a, $b) => strcmp($a['name'] ?? '', $b['name'] ?? ''));

            wp_send_json_success($cities);
            wp_die();
        }

         public function fetch_hotel_details_from_api() {
            // *** ADD THIS: License check ***
            if (empty($this->api_key)) {
                wp_send_json_error(['message' => 'Invalid or missing API license.'], 403);
                return;
            }
            // *** END ADD ***

            check_ajax_referer('yab_nonce', 'nonce');
            // ... (rest of method)
// ... (rest of fetch_hotel_details_from_api)
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }
            if (empty($_POST['hotel_id']) || !is_numeric($_POST['hotel_id'])) {
                wp_send_json_error(['message' => 'Invalid Hotel ID provided.'], 400);
                return;
            }

            $hotel_id = intval($_POST['hotel_id']);
             $api_url = "https://b2bapi.tapexplore.com/api/b2b/hotel/{$hotel_id}";
             $response = wp_remote_get($api_url, ['headers' => ['api-key' => $this->api_key], 'timeout' => 15]);

             if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                 $body = wp_remote_retrieve_body($response);
                 $data = json_decode($body, true);
                 if (($data['success'] ?? false) && isset($data['data'])) {
                     wp_send_json_success($data['data']);
                 } else {
                     wp_send_json_error(['message' => 'Invalid API response structure.'], 500);
                 }
             } else {
                 $error_message = is_wp_error($response) ? $response->get_error_message() : wp_remote_retrieve_response_message($response);
                 wp_send_json_error(['message' => 'Failed to fetch hotel details: ' . $error_message], 500);
             }
            wp_die();
        }

        public function fetch_hotels_from_api() {
            // *** ADD THIS: License check ***
            if (empty($this->api_key)) {
                wp_send_json_error(['message' => 'Invalid or missing API license.'], 403);
                return;
            }
            // *** END ADD ***
            
            check_ajax_referer('yab_nonce', 'nonce');
            // ... (rest of method)
// ... (rest of fetch_hotels_from_api)
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }

            $base_url = 'https://b2bapi.tapexplore.com/api/b2b/hotel/filter';

            $params = [];
            if (!empty($_POST['keyword'])) { $params['keyword'] = sanitize_text_field($_POST['keyword']); }
            if (!empty($_POST['page'])) { $params['page'] = intval($_POST['page']); }
            if (!empty($_POST['size'])) { $params['size'] = intval($_POST['size']); }
            if (!empty($_POST['types'])) { $params['types'] = sanitize_text_field($_POST['types']); }
            if (isset($_POST['minPrice']) && is_numeric($_POST['minPrice'])) { $params['minPrice'] = $_POST['minPrice']; }
             if (!empty($_POST['maxPrice']) && is_numeric($_POST['maxPrice']) && $_POST['maxPrice'] > 0 && $_POST['maxPrice'] >= ($params['minPrice'] ?? 0)) {
                 $params['maxPrice'] = $_POST['maxPrice'];
             }
            if (!empty($_POST['province'])) { $params['province'] = intval($_POST['province']); }
            if (!empty($_POST['stars'])) {
                 $stars_input = $_POST['stars'];
                 $valid_stars = array_filter(explode(',', $stars_input), function($s) { return is_numeric($s) && $s >= 1 && $s <= 5; });
                 if (!empty($valid_stars)) {
                     $params['stars'] = implode(',', $valid_stars);
                 }
            }
             if (!empty($_POST['sort']) && in_array($_POST['sort'], ['rate', 'price_low_high', 'price_high_low', 'star_high_low'])) {
                 $params['sort'] = sanitize_text_field($_POST['sort']);
             }

            $api_url = add_query_arg($params, $base_url);

            $response = wp_remote_get($api_url, [
                'headers' => [ 'api-key' => $this->api_key, 'Content-Type' => 'application/json' ],
                'timeout' => 20,
            ]);

            if (is_wp_error($response)) { wp_send_json_error(['message' => 'Failed to fetch data from API. ' . $response->get_error_message()], 500); return; }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) { wp_send_json_error(['message' => 'Invalid JSON response from API.'], 500); return; }

             if (!isset($data['data'])) { $data['data'] = []; }

            wp_send_json_success($data);
            wp_die();
        }
    }
}