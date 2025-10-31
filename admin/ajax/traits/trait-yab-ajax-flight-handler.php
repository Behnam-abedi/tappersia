<?php
// tappersia/admin/ajax/traits/trait-yab-ajax-flight-handler.php

if (!trait_exists('Yab_Ajax_Flight_Handler')) {
    trait Yab_Ajax_Flight_Handler {

        /**
         * Fetches flight data and returns the cheapest price and flight.
         * (Private helper method)
         *
         * @param string $from_code
         * @param string $from_country
         * @param string $to_code
         * @param string $to_country
         * @return array ['status' => 'success'|'error', 'data' => mixed]
         */
        private function _fetch_cheapest_flight($from_code, $from_country, $to_code, $to_country) {
            try {
                // Use UTC for consistency in API requests
                $tomorrow = new DateTime('now', new DateTimeZone('UTC'));
                $tomorrow->add(new DateInterval('P1D'));
                $departure_date_string = $tomorrow->format('Y-m-d');
            } catch (Exception $e) {
                return ['status' => 'error', 'data' => ['message' => 'Failed to calculate departure date: ' . $e->getMessage()]];
            }

            $api_url = 'https://b2bapi.tapexplore.com/api/booking/flight/search';
            $api_body = [
                'departureDateString' => $departure_date_string,
                'fromAirportCode' => $from_code,
                'fromCountryName' => $from_country,
                'toAirportCode' => $to_code,
                'toCountryName' => $to_country,
                'adult' => 1, 'child' => 0, 'infant' => 0
            ];

            $args = [
                'method' => 'POST',
                'headers' => ['api-key' => $this->api_key, 'Content-Type' => 'application/json'],
                'body' => json_encode($api_body),
                'timeout' => 20,
            ];

            $response = wp_remote_post($api_url, $args);

            if (is_wp_error($response)) {
                return ['status' => 'error', 'data' => ['message' => 'Failed to fetch flight data from API. ' . $response->get_error_message()]];
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['success']) || $data['success'] !== true || !isset($data['data']['data'])) {
                error_log("Tappersia Plugin: Invalid JSON or failed response from Flight Search API: " . $body);
                $api_message = $data['message'] ?? 'Invalid or failed response from Flight Search API.';
                return ['status' => 'error', 'data' => ['message' => $api_message, 'raw_response' => $body]];
            }
            
            $flights = $data['data']['data'];

            if (empty($flights) || !is_array($flights)) {
                return ['status' => 'success', 'data' => ['cheapestPrice' => null, 'cheapestFlight' => null, 'message' => 'No flights found.', 'allFlights' => []]];
            }

            $cheapest_price = null;
            $cheapest_flight = null;

            foreach ($flights as $flight) {
                if (isset($flight['pricing']['internationalPrice'])) {
                    $current_price = (float) $flight['pricing']['internationalPrice'];
                    if ($cheapest_price === null || $current_price < $cheapest_price) {
                        $cheapest_price = $current_price;
                        $cheapest_flight = $flight;
                    }
                }
            }

            return ['status' => 'success', 'data' => [
                'cheapestPrice' => $cheapest_price,
                'cheapestFlight' => $cheapest_flight,
                'totalFlights' => count($flights),
                'allFlights' => $flights
            ]];
        }
        
        // --- START: Added Helper functions (copied from Abstract Renderer) ---
        private function _get_background_style_for_flight(array $b): string {
            if (($b['backgroundType'] ?? 'solid') === 'gradient') {
                if (empty($b['gradientStops']) || !is_array($b['gradientStops'])) {
                    return "background: transparent;";
                }
                usort($b['gradientStops'], function($a, $b) {
                    return ($a['stop'] ?? 0) <=> ($b['stop'] ?? 0);
                });
                $stops_css = [];
                foreach ($b['gradientStops'] as $stop) {
                    $color = isset($stop['color']) ? trim($stop['color']) : 'transparent';
                    $sanitized_color = (strtolower($color) === 'transparent') ? 'transparent' : esc_attr($color);
                    $position = isset($stop['stop']) ? intval($stop['stop']) : 0;
                    $stops_css[] = $sanitized_color . ' ' . esc_attr($position) . '%';
                }
                if (empty($stops_css)) return "background: transparent;";
                $angle = isset($b['gradientAngle']) ? intval($b['gradientAngle']) . 'deg' : '90deg';
                return "background: linear-gradient({$angle}, " . implode(', ', $stops_css) . ");";
            }
            return "background-color: " . esc_attr($b['bgColor'] ?? '#ffffff') . ";";
        }

        private function _get_image_style_for_flight(array $b): string {
            $right = isset($b['imagePosRight']) && $b['imagePosRight'] !== null ? intval($b['imagePosRight']) . 'px' : '0';
            $bottom = isset($b['imagePosBottom']) && $b['imagePosBottom'] !== null ? intval($b['imagePosBottom']) . 'px' : '0';
            $style = "position: absolute; object-fit: cover; right: {$right}; bottom: {$bottom};";
            if (!empty($b['enableCustomImageSize'])) {
                $width_unit = isset($b['imageWidthUnit']) && in_array($b['imageWidthUnit'], ['px', '%']) ? $b['imageWidthUnit'] : 'px';
                $height_unit = isset($b['imageHeightUnit']) && in_array($b['imageHeightUnit'], ['px', '%']) ? $b['imageHeightUnit'] : 'px';
                $width = isset($b['imageWidth']) && $b['imageWidth'] !== null && $b['imageWidth'] !== '' ? intval($b['imageWidth']) . $width_unit : 'auto';
                $height = isset($b['imageHeight']) && $b['imageHeight'] !== null && $b['imageHeight'] !== '' ? intval($b['imageHeight']) . $height_unit : '100%';
                $style .= "width: {$width}; height: {$height};";
            } else {
                $style .= 'width: auto; height: 100%;'; // Default behavior from single-banner
            }
            return $style;
        }
        // --- END: Added Helper functions ---

        public function fetch_flight_search() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied.'], 403);
                return;
            }

            $required_fields = ['fromAirportCode', 'fromCountryName', 'toAirportCode', 'toCountryName'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    wp_send_json_error(['message' => 'Missing required field: ' . $field], 400);
                    return;
                }
            }

            $result = $this->_fetch_cheapest_flight(
                sanitize_text_field($_POST['fromAirportCode']),
                sanitize_text_field($_POST['fromCountryName']),
                sanitize_text_field($_POST['toAirportCode']),
                sanitize_text_field($_POST['toCountryName'])
            );

            if ($result['status'] === 'success') {
                wp_send_json_success($result['data']);
            } else {
                wp_send_json_error($result['data'], 500);
            }
            wp_die();
        }

        public function render_flight_ticket_ssr() {
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
            if ($banner_type_meta !== 'flight-ticket') {
                 wp_send_json_error(['message' => 'Invalid banner type for this request.'], 400);
                 return;
            }

            $data = get_post_meta($banner_id, '_yab_banner_data', true);
            
            // --- START: Load Design Settings ---
            $design = $data['flight_ticket']['design'] ?? []; // Get design settings
            
            // Set defaults if not present (for banners saved before this update)
            $defaults = [
                'minHeight' => 150, 'borderRadius' => 16, 'padding' => 12,
                'layerOrder' => 'overlay-below-image', 'backgroundType' => 'solid', 'bgColor' => '#CEE8F6',
                'imageUrl' => 'https://www.textmagic.com/wp-content/uploads/2022/05/Nestle-Building.png',
                'content1' => ['text' => 'Offering', 'color' => '#555555', 'fontSize' => 12, 'fontWeight' => '400'],
                'content2' => ['text' => 'BEST DEALS', 'color' => '#111111', 'fontSize' => 18, 'fontWeight' => '700'],
                'content3' => ['text' => 'on Iran Domestic Flight Booking', 'color' => '#333333', 'fontSize' => 14, 'fontWeight' => '400'],
                'price' => ['color' => '#00BAA4', 'fontSize' => 17, 'fontWeight' => '700'],
                'button' => ['bgColor' => '#1EC2AF', 'color' => '#FFFFFF', 'fontSize' => 13, 'fontWeight' => '600'],
                'fromCity' => ['color' => '#000000', 'fontSize' => 16, 'fontWeight' => '700'],
                'toCity' => ['color' => '#000000', 'fontSize' => 16, 'fontWeight' => '700'],
            ];
            // Use array_merge_recursive to fill in missing keys without overwriting existing ones
            $design = array_replace_recursive($defaults, $design);
            // --- END: Load Design Settings ---
            
            if (empty($data['flight_ticket']) || empty($data['flight_ticket']['from']) || empty($data['flight_ticket']['to'])) {
                 error_log("Tappersia Plugin SSR Error: Incomplete flight ticket data for ID {$banner_id}.");
                 wp_send_json_error(['message' => 'Banner configuration is incomplete.'], 500);
                 return;
            }

            $from = $data['flight_ticket']['from'];
            $to = $data['flight_ticket']['to'];

            $flight_result = $this->_fetch_cheapest_flight(
                $from['iataCode'], $from['countryName'], $to['iataCode'], $to['countryName']
            );

            $cheapest_price_formatted = "N/A";
            if ($flight_result['status'] === 'success' && $flight_result['data']['cheapestPrice'] !== null) {
                $cheapest_price_formatted = '€' . number_format($flight_result['data']['cheapestPrice'], 2);
            }

            try {
                $tomorrow = new DateTime('now', new DateTimeZone('UTC'));
                $tomorrow->add(new DateInterval('P1D'));
                $departure_date = $tomorrow->format('Y-m-d');
            } catch (Exception $e) {
                $departure_date = date('Y-m-d', strtotime('+1 day')); // Fallback
            }

            $from_city_path = strtolower(str_replace(' ', '-', $from['city']));
            $to_city_path = strtolower(str_replace(' ', '-', $to['city']));

            $booking_url = add_query_arg([
                'fromCountryName' => $from['countryName'],
                'fromCityName' => $from['city'],
                'fromAirportCode' => $from['iataCode'],
                'toCountryName' => $to['countryName'],
                'toCityName' => $to['city'],
                'toAirportCode' => $to['iataCode'],
                'departureDate' => $departure_date,
                'pageNumber' => 1,
                'pageSize' => 10,
                'sort' => 'earliest_time'
            ], "https://www.tappersia.com/iran-flights/{$from_city_path}/{$to_city_path}");

            $unique_id = "yab-ft-ticket-" . $banner_id;
            
            $origin_city = esc_html($from['city']);
            $dest_city = esc_html($to['city']);
            $price_display = esc_html($cheapest_price_formatted);
            $url_display = esc_url($booking_url);
            
            // --- START: Apply Dynamic Styles ---
            $promo_banner_style = sprintf(
                'min-height: %spx; border-radius: %spx; padding: %spx;',
                esc_attr($design['minHeight']),
                esc_attr($design['borderRadius']),
                esc_attr($design['padding'])
            );
            
            $bg_z_index = ($design['layerOrder'] === 'overlay-below-image') ? 1 : 2;
            $img_z_index = ($design['layerOrder'] === 'overlay-below-image') ? 2 : 1;
            
            $background_style = $this->_get_background_style_for_flight($design) . ' z-index: ' . $bg_z_index . ';';
            
            $image_html = '';
            if (!empty($design['imageUrl'])) {
                $image_style = $this->_get_image_style_for_flight($design) . ' z-index: ' . $img_z_index . ';';
                $image_html = sprintf(
                    '<div class="promo-banner__image-wrapper" style="z-index: %s;"><img src="%s" alt="" style="%s"></div>',
                    esc_attr($img_z_index),
                    esc_url($design['imageUrl']),
                    esc_attr($image_style)
                );
            }
            
            $content1_style = sprintf('color: %s; font-size: %spx; font-weight: %s;', esc_attr($design['content1']['color']), esc_attr($design['content1']['fontSize']), esc_attr($design['content1']['fontWeight']));
            $content2_style = sprintf('color: %s; font-size: %spx; font-weight: %s;', esc_attr($design['content2']['color']), esc_attr($design['content2']['fontSize']), esc_attr($design['content2']['fontWeight']));
            $content3_style = sprintf('color: %s; font-size: %spx; font-weight: %s;', esc_attr($design['content3']['color']), esc_attr($design['content3']['fontSize']), esc_attr($design['content3']['fontWeight']));
            
            $price_style = sprintf('color: %s; font-size: %spx; font-weight: %s;', esc_attr($design['price']['color']), esc_attr($design['price']['fontSize']), esc_attr($design['price']['fontWeight']));
            $button_style = sprintf('background-color: %s;', esc_attr($design['button']['bgColor']));
            $button_text_style = sprintf('color: %s; font-size: %spx; font-weight: %s;', esc_attr($design['button']['color']), esc_attr($design['button']['fontSize']), esc_attr($design['button']['fontWeight']));
            
            $from_city_style = sprintf('color: %s; font-size: %spx; font-weight: %s;', esc_attr($design['fromCity']['color']), esc_attr($design['fromCity']['fontSize']), esc_attr($design['fromCity']['fontWeight']));
            $to_city_style = sprintf('color: %s; font-size: %spx; font-weight: %s;', esc_attr($design['toCity']['color']), esc_attr($design['toCity']['fontSize']), esc_attr($design['toCity']['fontWeight']));
            // --- END: Apply Dynamic Styles ---

            // --- START: Updated HTML block ---
            $html = <<<HTML
            <div class="promo-banner" style="{$promo_banner_style}">
                <div class="promo-banner__background" style="{$background_style}"></div>
                {$image_html}
                <div class="promo-banner__content">
                    <span class="promo-banner__content_1" style="{$content1_style}">{$design['content1']['text']}</span>
                    <span class="promo-banner__content_2" style="{$content2_style}">{$design['content2']['text']}</span>
                    <span class="promo-banner__content_3" style="{$content3_style}">{$design['content3']['text']}</span>
                </div>
                <div class="ticket" id="{$unique_id}">
                    <div id="cutter-top-{$banner_id}" class="ticket__cutter ticket__cutter--top"></div>
                    <div id="cutter-bottom-{$banner_id}" class="ticket__cutter ticket__cutter--bottom"></div>
                    <div class="ticket__section ticket__section--actions">
                        <div class="ticket__price">
                            <div class="ticket__price-icon">
                                <svg width="19" height="15" viewBox="0 0 19 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.60641 0.0468752C3.37203 0.171875 1.78609 1.40625 1.73922 1.49219C1.68453 1.625 1.68453 1.75 1.74703 1.86719C1.78609 1.92969 2.60641 2.52344 4.29391 3.69531C5.66891 4.64062 6.82516 5.44531 6.85641 5.47656C6.91891 5.52344 6.82516 5.58594 5.68453 6.22656C4.63766 6.8125 4.42672 6.92969 4.30172 6.92969C4.16891 6.92969 4.03609 6.85156 3.05953 6.22656C2.45797 5.84375 1.92672 5.50781 1.87984 5.47656C1.70016 5.38281 1.60641 5.42969 0.856406 5.91406C0.239219 6.3125 0.106406 6.41406 0.051719 6.52344C-0.0810935 6.78906 -0.0654685 6.80469 1.45016 8.28906C2.95797 9.75781 2.98922 9.78906 3.56734 9.9375C4.34078 10.1328 5.51266 10.0312 6.66891 9.66406C7.19234 9.5 8.62984 8.94531 9.17672 8.69531C10.5127 8.09375 14.4736 5.82031 15.9267 4.82031C17.622 3.64844 18.083 3.25 18.333 2.70312C18.6455 2.02344 18.2002 1.42188 17.2861 1.28125C16.0986 1.09375 13.6455 1.72656 11.997 2.64062L11.458 2.94531L7.66891 1.47656C5.57516 0.664063 3.83297 0 3.77828 0C3.73141 0 3.65328 0.0234377 3.60641 0.0468752ZM7.18453 2.1875C9.00484 2.89844 10.497 3.48437 10.497 3.5C10.497 3.52344 7.84078 5.02344 7.77047 5.04688C7.72359 5.0625 2.87203 1.71094 2.87203 1.66406C2.87203 1.63281 3.81734 0.90625 3.86422 0.90625C3.87203 0.898438 5.36422 1.48438 7.18453 2.1875ZM17.2783 2.13281C17.5283 2.1875 17.622 2.26562 17.5908 2.39844C17.5205 2.67187 16.5595 3.42969 14.7392 4.65625C14.1298 5.0625 10.4814 7.13281 9.43453 7.66406C8.54391 8.11719 6.83297 8.78906 6.08297 8.98437C4.95016 9.27344 3.90328 9.28906 3.43453 9.01562C3.31734 8.94531 1.12203 6.82031 1.12203 6.78125C1.12203 6.73437 1.70797 6.38281 1.75484 6.40625C1.77828 6.41406 2.23141 6.70312 2.75484 7.03906C3.27828 7.375 3.77047 7.67969 3.84859 7.71094C4.04391 7.79687 4.45797 7.8125 4.65328 7.75C4.74703 7.71875 6.58297 6.69531 8.74703 5.47656C10.9111 4.25 12.8017 3.1875 12.9502 3.11719C14.208 2.47656 16.4892 1.96094 17.2783 2.13281Z" fill="#777777"/></svg>
                            </div>
                            <div class="ticket__price-label"><span>From</span></div>
                            <div class="ticket__price-amount"><span style="{$price_style}">{$price_display}</span></div>
                        </div>
                        <div class="ticket__button" style="{$button_style}">
                            <a href="{$url_display}" target="_blank" style="text-decoration: none;"><span class="ticket__button-text" style="{$button_text_style}">Book Now</span></a>
                        </div>
                    </div>
                    <div class="ticket__section ticket__section--details">
                        <div class="ticket__city">
                            <span class="ticket__city-name ticket-from-country" style="{$from_city_style}">{$origin_city}</span>
                            <div class="ticket__city-dot ticket__city-dot--origin"></div>
                        </div>
                        <div class="ticket__flight-path">
                            <div class="ticket__flight-arrow ticket__flight-arrow--top"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="20" viewBox="0 0 7 20" fill="none"><path d="M0.99 1.5L6.01 6.51" stroke="#999999" stroke-width="1.5" stroke-linecap="round"/><path d="M0.99 18.5V1.5" stroke="#999999" stroke-width="1.5" stroke-linecap="round"/></svg></div>
                            <div class="ticket__flight-arrow ticket__flight-arrow--bottom"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="20" viewBox="0 0 7 20" fill="none"><path d="M0.99 1.5L6.01 6.51" stroke="#999999" stroke-width="1.5" stroke-linecap="round"/><path d="M0.99 18.5V1.5" stroke="#999999" stroke-width="1.5" stroke-linecap="round"/></svg></div>
                        </div>
                        <div class="ticket__city">
                            <span class="ticket__city-name ticket-to-country" style="{$to_city_style}">{$dest_city}</span>
                            <div class="ticket__city-dot ticket__city-dot--destination"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
            (function() {
                var container = document.getElementById('{$unique_id}');
                if (!container) return;
                
                var updateClipPath = function() {
                    try {
                        var cutters = container.querySelectorAll('.ticket__cutter');
                        if (!cutters || cutters.length === 0) return;
                        
                        var pathString = 'M0,0 H' + container.offsetWidth + ' V' + container.offsetHeight + ' H0 Z';
                        
                        cutters.forEach(function(cutter) {
                            var x = cutter.offsetLeft;
                            var y = cutter.offsetTop;
                            var width = cutter.offsetWidth;
                            var r = width / 2;
                            var cx = x + r;
                            var cy = y + r;
                            pathString += ' M' + (cx - r) + ',' + cy + ' A' + r + ',' + r + ' 0 1,0 ' + (cx + r) + ',' + cy + ' A' + r + ',' + r + ' 0 1,0 ' + (cx - r) + ',' + cy + ' Z';
                        });
                        
                        var dashWidth = 1;
                        var dashLength = 3;
                        var gapLength = 3;
                        var radius = 17 / 2;
                        var centerX = container.offsetWidth / 2;
                        var lineStartY = radius;
                        var lineEndY = container.offsetHeight - radius;
                        
                        for (var y = lineStartY; y < lineEndY; y += (dashLength + gapLength)) {
                            var startX = centerX - (dashWidth / 2);
                            var endX = centerX + (dashWidth / 2);
                            var currentDashEnd = y + dashLength;
                            if (currentDashEnd > lineEndY) { currentDashEnd = lineEndY; }
                            pathString += ' M' + startX + ',' + y + ' L' + endX + ',' + y + ' L' + endX + ',' + currentDashEnd + ' L' + startX + ',' + currentDashEnd + ' Z';
                        }
                        
                        container.style.clipPath = "path(evenodd, '" + pathString + "')";
                    } catch (e) {
                        console.error('Error applying clip-path: ', e);
                    }
                };
                
                // Run on load
                updateClipPath();
                
                // Re-run on resize
                var resizeTimer;
                var onResize = function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(updateClipPath, 100);
                };
                window.addEventListener('resize', onResize);
                
                // Fallback for MutationObserver if needed, e.g., if styles load late
                if (window.MutationObserver) {
                    new MutationObserver(function(mutations) {
                        for(var m of mutations) {
                            if(m.type === 'attributes' && m.attributeName === 'style') {
                                updateClipPath();
                                break;
                            }
                        }
                    }).observe(container, { attributes: true });
                }
            })();
            </script>
HTML;
            // --- END: Updated HTML block ---

            wp_send_json_success(['html' => $html]);
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
    }
}