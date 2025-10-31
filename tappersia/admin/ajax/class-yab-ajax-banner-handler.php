<?php
// tappersia/admin/ajax/class-yab-ajax-banner-handler.php

if (!class_exists('Yab_Ajax_Banner_Handler')) {
    class Yab_Ajax_Banner_Handler {

        public function register_hooks() {
            add_action('wp_ajax_yab_save_banner', [$this, 'save_banner']);
            add_action('wp_ajax_yab_delete_banner', [$this, 'delete_banner']);
        }

        private function get_banner_type_handler($banner_type_slug) {
            $handlers = [
                'double-banner'                 => 'Yab_Double_Banner',
                'single-banner'                 => 'Yab_Single_Banner',
                'api-banner'                    => 'Yab_Api_Banner',
                'simple-banner'                 => 'Yab_Simple_Banner',
                'sticky-simple-banner'          => 'Yab_Sticky_Simple_Banner',
                'promotion-banner'              => 'Yab_Promotion_Banner',
                'content-html-banner'           => 'Yab_Content_Html_Banner',
                'content-html-sidebar-banner'   => 'Yab_Content_Html_Sidebar_Banner',
                'tour-carousel'                 => 'Yab_Tour_Carousel',
                'hotel-carousel'                => 'Yab_Hotel_Carousel',
                'flight-ticket'                 => 'Yab_Flight_Ticket',
                'welcome-package-banner'        => 'Yab_Welcome_Package_Banner', // Added
            ];

            if (array_key_exists($banner_type_slug, $handlers)) {
                $class_name = $handlers[$banner_type_slug];

                // Adjusted folder name logic slightly for consistency
                // Converts Yab_Welcome_Package_Banner to WelcomePackageBanner
                $folder_name = str_replace(['Yab_', '_'], ['', ''], $class_name);
                $file_path = YAB_PLUGIN_DIR . 'includes/BannerTypes/' . $folder_name . '/' . $folder_name . '.php';


                if (file_exists($file_path)) {
                    require_once $file_path;
                    if (class_exists($class_name)) {
                        return new $class_name();
                    }
                } else {
                     // Optionally log an error if the file doesn't exist
                     error_log("Tappersia Plugin: Banner handler file not found at " . $file_path);
                }
            } else {
                 error_log("Tappersia Plugin: No handler found for banner type " . $banner_type_slug);
            }


            return null;
        }

        public function save_banner() {
            // Check nonce and permissions
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied.'], 403);
                return;
            }

            // Check if required data is present
            if (!isset($_POST['banner_data']) || !isset($_POST['banner_type'])) {
                wp_send_json_error(['message' => 'Incomplete data received.'], 400);
                return;
            }

            // Decode banner data (use stripslashes for potential escaping issues)
            $banner_data = json_decode(stripslashes($_POST['banner_data']), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                 wp_send_json_error(['message' => 'Invalid banner data format (JSON decode failed).', 'error_details' => json_last_error_msg()], 400);
                 return;
            }

            $banner_type = sanitize_text_field($_POST['banner_type']);

            // Get the appropriate handler
            $handler = $this->get_banner_type_handler($banner_type);

            if (!$handler) {
                wp_send_json_error(['message' => 'Invalid banner type (' . esc_html($banner_type) . ') specified or handler not found.'], 400);
                return;
            }

            // Call the handler's save method (which should handle wp_send_json_success/error and wp_die)
             $handler->save($banner_data);

             // Fallback wp_die() in case handler doesn't call it (should not happen ideally)
             wp_die();
        }

        // --- delete_banner function remains unchanged ---
        public function delete_banner() {
             check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }
            if (!isset($_POST['banner_id']) || !is_numeric($_POST['banner_id'])) { wp_send_json_error(['message' => 'Invalid banner ID.'], 400); return; }

            $banner_id = intval($_POST['banner_id']);
            $post = get_post($banner_id);

            if (!$post || $post->post_type !== 'yab_banner') {
                wp_send_json_error(['message' => 'Banner not found.'], 404);
                return;
            }

            $result = wp_delete_post($banner_id, true); // true forces delete, false moves to trash

            if ($result === false) {
                wp_send_json_error(['message' => 'Failed to delete the banner.'], 500);
            } else {
                wp_send_json_success(['message' => 'Banner deleted successfully.']);
            }

            wp_die();
        }
    }
}