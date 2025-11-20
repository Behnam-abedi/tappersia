<?php
// tappersia/admin/ajax/traits/trait-yab-ajax-tour-handler.php

if (!trait_exists('Yab_Ajax_Tour_Handler')) {
    trait Yab_Ajax_Tour_Handler {

        public function fetch_tour_details_by_ids() {
            if (empty($_POST['tour_ids']) || !is_array($_POST['tour_ids'])) {
                wp_send_json_error(['message' => 'Invalid or empty tour IDs provided.'], 400);
                return;
            }

            $tour_ids = array_map('intval', $_POST['tour_ids']);
            $tour_details = [];


            foreach ($tour_ids as $tour_id) {
                if ($tour_id <= 0) continue;
                $api_url = "https://b2bapi.tapexplore.com/api/b2b/tour/{$tour_id}";
                $response = wp_remote_get($api_url, ['headers' => ['api-key' => $this->api_key], 'timeout' => 15]);

                 if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                     $body = wp_remote_retrieve_body($response);
                     $data = json_decode($body, true);

                    if (($data['success'] ?? false) && isset($data['data'])) {
                        $details = $data['data'];
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
                    } else {
                        error_log("Tappersia Plugin: Invalid API response for tour ID {$tour_id}: " . $body);
                    }
                 } else {
                    $error_message = is_wp_error($response) ? $response->get_error_message() : wp_remote_retrieve_response_message($response);
                    error_log("Tappersia Plugin: Failed to fetch API for tour ID {$tour_id}. Error: " . $error_message);
                 }
                usleep(100000); // 100 milliseconds delay
            }

            if (!empty($tour_details)) {
                wp_send_json_success($tour_details);
            } else {
                wp_send_json_success([]); // Return empty array on failure
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
            $api_url = "https://b2bapi.tapexplore.com/api/b2b/tour/{$tour_id}";
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
                wp_send_json_error(['message' => 'Failed to fetch tour details: ' . $error_message], 500);
            }
            wp_die();
        }

        public function fetch_tours_from_api() {
             check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }

            $base_url = 'https://b2bapi.tapexplore.com/api/b2b/tour/filter';

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

            $api_url = add_query_arg($params, $base_url);

            $response = wp_remote_get($api_url, [
                'headers' => [ 'api-key' => $this->api_key, 'Content-Type' => 'application/json' ],
                'timeout' => 20,
            ]);

            if (is_wp_error($response)) { wp_send_json_error(['message' => 'Failed to fetch tours from API. ' . $response->get_error_message()], 500); return; }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) { wp_send_json_error(['message' => 'Invalid JSON response from tours API.'], 500); return; }

             if (!isset($data['data'])) { $data['data'] = []; }

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

             $cities = $data['data'];
             usort($cities, fn($a, $b) => strcmp($a['name'] ?? '', $b['name'] ?? ''));

            wp_send_json_success($cities);
            wp_die();
        }
    }
}