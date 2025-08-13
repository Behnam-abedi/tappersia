<?php
if (!class_exists('Yab_Ajax_Handler')) :
    class Yab_Ajax_Handler {

        public function save_double_banner() {
            check_ajax_referer('yab_nonce', 'nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied.'], 403);
                return;
            }

            if (!isset($_POST['banner_data'])) {
                wp_send_json_error(['message' => 'No data received.']);
                return;
            }
            
            $banner_data = json_decode(stripslashes($_POST['banner_data']), true);

            if (empty($banner_data['name'])) {
                wp_send_json_error(['message' => 'Banner name is required.']);
                return;
            }

            // --- شروع بررسی تداخل بنر ---
            if ($banner_data['displayMethod'] === 'Fixed') {
                $conflict = $this->check_for_banner_conflict($banner_data['displayOn'], $banner_data['id']);
                if ($conflict['has_conflict']) {
                    wp_send_json_error([
                        'message' => 'Assignment Conflict: ' . $conflict['message']
                    ]);
                    return;
                }
            }
            // --- پایان بررسی تداخل بنر ---

            $sanitized_data = $this->sanitize_banner_data($banner_data);
            $post_id = !empty($sanitized_data['id']) ? intval($sanitized_data['id']) : 0;
            
            $post_data = [
                'post_title'    => $sanitized_data['name'],
                'post_type'     => 'yab_banner',
                'post_status'   => 'publish',
            ];

            if ($post_id > 0) {
                $post_data['ID'] = $post_id;
            }

            $result = wp_insert_post($post_data, true);

            if (is_wp_error($result)) {
                wp_send_json_error(['message' => $result->get_error_message()]);
            } else {
                // Remove name and id as they are part of the post object, not meta
                unset($sanitized_data['name']);
                unset($sanitized_data['id']);

                update_post_meta($result, '_yab_banner_data', $sanitized_data);
                update_post_meta($result, '_yab_display_method', $sanitized_data['displayMethod']);
                update_post_meta($result, '_yab_is_active', $sanitized_data['isActive']);
                
                wp_send_json_success(['message' => 'Banner saved successfully!', 'banner_id' => $result]);
            }

            wp_die();
        }

        public function search_content() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied.'], 403);
                return;
            }

            $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
            $content_type = isset($_POST['content_type']) ? sanitize_text_field($_POST['content_type']) : 'posts';

            $results = [];

            switch ($content_type) {
                case 'posts':
                    $query = new WP_Query([
                        'post_type' => 'post',
                        'posts_per_page' => 50,
                        's' => $search_term,
                        'post_status' => 'publish'
                    ]);
                    foreach ($query->posts as $post) {
                        $results[] = ['ID' => $post->ID, 'post_title' => $post->post_title];
                    }
                    break;
                case 'pages':
                    $query = new WP_Query([
                        'post_type' => 'page',
                        'posts_per_page' => 50,
                        's' => $search_term,
                        'post_status' => 'publish'
                    ]);
                    foreach ($query->posts as $page) {
                        $results[] = ['ID' => $page->ID, 'post_title' => $page->post_title];
                    }
                    break;
                case 'categories':
                    $terms = get_terms([
                        'taxonomy' => 'category',
                        'name__like' => $search_term,
                        'hide_empty' => false,
                        'number' => 50
                    ]);
                    foreach ($terms as $term) {
                        $results[] = ['term_id' => $term->term_id, 'name' => $term->name];
                    }
                    break;
            }
            wp_send_json_success($results);
            wp_die();
        }

        private function check_for_banner_conflict($displayOn, $current_banner_id) {
            $conflict = ['has_conflict' => false, 'message' => ''];
            
            $post_ids = !empty($displayOn['posts']) ? array_map('intval', $displayOn['posts']) : [];
            $page_ids = !empty($displayOn['pages']) ? array_map('intval', $displayOn['pages']) : [];
            $cat_ids  = !empty($displayOn['categories']) ? array_map('intval', $displayOn['categories']) : [];

            if (empty($post_ids) && empty($page_ids) && empty($cat_ids)) {
                return $conflict; // No rules, no conflict
            }

            $args = [
                'post_type' => 'yab_banner',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_query' => [
                    ['key' => '_yab_display_method', 'value' => 'Fixed', 'compare' => '=']
                ],
                'exclude' => $current_banner_id ? [$current_banner_id] : [],
            ];

            $query = new WP_Query($args);
            $other_banners = $query->posts;

            foreach ($other_banners as $banner_post) {
                $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
                if (empty($data) || empty($data['displayOn'])) continue;

                $other_display_on = $data['displayOn'];

                $conflicting_posts = array_intersect($post_ids, $other_display_on['posts'] ?? []);
                if (!empty($conflicting_posts)) {
                    $post_title = get_the_title(reset($conflicting_posts));
                    $conflict['has_conflict'] = true;
                    $conflict['message'] = "Post \"{$post_title}\" is already assigned to another banner ({$banner_post->post_title}).";
                    return $conflict;
                }

                $conflicting_pages = array_intersect($page_ids, $other_display_on['pages'] ?? []);
                if (!empty($conflicting_pages)) {
                    $page_title = get_the_title(reset($conflicting_pages));
                    $conflict['has_conflict'] = true;
                    $conflict['message'] = "Page \"{$page_title}\" is already assigned to another banner ({$banner_post->post_title}).";
                    return $conflict;
                }
                
                $conflicting_cats = array_intersect($cat_ids, $other_display_on['categories'] ?? []);
                if (!empty($conflicting_cats)) {
                    $cat_name = get_term(reset($conflicting_cats))->name;
                    $conflict['has_conflict'] = true;
                    $conflict['message'] = "Category \"{$cat_name}\" is already assigned to another banner ({$banner_post->post_title}).";
                    return $conflict;
                }
            }

            return $conflict;
        }
        
        private function sanitize_banner_data($data) {
            $sanitized = [];
            if (!is_array($data)) return $sanitized;

            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $sanitized[$key] = $this->sanitize_banner_data($value);
                } elseif (is_bool($value)) {
                    $sanitized[$key] = $value;
                } elseif (is_numeric($value)) {
                    $sanitized[$key] = $value;
                } else {
                    switch ($key) {
                        case 'buttonLink':
                        case 'imageUrl':
                            $sanitized[$key] = esc_url_raw(trim($value));
                            break;
                        case 'descText':
                            $sanitized[$key] = wp_kses_post(trim($value));
                            break;
                        case 'bgColor':
                        case 'gradientColor1':
                        case 'gradientColor2':
                        case 'titleColor':
                        case 'descColor':
                        case 'buttonBgColor':
                        case 'buttonTextColor':
                            $sanitized[$key] = sanitize_hex_color($value);
                            break;
                        default:
                            $sanitized[$key] = sanitize_text_field(trim($value));
                    }
                }
            }
            return $sanitized;
        }
    }
endif;