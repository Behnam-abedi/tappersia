<?php
// tappersia/admin/ajax/class-yab-ajax-api-handler.php

if (!class_exists('Yab_Ajax_Api_Handler')) {
    class Yab_Ajax_Api_Handler {

        private $api_key = '0963b596-1f23-4188-b46c-d7d671028940'; // Store API key securely if possible

        public function register_hooks() {
            // ... (Existing hooks remain the same) ...
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
            add_action('wp_ajax_yab_fetch_hotel_details_by_ids', [$this, 'fetch_hotel_details_by_ids']);
            add_action('wp_ajax_nopriv_yab_fetch_hotel_details_by_ids', [$this, 'fetch_hotel_details_by_ids']);
            add_action('wp_ajax_yab_fetch_airports_from_api', [$this, 'fetch_airports_from_api']);
            add_action('wp_ajax_yab_fetch_welcome_packages', [$this, 'fetch_welcome_packages']);
            add_action('wp_ajax_nopriv_yab_render_welcome_package_ssr', [$this, 'render_welcome_package_ssr']);
            add_action('wp_ajax_yab_render_welcome_package_ssr', [$this, 'render_welcome_package_ssr']);
        }

        public function fetch_welcome_packages() {
             // ... (Keep existing fetch_welcome_packages logic) ...
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied.'], 403);
                return;
            }

            $api_url = 'https://b2bapi.tapexplore.com/api/service-fee/packages';
            $response = wp_remote_get($api_url, ['headers' => ['api-key' => $this->api_key], 'timeout' => 15]);

            if (is_wp_error($response)) {
                wp_send_json_error(['message' => 'Failed to fetch welcome packages from API. ' . $response->get_error_message()], 500);
                return;
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['success']) || $data['success'] !== true || !isset($data['data']) || !is_array($data['data'])) {
                error_log("Tappersia Plugin: Invalid JSON or failed response from Welcome Packages API: " . $body);
                wp_send_json_error(['message' => 'Invalid or failed response from Welcome Packages API.'], 500);
                return;
            }
            wp_send_json_success($data['data']);
            wp_die();
        }

        public function render_welcome_package_ssr() {
            if (empty($_POST['banner_id']) || !is_numeric($_POST['banner_id'])) {
                wp_send_json_error(['message' => 'Invalid Banner ID.'], 400);
                return;
            }

            $banner_id = intval($_POST['banner_id']);
            $banner_post = get_post($banner_id);

            if (!$banner_post || $banner_post->post_type !== 'yab_banner' || $banner_post->post_status !== 'publish') {
                wp_send_json_error(['message' => 'Banner not found or not published.'], 404);
                return;
            }
             $banner_type_meta = get_post_meta($banner_id, '_yab_banner_type', true);
            if ($banner_type_meta !== 'welcome-package-banner') {
                 wp_send_json_error(['message' => 'Invalid banner type for this request.'], 400);
                 return;
            }

            $data = get_post_meta($banner_id, '_yab_banner_data', true);

            if (empty($data['welcome_package']) || empty($data['welcome_package']['selectedKey']) || !isset($data['welcome_package']['html'])) {
                 error_log("Tappersia Plugin SSR Error: Incomplete banner data for ID {$banner_id}.");
                 wp_send_json_error(['message' => 'Banner configuration is incomplete.'], 500);
                 return;
            }

            $api_url = 'https://b2bapi.tapexplore.com/api/service-fee/packages';
            $response = wp_remote_get($api_url, ['headers' => ['api-key' => $this->api_key], 'timeout' => 15]);

            // Use stored values as fallback
            $selected_key = $data['welcome_package']['selectedKey'] ?? '';
            $price_val = $data['welcome_package']['selectedPrice'] ?? 0; // Use numeric value for calculation
            $original_price_val = $data['welcome_package']['selectedOriginalPrice'] ?? 0; // Use numeric value for calculation
            $discount_percentage = 0; // Default discount

            // Try fetching from API
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $body = wp_remote_retrieve_body($response);
                $api_data = json_decode($body, true);

                if (isset($api_data['success']) && $api_data['success'] === true && isset($api_data['data']) && is_array($api_data['data'])) {
                     $found_package = null;
                     foreach ($api_data['data'] as $package) {
                         if (isset($package['key']) && $package['key'] === $selected_key) {
                             $found_package = $package;
                             break;
                         }
                     }

                     if ($found_package) {
                        // ** Update numeric values from API **
                        $price_val = $found_package['moneyValue'] ?? 0;
                        $original_price_val = $found_package['originalMoneyValue'] ?? 0;
                    } else {
                         error_log("Tappersia Plugin SSR Warning: Saved package key '{$selected_key}' not found in current API response for banner ID {$banner_id}. Using saved prices.");
                    }
                } else {
                     error_log("Tappersia Plugin SSR Error: Invalid or failed API response while rendering banner ID {$banner_id}. Using saved prices. Body: " . $body);
                }
            } else {
                $error_message = is_wp_error($response) ? $response->get_error_message() : wp_remote_retrieve_response_message($response);
                error_log("Tappersia Plugin SSR Error: Failed to fetch API for banner ID {$banner_id}. Using saved prices. Error: " . $error_message);
            }

            // ** START: Calculate Discount Percentage **
            if ($original_price_val > 0 && $original_price_val > $price_val) {
                 $discount_percentage = round((($original_price_val - $price_val) / $original_price_val) * 100);
            } else {
                $discount_percentage = 0; // No discount if original price is zero or less than/equal to current price
            }
            // ** END: Calculate Discount Percentage **

            // Format prices for display
            $current_price_formatted = number_format($price_val, 2);
            $current_original_price_formatted = number_format($original_price_val, 2);

            // Replace placeholders
            $html_template = $data['welcome_package']['html'] ?? '';
            $rendered_html = str_replace(
                ['{{price}}', '{{originalPrice}}', '{{selectedKey}}', '{{discountPercentage}}'], // Added {{discountPercentage}}
                [
                    esc_html($current_price_formatted),
                    esc_html($current_original_price_formatted),
                    esc_html($selected_key),
                    esc_html($discount_percentage) // Add calculated percentage
                ],
                $html_template
            );

             $allowed_tags = [ /* ... (Keep your allowed tags array here) ... */
                 'html'   => ['lang' => true, 'dir' => true],
                 'head'   => [],
                 'body'   => ['class' => true, 'id' => true, 'style' => true],
                 'meta'   => ['charset' => true, 'name' => true, 'content' => true, 'http-equiv' => true],
                 'title'  => [],
                 'style'  => ['type' => true],
                 'script' => ['type' => true, 'src' => true, 'async' => true, 'defer' => true],
                 'div'    => ['class' => true, 'id' => true, 'style' => true, 'role' => true, 'aria-label' => true],
                 'article'=> ['class' => true, 'id' => true, 'style' => true],
                 'span'   => ['class' => true, 'id' => true, 'style' => true],
                 'p'      => ['class' => true, 'id' => true, 'style' => true],
                 'a'      => ['href' => true, 'target' => true, 'rel' => true, 'class' => true, 'id' => true, 'style' => true],
                 'img'    => ['src' => true, 'alt' => true, 'width' => true, 'height' => true, 'class' => true, 'id' => true, 'style' => true, 'role' => true, 'aria-label' => true],
                 'h1'     => ['class' => true, 'id' => true, 'style' => true],
                 'h2'     => ['class' => true, 'id' => true, 'style' => true],
                 'h3'     => ['class' => true, 'id' => true, 'style' => true],
                 'h4'     => ['class' => true, 'id' => true, 'style' => true],
                 'h5'     => ['class' => true, 'id' => true, 'style' => true],
                 'h6'     => ['class' => true, 'id' => true, 'style' => true],
                 'ul'     => ['class' => true, 'id' => true, 'style' => true],
                 'ol'     => ['class' => true, 'id' => true, 'style' => true],
                 'li'     => ['class' => true, 'id' => true, 'style' => true],
                 'strong' => [], 'em' => [], 'br' => [],
                 'table'  => ['class' => true, 'id' => true, 'style' => true, 'border' => true, 'cellpadding' => true, 'cellspacing' => true],
                 'thead'  => [], 'tbody'  => [],
                 'tr'     => ['class' => true, 'id' => true, 'style' => true],
                 'th'     => ['class' => true, 'id' => true, 'style' => true, 'scope' => true],
                 'td'     => ['class' => true, 'id' => true, 'style' => true, 'colspan' => true, 'rowspan' => true],
                 'button' => ['class' => true, 'id' => true, 'style' => true, 'type' => true, 'onclick' => true],
             ];
             $final_sanitized_html = wp_kses($rendered_html, $allowed_tags);

             wp_send_json_success(['html' => $final_sanitized_html]);
             wp_die();
        }

        // --- Keep existing methods ---
        // ... (rest of the existing methods) ...
         public function fetch_hotel_details_by_ids() {
            // No nonce check needed if used on frontend potentially
            if (empty($_POST['hotel_ids']) || !is_array($_POST['hotel_ids'])) {
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
             $banner_type_meta = get_post_meta($banner_id, '_yab_banner_type', true);

            // Ensure it's an API banner before proceeding
            if ($banner_type_meta !== 'api-banner' || empty($data) || empty($data['api'])) {
                 wp_send_json_error(['message' => 'Banner data is incomplete or invalid type.'], 500);
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
             usort($cities, fn($a, $b) => strcmp($a['name'] ?? '', $b['name'] ?? ''));

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

        public function fetch_hotels_from_api() {
            check_ajax_referer('yab_nonce', 'nonce');
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
?>