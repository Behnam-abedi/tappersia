<?php
// tappersia/includes/BannerTypes/ContentHtmlBanner/ContentHtmlBanner.php

if (!class_exists('Yab_Content_Html_Banner')) :
class Yab_Content_Html_Banner {
    
    public function save($banner_data) {
        if (empty($banner_data['name'])) {
            wp_send_json_error(['message' => 'Banner name is required.']);
            return;
        }

        if ($banner_data['displayMethod'] === 'Fixed') {
            $conflict = $this->check_for_banner_conflict($banner_data['displayOn'], $banner_data['id']);
            if ($conflict['has_conflict']) {
                wp_send_json_error(['message' => $conflict['message']]);
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
            update_post_meta($result, '_yab_banner_type', 'content-html-banner');
            update_post_meta($result, '_yab_display_method', $sanitized_data['displayMethod']);
            update_post_meta($result, '_yab_is_active', $sanitized_data['isActive']);
            
            wp_send_json_success(['message' => 'Banner saved successfully!', 'banner_id' => $result]);
        }

        wp_die();
    }

    private function check_for_banner_conflict($displayOn, $current_banner_id) {
        // This logic remains the same as other banner types
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
                ['key' => '_yab_banner_type', 'value' => 'content-html-banner', 'compare' => '=']
            ],
            'post__not_in' => $current_banner_id ? [intval($current_banner_id)] : [],
        ];

        $other_banners_query = new WP_Query($args);

        foreach ($other_banners_query->posts as $banner_post) {
            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
            if (empty($data) || empty($data['displayOn'])) continue;

            $other_post_ids = !empty($data['displayOn']['posts']) ? array_map('intval', $data['displayOn']['posts']) : [];
            if (!empty(array_intersect($post_ids, $other_post_ids))) {
                return ['has_conflict' => true, 'message' => 'A Content HTML banner is already assigned to one of the selected posts.'];
            }

            $other_page_ids = !empty($data['displayOn']['pages']) ? array_map('intval', $data['displayOn']['pages']) : [];
            if (!empty(array_intersect($page_ids, $other_page_ids))) {
                 return ['has_conflict' => true, 'message' => 'A Content HTML banner is already assigned to one of the selected pages.'];
            }
        }
        return $conflict;
    }
    
    private function sanitize_banner_data($data) {
        $sanitized = [];
        if (!is_array($data)) return $sanitized;

        // Sanitize banner-level properties
        $sanitized['id'] = isset($data['id']) ? intval($data['id']) : null;
        $sanitized['name'] = isset($data['name']) ? sanitize_text_field($data['name']) : '';
        $sanitized['displayMethod'] = isset($data['displayMethod']) ? sanitize_text_field($data['displayMethod']) : 'Fixed';
        $sanitized['isActive'] = isset($data['isActive']) ? boolval($data['isActive']) : true;
        $sanitized['type'] = isset($data['type']) ? sanitize_text_field($data['type']) : 'content-html-banner';

        // For the 'content_html' part, we don't sanitize the HTML to allow user flexibility.
        // The admin user is responsible for the content they add.
        if (isset($data['content_html']) && is_array($data['content_html'])) {
            $sanitized['content_html']['html'] = $data['content_html']['html'];
        } else {
            $sanitized['content_html']['html'] = '';
        }

        // Sanitize display conditions
        $sanitized['displayOn'] = [
            'posts' => isset($data['displayOn']['posts']) ? array_map('intval', $data['displayOn']['posts']) : [],
            'pages' => isset($data['displayOn']['pages']) ? array_map('intval', $data['displayOn']['pages']) : [],
            'categories' => isset($data['displayOn']['categories']) ? array_map('intval', $data['displayOn']['categories']) : [],
        ];

        return $sanitized;
    }
}
endif;