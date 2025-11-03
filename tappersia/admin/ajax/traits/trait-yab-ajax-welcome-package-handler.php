<?php
// tappersia/admin/ajax/traits/trait-yab-ajax-welcome-package-handler.php

if (!trait_exists('Yab_Ajax_Welcome_Package_Handler')) {
    trait Yab_Ajax_Welcome_Package_Handler {

        public function fetch_welcome_packages() {
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

             $allowed_tags = $this->get_allowed_html_tags();
             $final_sanitized_html = wp_kses($rendered_html, $allowed_tags);

             wp_send_json_success(['html' => $final_sanitized_html]);
             wp_die();
        }

        /**
         * Defines allowed HTML tags for the Welcome Package SSR renderer.
         * @return array
         */
        private function get_allowed_html_tags() {
            // Define allowed tags and attributes for wp_kses
            return array(
                'html'   => array('lang' => true, 'dir' => true),
                'head'   => array(),
                'body'   => array('class' => true, 'id' => true, 'style' => true),
                'meta'   => array('charset' => true, 'name' => true, 'content' => true, 'http-equiv' => true),
                'title'  => array(),
                'style'  => array('type' => true), // Allow style tags
                'script' => array('type' => true, 'src' => true, 'async' => true, 'defer' => true), // Allow script tags with limited attributes
                'div'    => array('class' => true, 'id' => true, 'style' => true,'role' => true,'aria-label' => true),
                'article'=> array('class' => true, 'id' => true, 'style' => true),
                'span'   => array('class' => true, 'id' => true, 'style' => true),
                'p'      => array('class' => true, 'id' => true, 'style' => true),
                'a'      => array('href' => true, 'target' => true, 'rel' => true, 'class' => true, 'id' => true, 'style' => true,'style' => true),
                'img'    => array('src' => true, 'alt' => true, 'width' => true, 'height' => true, 'class' => true, 'id' => true, 'role' => true,'aria-label' => true),
                'h1'     => array('class' => true, 'id' => true, 'style' => true),
                'h2'     => array('class' => true, 'id' => true, 'style' => true),
                'h3'     => array('class' => true, 'id' => true, 'style' => true),
                'h4'     => array('class' => true, 'id' => true, 'style' => true),
                'h5'     => array('class' => true, 'id' => true, 'style' => true),
                'h6'     => array('class' => true, 'id' => true, 'style' => true),
                'ul'     => array('class' => true, 'id' => true, 'style' => true),
                'ol'     => array('class' => true, 'id' => true, 'style' => true),
                'li'     => array('class' => true, 'id' => true, 'style' => true),
                'strong' => array(),
                'em'     => array(),
                'br'     => array(),
                'table'  => array('class' => true, 'id' => true, 'style' => true, 'border' => true, 'cellpadding' => true, 'cellspacing' => true),
                'thead'  => array(),
                'tbody'  => array(),
                'tr'     => array('class' => true, 'id' => true, 'style' => true),
                'th'     => array('class' => true, 'id' => true, 'style' => true, 'scope' => true),
                'td'     => array('class' => true, 'id' => true, 'style' => true, 'colspan' => true, 'rowspan' => true),
                'button' => array('class' => true, 'id' => true, 'style' => true, 'type' => true, 'onclick' => true), // Allow basic button attributes
            );
        }
    }
}