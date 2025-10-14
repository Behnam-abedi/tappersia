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
                'double-banner' => 'Yab_Double_Banner',
                'single-banner' => 'Yab_Single_Banner',
                'api-banner' => 'Yab_Api_Banner',
                'simple-banner' => 'Yab_Simple_Banner',
                'sticky-simple-banner' => 'Yab_Sticky_Simple_Banner',
                'promotion-banner' => 'Yab_Promotion_Banner',
                'content-html-banner' => 'Yab_Content_Html_Banner',
                'content-html-sidebar-banner' => 'Yab_Content_Html_Sidebar_Banner',
                'tour-carousel' => 'Yab_Tour_Carousel',
                'flight-ticket' => 'Yab_Flight_Ticket', // Add this line
            ];

            if (array_key_exists($banner_type_slug, $handlers)) {
                $class_name = $handlers[$banner_type_slug];
                
                $folder_name = str_replace('_', '', str_replace('Yab_', '', $class_name));
                $file_path = YAB_PLUGIN_DIR . 'includes/BannerTypes/' . $folder_name . '/' . $folder_name . '.php';

                if (file_exists($file_path)) {
                    require_once $file_path;
                    if (class_exists($class_name)) {
                        return new $class_name();
                    }
                }
            }

            return null;
        }
        
        public function save_banner() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }
            if (!isset($_POST['banner_data']) || !isset($_POST['banner_type'])) { wp_send_json_error(['message' => 'Incomplete data received.']); return; }
            
            $banner_data = json_decode(stripslashes($_POST['banner_data']), true);
            $banner_type = sanitize_text_field($_POST['banner_type']);
            
            $handler = $this->get_banner_type_handler($banner_type);
            
            if (!$handler) { 
                wp_send_json_error(['message' => 'Invalid banner type specified.']); 
                return; 
            }
            
            $handler->save($banner_data);
        }

        public function delete_banner() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Permission denied.'], 403); return; }
            if (!isset($_POST['banner_id']) || !is_numeric($_POST['banner_id'])) { wp_send_json_error(['message' => 'Invalid banner ID.']); return; }
            
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
}