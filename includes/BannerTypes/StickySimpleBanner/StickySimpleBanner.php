<?php
if (!class_exists('Yab_Sticky_Simple_Banner')) :
class Yab_Sticky_Simple_Banner {
    
    public function save($banner_data) {
        if (empty($banner_data['name'])) {
            wp_send_json_error(['message' => 'Banner name is required.']);
            return;
        }

        // Force displayMethod to be 'Fixed'
        $banner_data['displayMethod'] = 'Fixed';

        $conflict = $this->check_for_banner_conflict($banner_data['displayOn'], $banner_data['id']);
        if ($conflict['has_conflict']) {
            wp_send_json_error(['message' => $conflict['message']]);
            return;
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
            update_post_meta($result, '_yab_banner_type', 'sticky-simple-banner');
            update_post_meta($result, '_yab_display_method', $sanitized_data['displayMethod']);
            update_post_meta($result, '_yab_is_active', $sanitized_data['isActive']);
            
            wp_send_json_success(['message' => 'Banner saved successfully!', 'banner_id' => $result]);
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
                'relation' => 'AND',
                ['key' => '_yab_display_method', 'value' => 'Fixed', 'compare' => '='],
                ['key' => '_yab_banner_type', 'value' => 'sticky-simple-banner', 'compare' => '=']
            ],
            'post__not_in' => $current_banner_id ? [intval($current_banner_id)] : [],
        ];

        $other_banners_query = new WP_Query($args);

        foreach ($other_banners_query->posts as $banner_post) {
            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
            if (empty($data) || empty($data['displayOn'])) continue;

            $other_post_ids = !empty($data['displayOn']['posts']) ? array_map('intval', $data['displayOn']['posts']) : [];
            $other_page_ids = !empty($data['displayOn']['pages']) ? array_map('intval', $data['displayOn']['pages']) : [];

            $post_intersection = array_intersect($post_ids, $other_post_ids);
            if (!empty($post_intersection)) {
                $p = get_post(reset($post_intersection));
                return ['has_conflict' => true, 'message' => sprintf('Error: The post "%s" already has the Sticky Simple Banner "%s" assigned to it.', $p->post_title, $banner_post->post_title)];
            }

            $page_intersection = array_intersect($page_ids, $other_page_ids);
            if (!empty($page_intersection)) {
                $p = get_post(reset($page_intersection));
                return ['has_conflict' => true, 'message' => sprintf('Error: The page "%s" already has the Sticky Simple Banner "%s" assigned to it.', $p->post_title, $banner_post->post_title)];
            }
            
            if (!empty($cat_ids)) {
                 $posts_in_cats_query = new WP_Query(['post_type' => 'post', 'posts_per_page' => -1, 'category__in' => $cat_ids, 'fields' => 'ids']);
                if (!empty($posts_in_cats_query->posts)) {
                    $conflict_in_cats = array_intersect($posts_in_cats_query->posts, $other_post_ids);
                    if (!empty($conflict_in_cats)) {
                        $p = get_post(reset($conflict_in_cats));
                        $cat_id = wp_get_post_categories($p->ID, ['fields' => 'ids'])[0];
                        $cat = get_term($cat_id);
                        return ['has_conflict' => true, 'message' => sprintf('Error: In the category "%s" you selected, the post "%s" already has the Sticky Simple Banner "%s" assigned to it.', $cat->name, $p->post_title, $banner_post->post_title)];
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
            } elseif (is_numeric($value) || $value === null) {
                $sanitized[$key] = $value;
            } else {
                switch ($key) {
                    case 'buttonLink':
                        $sanitized[$key] = esc_url_raw(trim($value));
                        break;
                    case 'textColor':
                    case 'buttonBgColor':
                    case 'buttonTextColor':
                    case 'bgColor':
                    case 'gradientColor1':
                    case 'gradientColor2':
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