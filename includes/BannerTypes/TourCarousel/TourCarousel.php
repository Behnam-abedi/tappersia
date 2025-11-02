<?php
// tappersia/includes/BannerTypes/TourCarousel/TourCarousel.php

if (!class_exists('Yab_Tour_Carousel')) :
class Yab_Tour_Carousel {

    public function save($banner_data) {
        if (empty($banner_data['name'])) {
            wp_send_json_error(['message' => 'Banner name is required.']);
            return;
        }

        // --- Desktop Validation ---
        if (isset($banner_data['tour_carousel']['settings'])) {
            $desktop_settings = $banner_data['tour_carousel']['settings'];
            $tour_count = count($banner_data['tour_carousel']['selectedTours'] ?? []);
            
            if (($desktop_settings['isDoubled'] ?? false) && !($desktop_settings['loop'] ?? false) && $tour_count % 2 !== 0) {
                wp_send_json_error(['message' => "Desktop validation failed: Double carousel requires an even number of tours. You have {$tour_count}."], 400);
                return;
            }
             if (!($desktop_settings['loop'] ?? false) && !($desktop_settings['isDoubled'] ?? false) && $tour_count > 0 && $tour_count < ($desktop_settings['slidesPerView'] ?? 3)) {
                wp_send_json_error(['message' => "Desktop validation failed: You need at least {$desktop_settings['slidesPerView']} tours when loop is disabled."], 400);
                return;
            }
        }
        
        // --- Mobile Validation ---
        if (isset($banner_data['tour_carousel']['settings_mobile'])) {
            $mobile_settings = $banner_data['tour_carousel']['settings_mobile'];
            $tour_count = count($banner_data['tour_carousel']['selectedTours'] ?? []);

            if (($mobile_settings['isDoubled'] ?? false) && !($mobile_settings['loop'] ?? false) && $tour_count % 2 !== 0) {
                wp_send_json_error(['message' => "Mobile validation failed: Double carousel requires an even number of tours. You have {$tour_count}."], 400);
                return;
            }
        }


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
            update_post_meta($result, '_yab_banner_type', 'tour-carousel');
            update_post_meta($result, '_yab_display_method', $sanitized_data['displayMethod']);
            update_post_meta($result, '_yab_is_active', $sanitized_data['isActive']);
            wp_send_json_success(['message' => 'Carousel saved successfully!', 'banner_id' => $result]);
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
                ['key' => '_yab_banner_type', 'value' => 'tour-carousel', 'compare' => '=']
            ],
            'post__not_in' => $current_banner_id ? [intval($current_banner_id)] : [],
        ];

        $other_banners_query = new WP_Query($args);

        foreach ($other_banners_query->posts as $banner_post) {
            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
            if (empty($data) || empty($data['displayOn'])) continue;

            $other_post_ids = !empty($data['displayOn']['posts']) ? array_map('intval', $data['displayOn']['posts']) : [];
            if (!empty(array_intersect($post_ids, $other_post_ids))) {
                return ['has_conflict' => true, 'message' => 'A Tour Carousel is already assigned to one of the selected posts.'];
            }

            $other_page_ids = !empty($data['displayOn']['pages']) ? array_map('intval', $data['displayOn']['pages']) : [];
            if (!empty(array_intersect($page_ids, $other_page_ids))) {
                 return ['has_conflict' => true, 'message' => 'A Tour Carousel is already assigned to one of the selected pages.'];
            }
        }
        return $conflict;
    }

    private function sanitize_settings_object($settings) {
        $sanitized = [];
        $sanitized['slidesPerView'] = isset($settings['slidesPerView']) ? intval($settings['slidesPerView']) : 3;
        $sanitized['loop'] = isset($settings['loop']) ? boolval($settings['loop']) : false;
        $sanitized['spaceBetween'] = isset($settings['spaceBetween']) ? intval($settings['spaceBetween']) : 22;
        $sanitized['isDoubled'] = isset($settings['isDoubled']) ? boolval($settings['isDoubled']) : false;
        $sanitized['gridFill'] = isset($settings['gridFill']) ? sanitize_text_field($settings['gridFill']) : 'column';
        $sanitized['direction'] = isset($settings['direction']) ? sanitize_text_field($settings['direction']) : 'ltr';
        
        $sanitized['header'] = [
            'text' => isset($settings['header']['text']) ? sanitize_text_field($settings['header']['text']) : 'Top Iran Tours',
            'fontSize' => isset($settings['header']['fontSize']) ? intval($settings['header']['fontSize']) : 24,
            'fontWeight' => isset($settings['header']['fontWeight']) ? sanitize_text_field($settings['header']['fontWeight']) : '700',
            'color' => isset($settings['header']['color']) ? sanitize_hex_color($settings['header']['color']) : '#000000',
            'lineColor' => isset($settings['header']['lineColor']) ? sanitize_hex_color($settings['header']['lineColor']) : '#00BAA4',
            'marginTop' => isset($settings['header']['marginTop']) ? intval($settings['header']['marginTop']) : 28,
        ];

        $sanitized['card'] = $this->sanitize_card_settings($settings['card'] ?? []);
        
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

        return $sanitized;
    }

    private function sanitize_banner_data($data) {
        $sanitized = [];
        if (!is_array($data)) return $sanitized;
    
        foreach ($data as $key => $value) {
            if ($key === 'tour_carousel' && is_array($value)) {
                $sanitized['tour_carousel'] = [];
                $sanitized['tour_carousel']['selectedTours'] = isset($value['selectedTours']) ? array_map('intval', $value['selectedTours']) : [];
                $sanitized['tour_carousel']['updateCounter'] = isset($value['updateCounter']) ? intval($value['updateCounter']) : 0;
                $sanitized['tour_carousel']['isMobileConfigured'] = isset($value['isMobileConfigured']) ? boolval($value['isMobileConfigured']) : false;

                if (isset($value['settings']) && is_array($value['settings'])) {
                    $sanitized['tour_carousel']['settings'] = $this->sanitize_settings_object($value['settings']);
                }
                 if (isset($value['settings_mobile']) && is_array($value['settings_mobile'])) {
                    $sanitized['tour_carousel']['settings_mobile'] = $this->sanitize_settings_object($value['settings_mobile']);
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

     private function sanitize_card_settings($card_settings) {
        $sanitized = [];
        $sanitized['height'] = isset($card_settings['height']) ? intval($card_settings['height']) : 375;
        $sanitized['backgroundType'] = isset($card_settings['backgroundType']) ? sanitize_text_field($card_settings['backgroundType']) : 'solid';
        $sanitized['bgColor'] = isset($card_settings['bgColor']) ? sanitize_text_field($card_settings['bgColor']) : '#FFFFFF';
        $sanitized['gradientAngle'] = isset($card_settings['gradientAngle']) ? intval($card_settings['gradientAngle']) : 90;
        $sanitized['gradientStops'] = isset($card_settings['gradientStops']) ? array_map(function($stop) {
            return [
                'color' => isset($stop['color']) ? sanitize_text_field($stop['color']) : '#FFFFFF',
                'stop' => isset($stop['stop']) ? intval($stop['stop']) : 0,
            ];
        }, $card_settings['gradientStops']) : [];
        $sanitized['borderWidth'] = isset($card_settings['borderWidth']) ? intval($card_settings['borderWidth']) : 1;
        $sanitized['borderColor'] = isset($card_settings['borderColor']) ? sanitize_hex_color($card_settings['borderColor']) : '#ebebeb';
        $sanitized['borderRadius'] = isset($card_settings['borderRadius']) ? intval($card_settings['borderRadius']) : 14;
        $sanitized['padding'] = isset($card_settings['padding']) ? intval($card_settings['padding']) : 9;
        $sanitized['imageHeight'] = isset($card_settings['imageHeight']) ? intval($card_settings['imageHeight']) : 204;
        
        $text_elements = ['province', 'title', 'price', 'duration', 'rating', 'reviews', 'button'];
        foreach ($text_elements as $el) {
            $sanitized[$el] = [
                'fontSize' => isset($card_settings[$el]['fontSize']) ? intval($card_settings[$el]['fontSize']) : 14,
                'fontWeight' => isset($card_settings[$el]['fontWeight']) ? sanitize_text_field($card_settings[$el]['fontWeight']) : '400',
                'color' => isset($card_settings[$el]['color']) ? sanitize_text_field($card_settings[$el]['color']) : '#000000',
            ];
        }

        $sanitized['province']['bgColor'] = isset($card_settings['province']['bgColor']) ? sanitize_text_field($card_settings['province']['bgColor']) : 'rgba(14,14,14,0.2)';
        $sanitized['province']['blur'] = isset($card_settings['province']['blur']) ? intval($card_settings['province']['blur']) : 3;
        $sanitized['province']['bottom'] = isset($card_settings['province']['bottom']) ? intval($card_settings['province']['bottom']) : 9;
        $sanitized['province']['side'] = isset($card_settings['province']['side']) ? intval($card_settings['province']['side']) : 11;
        $sanitized['title']['lineHeight'] = isset($card_settings['title']['lineHeight']) ? floatval($card_settings['title']['lineHeight']) : 1.5;
        $sanitized['button']['bgColor'] = isset($card_settings['button']['bgColor']) ? sanitize_hex_color($card_settings['button']['bgColor']) : '#00BAA4';
        $sanitized['button']['BgHoverColor'] = isset($card_settings['button']['BgHoverColor']) ? sanitize_hex_color($card_settings['button']['BgHoverColor']) : '#008a7b';
        $sanitized['button']['arrowSize'] = isset($card_settings['button']['arrowSize']) ? intval($card_settings['button']['arrowSize']) : 10;
        
        return $sanitized;
    }
}
endif;