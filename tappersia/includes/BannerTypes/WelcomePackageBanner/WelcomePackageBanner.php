<?php
// tappersia/includes/BannerTypes/WelcomePackageBanner/WelcomePackageBanner.php

if (!class_exists('Yab_Welcome_Package_Banner')) :
class Yab_Welcome_Package_Banner {

    public function save($banner_data) {
        if (empty($banner_data['name'])) {
            wp_send_json_error(['message' => 'Banner name is required.']);
            return;
        }

        // --- Specific Welcome Package Validation ---
        if (empty($banner_data['welcome_package']['selectedKey'])) {
            wp_send_json_error(['message' => 'Please select a Welcome Package.'], 400);
            return;
        }
        if (empty($banner_data['welcome_package']['html'])) {
            wp_send_json_error(['message' => 'Banner HTML content cannot be empty.'], 400);
            return;
        }
        // --- End Validation ---

        if (($banner_data['displayMethod'] ?? 'Embeddable') === 'Fixed') {
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
        if ($post_id > 0) { $post_data['ID'] = $post_id; }
        $result = wp_insert_post($post_data, true);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        } else {
            // Remove general banner data before saving as meta
            unset($sanitized_data['name'], $sanitized_data['id']);

            update_post_meta($result, '_yab_banner_data', $sanitized_data);
            update_post_meta($result, '_yab_banner_type', 'welcome-package-banner'); // Set type
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
                ['key' => '_yab_banner_type', 'value' => 'welcome-package-banner', 'compare' => '='] // Check this type
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
            // Add category conflict check if necessary
        }
        return $conflict;
    }

    private function get_allowed_html_tags() {
        // Define allowed tags and attributes for wp_kses
        // Allows common formatting, structure, style, and script tags with specific attributes.
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
            // Add other tags as needed, be restrictive with attributes
        );
    }

    private function sanitize_banner_data($data) {
        $sanitized = [];
        if (!is_array($data)) return $sanitized;

        // General banner properties
        $sanitized['id'] = isset($data['id']) ? intval($data['id']) : null;
        $sanitized['name'] = isset($data['name']) ? sanitize_text_field($data['name']) : '';
        $sanitized['displayMethod'] = isset($data['displayMethod']) ? sanitize_text_field($data['displayMethod']) : 'Embeddable';
        $sanitized['isActive'] = isset($data['isActive']) ? boolval($data['isActive']) : true;
        $sanitized['type'] = 'welcome-package-banner'; // Hardcode type

        // Welcome Package specific data
        if (isset($data['welcome_package']) && is_array($data['welcome_package'])) {
            $wp_data = $data['welcome_package'];
            $sanitized['welcome_package'] = [
                'selectedKey' => isset($wp_data['selectedKey']) ? sanitize_text_field($wp_data['selectedKey']) : null,
                'selectedPrice' => isset($wp_data['selectedPrice']) ? floatval($wp_data['selectedPrice']) : null,
                'selectedOriginalPrice' => isset($wp_data['selectedOriginalPrice']) ? floatval($wp_data['selectedOriginalPrice']) : null,
                // Sanitize HTML using wp_kses
                'html' => isset($wp_data['html']) ? wp_kses($wp_data['html'], $this->get_allowed_html_tags()) : '',
            ];
        } else {
            // Default empty structure if not provided
            $sanitized['welcome_package'] = [
                'selectedKey' => null,
                'selectedPrice' => null,
                'selectedOriginalPrice' => null,
                'html' => '',
            ];
        }

        // Display Conditions
        $sanitized['displayOn'] = [
            'posts' => isset($data['displayOn']['posts']) ? array_map('intval', $data['displayOn']['posts']) : [],
            'pages' => isset($data['displayOn']['pages']) ? array_map('intval', $data['displayOn']['pages']) : [],
            'categories' => isset($data['displayOn']['categories']) ? array_map('intval', $data['displayOn']['categories']) : [],
        ];

        return $sanitized;
    }
}
endif;