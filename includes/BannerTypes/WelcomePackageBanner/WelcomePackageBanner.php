<?php
// /includes/BannerTypes/WelcomePackageBanner/WelcomePackageBanner.php

if (!class_exists('Yab_Welcome_Package_Banner')) :
class Yab_Welcome_Package_Banner {

    public function save($banner_data) {
        // Validation: Check required fields
        if (empty($banner_data['name'])) {
            wp_send_json_error(['message' => 'Banner name is required.'], 400);
            return;
        }
        if (empty($banner_data['welcome_package']['selectedPackageKey'])) {
            wp_send_json_error(['message' => 'Please select a welcome package.'], 400);
            return;
        }
         if (empty($banner_data['welcome_package']['htmlContent'])) {
            wp_send_json_error(['message' => 'HTML content cannot be empty.'], 400);
            return;
        }

        // Conflict check (remains the same logic as other types)
        if ($banner_data['displayMethod'] === 'Fixed') {
            $conflict = $this->check_for_banner_conflict($banner_data['displayOn'], $banner_data['id']);
            if ($conflict['has_conflict']) {
                wp_send_json_error(['message' => $conflict['message']], 409); // 409 Conflict
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
            wp_send_json_error(['message' => $result->get_error_message()], 500);
        } else {
            // Remove general banner data before saving as meta
            unset($sanitized_data['name']);
            unset($sanitized_data['id']);

            // Save specific and general banner data
            update_post_meta($result, '_yab_banner_data', $sanitized_data);
            update_post_meta($result, '_yab_banner_type', 'welcome-package-banner');
            update_post_meta($result, '_yab_display_method', $sanitized_data['displayMethod']);
            update_post_meta($result, '_yab_is_active', $sanitized_data['isActive']);

            wp_send_json_success(['message' => 'Welcome Package Banner saved successfully!', 'banner_id' => $result]);
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
                ['key' => '_yab_banner_type', 'value' => 'welcome-package-banner', 'compare' => '='] // Check for this specific type
            ],
            'post__not_in' => $current_banner_id ? [intval($current_banner_id)] : [],
        ];

        $other_banners_query = new WP_Query($args);

        foreach ($other_banners_query->posts as $banner_post) {
            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
            if (empty($data) || empty($data['displayOn'])) continue;

            $other_post_ids = !empty($data['displayOn']['posts']) ? array_map('intval', $data['displayOn']['posts']) : [];
            if (!empty(array_intersect($post_ids, $other_post_ids))) {
                return ['has_conflict' => true, 'message' => 'A Welcome Package Banner is already assigned to one of the selected posts.'];
            }

            $other_page_ids = !empty($data['displayOn']['pages']) ? array_map('intval', $data['displayOn']['pages']) : [];
            if (!empty(array_intersect($page_ids, $other_page_ids))) {
                 return ['has_conflict' => true, 'message' => 'A Welcome Package Banner is already assigned to one of the selected pages.'];
            }
            // Add category conflict check if necessary for your plugin logic
        }
        return $conflict;
    }

    private function sanitize_banner_data($data) {
        $sanitized = [];
        if (!is_array($data)) return $sanitized;

        // Sanitize general banner properties
        $sanitized['id'] = isset($data['id']) ? intval($data['id']) : null;
        $sanitized['name'] = isset($data['name']) ? sanitize_text_field($data['name']) : '';
        $sanitized['displayMethod'] = isset($data['displayMethod']) ? sanitize_text_field($data['displayMethod']) : 'Fixed';
        $sanitized['isActive'] = isset($data['isActive']) ? boolval($data['isActive']) : true;
        $sanitized['type'] = 'welcome-package-banner'; // Force type

        // Sanitize specific welcome_package data
        if (isset($data['welcome_package']) && is_array($data['welcome_package'])) {
            $sanitized['welcome_package'] = [
                'selectedPackageKey' => isset($data['welcome_package']['selectedPackageKey']) ? sanitize_text_field($data['welcome_package']['selectedPackageKey']) : null,
                // Allow specific HTML tags (e.g., div, span, p, a, img, basic styling) but sanitize potentially harmful ones
                'htmlContent' => isset($data['welcome_package']['htmlContent']) ? wp_kses_post($data['welcome_package']['htmlContent']) : '',
                // Store initially selected prices (might be useful, though frontend fetches live)
                'initialOriginalPrice' => isset($data['welcome_package']['originalPrice']) ? sanitize_text_field($data['welcome_package']['originalPrice']) : null,
                'initialDiscountedPrice' => isset($data['welcome_package']['discountedPrice']) ? sanitize_text_field($data['welcome_package']['discountedPrice']) : null,
            ];
        } else {
            $sanitized['welcome_package'] = [
                'selectedPackageKey' => null,
                'htmlContent' => '',
                'initialOriginalPrice' => null,
                'initialDiscountedPrice' => null,
            ];
        }


        // Sanitize display conditions
        $sanitized['displayOn'] = [
            'posts' => isset($data['displayOn']['posts']) ? array_map('intval', $data['displayOn']['posts']) : [],
            'pages' => isset($data['displayOn']['pages']) ? array_map('intval', $data['displayOn']['pages']) : [],
            'categories' => isset($data['displayOn']['categories']) ? array_map('intval', $data['displayOn']['categories']) : [],
        ];

         // Sanitize other banner types if they exist in the data (copy from existing handlers)
         foreach (['single', 'single_mobile', 'double', 'simple', 'simple_mobile', 'sticky_simple', 'sticky_simple_mobile', 'promotion', 'promotion_mobile', 'content_html', 'content_html_sidebar', 'api', 'tour_carousel', 'hotel_carousel', 'flight_ticket'] as $key) {
             if (isset($data[$key])) {
                 // You'll need appropriate sanitization logic for each type here
                 // For simplicity, just assigning, but real implementation needs proper sanitization
                 $sanitized[$key] = $data[$key];
             }
         }


        return $sanitized;
    }
}
endif;
