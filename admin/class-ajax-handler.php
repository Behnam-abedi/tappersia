<?php
if (!class_exists('Yab_Ajax_Handler')) :
    class Yab_Ajax_Handler {
        public function save_double_banner() {
            check_ajax_referer('yab_nonce', 'nonce');

            if ( ! current_user_can('manage_options') ) {
                wp_send_json_error(array('message' => 'Permission denied.'), 403);
                return;
            }

            if ( ! isset($_POST['banner_data']) ) {
                wp_send_json_error(array('message' => 'No data received.'));
                return;
            }
            
            $banner_data = json_decode(stripslashes($_POST['banner_data']), true);

            if ( empty($banner_data['name']) ) {
                wp_send_json_error(array('message' => 'Banner name is required.'));
                return;
            }

            $sanitized_data = $this->sanitize_banner_data($banner_data);
            $post_id = !empty($sanitized_data['id']) ? intval($sanitized_data['id']) : 0;
            
            $post_data = array(
                'post_title'    => $sanitized_data['name'],
                'post_type'     => 'yab_banner',
                'post_status'   => 'publish',
            );

            if ($post_id > 0) {
                $post_data['ID'] = $post_id;
            }

            $result = wp_insert_post($post_data, true);

            if (is_wp_error($result)) {
                wp_send_json_error(array('message' => $result->get_error_message()));
            } else {
                update_post_meta($result, '_yab_banner_data', $sanitized_data);
                update_post_meta($result, '_yab_display_method', $sanitized_data['displayMethod']);
                update_post_meta($result, '_yab_is_active', $sanitized_data['isActive']);
                
                wp_send_json_success(array('message' => 'Banner saved successfully!', 'banner_id' => $result));
            }

            wp_die();
        }
        
        private function sanitize_banner_data($data) {
            $sanitized = [];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $sanitized[$key] = $this->sanitize_banner_data($value);
                } elseif (is_bool($value)) {
                    $sanitized[$key] = $value;
                } elseif (is_numeric($value)) {
                    $sanitized[$key] = $value;
                }
                else {
                    switch ($key) {
                        case 'buttonLink':
                        case 'imageUrl':
                            $sanitized[$key] = esc_url_raw($value);
                            break;
                        case 'descText':
                            $sanitized[$key] = wp_kses_post($value);
                            break;
                        default:
                            $sanitized[$key] = sanitize_text_field($value);
                    }
                }
            }
            return $sanitized;
        }
    }
endif;