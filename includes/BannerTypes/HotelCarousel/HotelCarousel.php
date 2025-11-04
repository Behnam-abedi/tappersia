<?php
// tappersia/includes/BannerTypes/HotelCarousel/HotelCarousel.php

if (!class_exists('Yab_Hotel_Carousel')) :
class Yab_Hotel_Carousel {

    public function save($banner_data) {
        if (empty($banner_data['name'])) {
            wp_send_json_error(['message' => 'Banner name is required.']);
            return;
        }

        // --- Validation Placeholder ---
        // Add specific hotel carousel validations later if needed.
        // For now, mirroring tour carousel validation logic structure.
        if (isset($banner_data['hotel_carousel']['settings'])) {
            // Placeholder for Desktop validation
        }
        if (isset($banner_data['hotel_carousel']['settings_mobile'])) {
            // Placeholder for Mobile validation
        }
        // --- End Validation Placeholder ---

        if (($banner_data['displayMethod'] ?? 'Embeddable') === 'Fixed') {
            $conflict = $this->check_for_banner_conflict($banner_data['displayOn'], $banner_data['id']);
            if ($conflict['has_conflict']) {
                wp_send_json_error(['message' => $conflict['message']]);
                return;
            }
        }

        $sanitized_data = $this->sanitize_banner_data($banner_data);
        $post_id = !empty($sanitized_data['id']) ? intval($sanitized_data['id']) : 0;

        $post_data = [ 'post_title' => $sanitized_data['name'], 'post_type' => 'yab_banner', 'post_status' => 'publish' ];
        if ($post_id > 0) { $post_data['ID'] = $post_id; }
        $result = wp_insert_post($post_data, true);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        } else {
            unset($sanitized_data['name'], $sanitized_data['id']);
            update_post_meta($result, '_yab_banner_data', $sanitized_data);
            update_post_meta($result, '_yab_banner_type', 'hotel-carousel'); // Changed type
            update_post_meta($result, '_yab_display_method', $sanitized_data['displayMethod']);
            update_post_meta($result, '_yab_is_active', $sanitized_data['isActive']);
            wp_send_json_success(['message' => 'Hotel Carousel saved successfully!', 'banner_id' => $result]);
        }
        wp_die();
    }

    private function check_for_banner_conflict($displayOn, $current_banner_id) {
        // ... (Conflict check logic remains the same as Tour Carousel) ...
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
                ['key' => '_yab_banner_type', 'value' => 'hotel-carousel', 'compare' => '='] // Changed type
            ],
            'post__not_in' => $current_banner_id ? [intval($current_banner_id)] : [],
        ];

        $other_banners_query = new WP_Query($args);

        foreach ($other_banners_query->posts as $banner_post) {
            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
            if (empty($data) || empty($data['displayOn'])) continue;

            $other_post_ids = !empty($data['displayOn']['posts']) ? array_map('intval', $data['displayOn']['posts']) : [];
            if (!empty(array_intersect($post_ids, $other_post_ids))) {
                return ['has_conflict' => true, 'message' => 'A Hotel Carousel is already assigned to one of the selected posts.'];
            }

            $other_page_ids = !empty($data['displayOn']['pages']) ? array_map('intval', $data['displayOn']['pages']) : [];
            if (!empty(array_intersect($page_ids, $other_page_ids))) {
                 return ['has_conflict' => true, 'message' => 'A Hotel Carousel is already assigned to one of the selected pages.'];
            }
            // Add category conflict check if necessary
        }
        return $conflict;
    }

    private function sanitize_settings_object($settings) {
        // --- START: Update to include new card settings ---
        $sanitized = [];
        $sanitized['slidesPerView'] = isset($settings['slidesPerView']) ? intval($settings['slidesPerView']) : 3;
        $sanitized['cardWidth'] = isset($settings['cardWidth']) ? intval($settings['cardWidth']) : 295; // +++ مطمئن شوید این خط وجود دارد +++
        $sanitized['loop'] = isset($settings['loop']) ? boolval($settings['loop']) : false;
        $sanitized['spaceBetween'] = isset($settings['spaceBetween']) ? intval($settings['spaceBetween']) : 20;
        $sanitized['isDoubled'] = isset($settings['isDoubled']) ? boolval($settings['isDoubled']) : false;
        $sanitized['gridFill'] = isset($settings['gridFill']) ? sanitize_text_field($settings['gridFill']) : 'column';
        $sanitized['direction'] = isset($settings['direction']) ? sanitize_text_field($settings['direction']) : 'ltr';

        $sanitized['header'] = [
            'text' => isset($settings['header']['text']) ? sanitize_text_field($settings['header']['text']) : 'Top Rated Hotel in Isfahan',
            'fontSize' => isset($settings['header']['fontSize']) ? intval($settings['header']['fontSize']) : 24,
            'fontWeight' => isset($settings['header']['fontWeight']) ? sanitize_text_field($settings['header']['fontWeight']) : '700',
            'color' => isset($settings['header']['color']) ? sanitize_hex_color($settings['header']['color']) : '#000000',
            'lineColor' => isset($settings['header']['lineColor']) ? sanitize_hex_color($settings['header']['lineColor']) : '#00BAA4',
            'marginTop' => isset($settings['header']['marginTop']) ? intval($settings['header']['marginTop']) : 28,
        ];

        $sanitized['card'] = $this->sanitize_card_settings($settings['card'] ?? []); // Call new function

        $sanitized['autoplay'] = [
            'enabled' => isset($settings['autoplay']['enabled']) ? boolval($settings['autoplay']['enabled']) : false,
            'delay' => isset($settings['autoplay']['delay']) ? intval($settings['autoplay']['delay']) : 3000,
        ];
        $sanitized['navigation'] = [ 'enabled' => isset($settings['navigation']['enabled']) ? boolval($settings['navigation']['enabled']) : true ];
        $sanitized['pagination'] = [
            'enabled' => isset($settings['pagination']['enabled']) ? boolval($settings['pagination']['enabled']) : true,
            'paginationColor' => isset($settings['pagination']['paginationColor']) ? sanitize_text_field($settings['pagination']['paginationColor']) : '#00BAA44F',
            'paginationActiveColor' => isset($settings['pagination']['paginationActiveColor']) ? sanitize_text_field($settings['pagination']['paginationActiveColor']) : '#00BAA4',
        ];
        // --- END: Update to include new card settings ---
        return $sanitized;
    }

    private function sanitize_banner_data($data) {
        $sanitized = [];
        if (!is_array($data)) return $sanitized;

        foreach ($data as $key => $value) {
            if ($key === 'hotel_carousel' && is_array($value)) { // Changed key
                $sanitized['hotel_carousel'] = [];
                // Adapt keys if needed, e.g., selectedHotels instead of selectedTours
                $sanitized['hotel_carousel']['selectedHotels'] = isset($value['selectedHotels']) ? array_map('intval', $value['selectedHotels']) : [];
                $sanitized['hotel_carousel']['updateCounter'] = isset($value['updateCounter']) ? intval($value['updateCounter']) : 0;
                $sanitized['hotel_carousel']['isMobileConfigured'] = isset($value['isMobileConfigured']) ? boolval($value['isMobileConfigured']) : false;

                if (isset($value['settings']) && is_array($value['settings'])) {
                    $sanitized['hotel_carousel']['settings'] = $this->sanitize_settings_object($value['settings']);
                }
                 if (isset($value['settings_mobile']) && is_array($value['settings_mobile'])) {
                    $sanitized['hotel_carousel']['settings_mobile'] = $this->sanitize_settings_object($value['settings_mobile']);
                }

            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitize_banner_data($value);
            } elseif (is_bool($value)) {
                $sanitized[$key] = $value;
            } elseif (is_numeric($value) || $value === null) {
                $sanitized[$key] = $value;
            } else {
                $sanitized[$key] = sanitize_text_field(trim($value));
            }
        }
        return $sanitized;
    }

     // --- START: Added specific card sanitization ---
     private function sanitize_card_settings($card_settings) {
        $s = []; // Use 's' for sanitized card settings

        // Layout & Border
        $s['minHeight'] = isset($card_settings['minHeight']) ? intval($card_settings['minHeight']) : 357;
        $s['padding'] = isset($card_settings['padding']) ? intval($card_settings['padding']) : 9;
        $s['borderWidth'] = isset($card_settings['borderWidth']) ? intval($card_settings['borderWidth']) : 1;
        $s['borderColor'] = isset($card_settings['borderColor']) ? sanitize_hex_color($card_settings['borderColor']) : '#E5E5E5';
        $s['borderRadius'] = isset($card_settings['borderRadius']) ? intval($card_settings['borderRadius']) : 16;
        $s['bgColor'] = isset($card_settings['bgColor']) ? sanitize_hex_color($card_settings['bgColor']) : '#FFFFFF';

        // Image Area
        $s['imageContainer'] = [
            'paddingX' => isset($card_settings['imageContainer']['paddingX']) ? intval($card_settings['imageContainer']['paddingX']) : 13,
            'paddingY' => isset($card_settings['imageContainer']['paddingY']) ? intval($card_settings['imageContainer']['paddingY']) : 13,
        ];
        $s['image'] = [
            'height' => isset($card_settings['image']['height']) ? intval($card_settings['image']['height']) : 176,
            'radius' => isset($card_settings['image']['radius']) ? intval($card_settings['image']['radius']) : 14,
        ];
        $s['imageOverlay'] = [
            'gradientStartColor' => isset($card_settings['imageOverlay']['gradientStartColor']) ? sanitize_text_field($card_settings['imageOverlay']['gradientStartColor']) : 'rgba(0,0,0,0)',
            'gradientEndColor' => isset($card_settings['imageOverlay']['gradientEndColor']) ? sanitize_text_field($card_settings['imageOverlay']['gradientEndColor']) : 'rgba(0,0,0,0.83)',
            'gradientStartPercent' => isset($card_settings['imageOverlay']['gradientStartPercent']) ? intval($card_settings['imageOverlay']['gradientStartPercent']) : 38,
            'gradientEndPercent' => isset($card_settings['imageOverlay']['gradientEndPercent']) ? intval($card_settings['imageOverlay']['gradientEndPercent']) : 0,
        ];

        // Badges
        $s['badges'] = [
            'bestSeller' => [
                'textColor' => isset($card_settings['badges']['bestSeller']['textColor']) ? sanitize_hex_color($card_settings['badges']['bestSeller']['textColor']) : '#ffffff',
                'fontSize' => isset($card_settings['badges']['bestSeller']['fontSize']) ? intval($card_settings['badges']['bestSeller']['fontSize']) : 12,
                'bgColor' => isset($card_settings['badges']['bestSeller']['bgColor']) ? sanitize_hex_color($card_settings['badges']['bestSeller']['bgColor']) : '#F66A05',
                'paddingX' => isset($card_settings['badges']['bestSeller']['paddingX']) ? intval($card_settings['badges']['bestSeller']['paddingX']) : 7,
                'paddingY' => isset($card_settings['badges']['bestSeller']['paddingY']) ? intval($card_settings['badges']['bestSeller']['paddingY']) : 5,
                'radius' => isset($card_settings['badges']['bestSeller']['radius']) ? intval($card_settings['badges']['bestSeller']['radius']) : 20,
            ],
            'discount' => [
                'textColor' => isset($card_settings['badges']['discount']['textColor']) ? sanitize_hex_color($card_settings['badges']['discount']['textColor']) : '#ffffff',
                'fontSize' => isset($card_settings['badges']['discount']['fontSize']) ? intval($card_settings['badges']['discount']['fontSize']) : 12,
                'bgColor' => isset($card_settings['badges']['discount']['bgColor']) ? sanitize_hex_color($card_settings['badges']['discount']['bgColor']) : '#FB2D51',
                'paddingX' => isset($card_settings['badges']['discount']['paddingX']) ? intval($card_settings['badges']['discount']['paddingX']) : 10,
                'paddingY' => isset($card_settings['badges']['discount']['paddingY']) ? intval($card_settings['badges']['discount']['paddingY']) : 5,
                'radius' => isset($card_settings['badges']['discount']['radius']) ? intval($card_settings['badges']['discount']['radius']) : 20,
            ],
        ];

        // Stars
        $s['stars'] = [
            'shapeSize' => isset($card_settings['stars']['shapeSize']) ? intval($card_settings['stars']['shapeSize']) : 17,
            'shapeColor' => isset($card_settings['stars']['shapeColor']) ? sanitize_hex_color($card_settings['stars']['shapeColor']) : '#FCC13B',
            'textSize' => isset($card_settings['stars']['textSize']) ? intval($card_settings['stars']['textSize']) : 12,
            'textColor' => isset($card_settings['stars']['textColor']) ? sanitize_hex_color($card_settings['stars']['textColor']) : '#ffffff',
        ];

        // Body Content Area
        $s['bodyContent'] = [
             'marginTop' => isset($card_settings['bodyContent']['marginTop']) ? intval($card_settings['bodyContent']['marginTop']) : 14,
             'marginX' => isset($card_settings['bodyContent']['marginX']) ? intval($card_settings['bodyContent']['marginX']) : 19,
             'textColor' => isset($card_settings['bodyContent']['textColor']) ? sanitize_hex_color($card_settings['bodyContent']['textColor']) : '#333333',
        ];

        // Title
        $s['title'] = [
            'fontSize' => isset($card_settings['title']['fontSize']) ? intval($card_settings['title']['fontSize']) : 14,
            'fontWeight' => isset($card_settings['title']['fontWeight']) ? sanitize_text_field($card_settings['title']['fontWeight']) : '600',
            'color' => isset($card_settings['title']['color']) ? sanitize_hex_color($card_settings['title']['color']) : '#333333',
            'lineHeight' => isset($card_settings['title']['lineHeight']) ? floatval($card_settings['title']['lineHeight']) : 1.2,
            'minHeight' => isset($card_settings['title']['minHeight']) ? intval($card_settings['title']['minHeight']) : 34,
        ];

        // Rating Section
        $s['rating'] = [
            'marginTop' => isset($card_settings['rating']['marginTop']) ? intval($card_settings['rating']['marginTop']) : 7,
            'gap' => isset($card_settings['rating']['gap']) ? intval($card_settings['rating']['gap']) : 6,
            'boxBgColor' => isset($card_settings['rating']['boxBgColor']) ? sanitize_hex_color($card_settings['rating']['boxBgColor']) : '#5191FA',
            'boxColor' => isset($card_settings['rating']['boxColor']) ? sanitize_hex_color($card_settings['rating']['boxColor']) : '#ffffff',
            'boxFontSize' => isset($card_settings['rating']['boxFontSize']) ? intval($card_settings['rating']['boxFontSize']) : 11,
            'boxPaddingX' => isset($card_settings['rating']['boxPaddingX']) ? intval($card_settings['rating']['boxPaddingX']) : 6,
            'boxPaddingY' => isset($card_settings['rating']['boxPaddingY']) ? intval($card_settings['rating']['boxPaddingY']) : 2,
            'boxRadius' => isset($card_settings['rating']['boxRadius']) ? intval($card_settings['rating']['boxRadius']) : 3,
            'labelColor' => isset($card_settings['rating']['labelColor']) ? sanitize_hex_color($card_settings['rating']['labelColor']) : '#333333',
            'labelFontSize' => isset($card_settings['rating']['labelFontSize']) ? intval($card_settings['rating']['labelFontSize']) : 12,
            'countColor' => isset($card_settings['rating']['countColor']) ? sanitize_hex_color($card_settings['rating']['countColor']) : '#999999',
            'countFontSize' => isset($card_settings['rating']['countFontSize']) ? intval($card_settings['rating']['countFontSize']) : 10,
        ];

        // Tags
        $s['tags'] = [
             'marginTop' => isset($card_settings['tags']['marginTop']) ? intval($card_settings['tags']['marginTop']) : 7,
             'gap' => isset($card_settings['tags']['gap']) ? intval($card_settings['tags']['gap']) : 5,
             'fontSize' => isset($card_settings['tags']['fontSize']) ? intval($card_settings['tags']['fontSize']) : 11,
             'paddingX' => isset($card_settings['tags']['paddingX']) ? intval($card_settings['tags']['paddingX']) : 6,
             'paddingY' => isset($card_settings['tags']['paddingY']) ? intval($card_settings['tags']['paddingY']) : 2,
             'radius' => isset($card_settings['tags']['radius']) ? intval($card_settings['tags']['radius']) : 3,
        ];

        // Divider
        $s['divider'] = [
             'marginTop' => isset($card_settings['divider']['marginTop']) ? floatval($card_settings['divider']['marginTop']) : 9.5,
             'marginBottom' => isset($card_settings['divider']['marginBottom']) ? floatval($card_settings['divider']['marginBottom']) : 7.5,
             'color' => isset($card_settings['divider']['color']) ? sanitize_hex_color($card_settings['divider']['color']) : '#EEEEEE',
        ];

        // Price Section
        $s['price'] = [
            'fromColor' => isset($card_settings['price']['fromColor']) ? sanitize_hex_color($card_settings['price']['fromColor']) : '#999999',
            'fromSize' => isset($card_settings['price']['fromSize']) ? intval($card_settings['price']['fromSize']) : 12,
            'amountColor' => isset($card_settings['price']['amountColor']) ? sanitize_hex_color($card_settings['price']['amountColor']) : '#00BAA4',
            'amountSize' => isset($card_settings['price']['amountSize']) ? intval($card_settings['price']['amountSize']) : 16,
            'amountWeight' => isset($card_settings['price']['amountWeight']) ? sanitize_text_field($card_settings['price']['amountWeight']) : '700',
            'nightColor' => isset($card_settings['price']['nightColor']) ? sanitize_hex_color($card_settings['price']['nightColor']) : '#555555',
            'nightSize' => isset($card_settings['price']['nightSize']) ? intval($card_settings['price']['nightSize']) : 13,
            'originalColor' => isset($card_settings['price']['originalColor']) ? sanitize_hex_color($card_settings['price']['originalColor']) : '#999999',
            'originalSize' => isset($card_settings['price']['originalSize']) ? intval($card_settings['price']['originalSize']) : 12,
        ];

        return $s;
    }
     // --- END: Added specific card sanitization ---
}
endif;
