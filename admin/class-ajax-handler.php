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

            if ($banner_data['displayMethod'] === 'Fixed') {
                $conflict = $this->check_for_banner_conflict($banner_data['displayOn'], $banner_data['id']);
                if ($conflict['has_conflict']) {
                    wp_send_json_error([
                        'message' => $conflict['message'] // Use the new detailed English message
                    ]);
                    return;
                }
            }

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

        public function delete_banner() {
            check_ajax_referer('yab_nonce', 'nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied.'], 403);
                return;
            }

            if (!isset($_POST['banner_id']) || !is_numeric($_POST['banner_id'])) {
                wp_send_json_error(['message' => 'Invalid banner ID.']);
                return;
            }

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

        private function check_for_banner_conflict($displayOn, $current_banner_id) {
            $conflict = ['has_conflict' => false, 'message' => ''];
            
            $post_ids = !empty($displayOn['posts']) ? array_map('intval', $displayOn['posts']) : [];
            $page_ids = !empty($displayOn['pages']) ? array_map('intval', $displayOn['pages']) : [];
            $cat_ids  = !empty($displayOn['categories']) ? array_map('intval', $displayOn['categories']) : [];

            if (empty($post_ids) && empty($page_ids) && empty($cat_ids)) {
                return $conflict;
            }

            $args = [
                'post_type'    => 'yab_banner',
                'posts_per_page' => -1,
                'post_status'  => 'publish',
                'meta_query'   => [
                    ['key' => '_yab_display_method', 'value' => 'Fixed', 'compare' => '=']
                ],
                'post__not_in' => $current_banner_id ? [intval($current_banner_id)] : [],
            ];

            $other_banners_query = new WP_Query($args);

            foreach ($other_banners_query->posts as $banner_post) {
                $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
                if (empty($data) || empty($data['displayOn'])) continue;

                $other_post_ids = !empty($data['displayOn']['posts']) ? $data['displayOn']['posts'] : [];
                $other_page_ids = !empty($data['displayOn']['pages']) ? $data['displayOn']['pages'] : [];
                $other_cat_ids  = !empty($data['displayOn']['categories']) ? $data['displayOn']['categories'] : [];

                // Type 1: Our selected POSTS vs their selected POSTS
                $post_vs_post = array_intersect($post_ids, $other_post_ids);
                if (!empty($post_vs_post)) {
                    $post_title = get_the_title(reset($post_vs_post));
                    $conflict['has_conflict'] = true;
                    $conflict['message'] = "Assignment Conflict:\nThe post \"{$post_title}\" is already directly assigned to the double banner \"{$banner_post->post_title}\".";
                    return $conflict;
                }

                // Type 2: Our selected PAGES vs their selected PAGES
                $page_vs_page = array_intersect($page_ids, $other_page_ids);
                if (!empty($page_vs_page)) {
                    $page_title = get_the_title(reset($page_vs_page));
                    $conflict['has_conflict'] = true;
                    $conflict['message'] = "Assignment Conflict:\nThe page \"{$page_title}\" is already assigned to the double banner \"{$banner_post->post_title}\".";
                    return $conflict;
                }

                // Type 3: Our selected POSTS vs their selected CATEGORIES
                if (!empty($post_ids) && !empty($other_cat_ids)) {
                    foreach ($post_ids as $post_id) {
                        if (in_category($other_cat_ids, $post_id)) {
                            $post_title = get_the_title($post_id);
                            $conflicting_cat = get_term(reset(wp_get_post_categories($post_id, ['fields' => 'ids', 'include' => $other_cat_ids])));
                            $conflict['has_conflict'] = true;
                            $conflict['message'] = "Assignment Conflict:\nThe post \"{$post_title}\" you selected is already covered by the double banner \"{$banner_post->post_title}\" because it belongs to the category \"{$conflicting_cat->name}\" which is assigned to that banner.";
                            return $conflict;
                        }
                    }
                }

                // Type 4: Our selected CATEGORIES vs their selected POSTS
                if (!empty($cat_ids) && !empty($other_post_ids)) {
                    $posts_in_our_cats = get_posts(['post_type' => 'post', 'posts_per_page' => -1, 'category__in' => $cat_ids, 'fields' => 'ids']);
                    $cat_vs_post = array_intersect($posts_in_our_cats, $other_post_ids);
                    if (!empty($cat_vs_post)) {
                        $post_title = get_the_title(reset($cat_vs_post));
                        $conflicting_cat = get_term(reset(wp_get_post_categories(reset($cat_vs_post), ['fields' => 'ids', 'include' => $cat_ids])));
                        $conflict['has_conflict'] = true;
                        $conflict['message'] = "Assignment Conflict:\nThe category \"{$conflicting_cat->name}\" you selected includes the post \"{$post_title}\", which is already directly assigned to the double banner \"{$banner_post->post_title}\".\n\nPlease remove the direct assignment from that post to proceed.";
                        return $conflict;
                    }
                }
                
                // Type 5: Our selected CATEGORIES vs their selected CATEGORIES
                if (!empty($cat_ids) && !empty($other_cat_ids)) {
                    $posts_in_our_cats = get_posts(['post_type' => 'post', 'posts_per_page' => -1, 'category__in' => $cat_ids, 'fields' => 'ids']);
                    foreach($posts_in_our_cats as $post_id) {
                        if(in_category($other_cat_ids, $post_id)) {
                            $post_title = get_the_title($post_id);
                            $our_cat = get_term(reset(wp_get_post_categories($post_id, ['fields' => 'ids', 'include' => $cat_ids])));
                            $other_cat = get_term(reset(wp_get_post_categories($post_id, ['fields' => 'ids', 'include' => $other_cat_ids])));
                            $conflict['has_conflict'] = true;
                            $conflict['message'] = "Assignment Conflict:\nThe category \"{$our_cat->name}\" you selected has a conflict. It includes the post \"{$post_title}\", which is also covered by the double banner \"{$banner_post->post_title}\" through its assignment to the category \"{$other_cat->name}\".\n\nOne post cannot be covered by two different category rules.";
                            return $conflict;
                        }
                    }
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
