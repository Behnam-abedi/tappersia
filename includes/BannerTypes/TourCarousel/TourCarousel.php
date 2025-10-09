<?php
// tappersia/includes/BannerTypes/TourCarousel/TourCarousel.php

if (!class_exists('Yab_Tour_Carousel')) :
class Yab_Tour_Carousel {

    public function save($banner_data) {
        if (empty($banner_data['name'])) {
            wp_send_json_error(['message' => 'Banner name is required.']);
            return;
        }

        if (isset($banner_data['tour_carousel'])) {
            $tour_carousel = $banner_data['tour_carousel'];
            $settings = $tour_carousel['settings'] ?? [];
            $selected_tours_count = isset($tour_carousel['selectedTours']) ? count($tour_carousel['selectedTours']) : 0;
            $slides_per_view = $settings['slidesPerView'] ?? 3;
            $loop = $settings['loop'] ?? false;
            $is_doubled = $settings['isDoubled'] ?? false;

            if ($is_doubled && !$loop) {
                if ($selected_tours_count % 2 !== 0) {
                    wp_send_json_error(['message' => "Validation failed: The number of tours for a double carousel must be an even number. You have selected {$selected_tours_count} tours."], 400);
                    return;
                }
                $max_slides = ($selected_tours_count > 0 && $selected_tours_count < 8) ? floor($selected_tours_count / 2) : 4;
                if ($slides_per_view > $max_slides) {
                    wp_send_json_error(['message' => "Validation failed: With {$selected_tours_count} tours, the maximum 'Slides Per View' for a double carousel is {$max_slides}."], 400);
                    return;
                }
            }

            if (!$loop && !$is_doubled && $selected_tours_count > 0 && $selected_tours_count < $slides_per_view) {
                 wp_send_json_error(['message' => "Validation failed: You need at least {$slides_per_view} tours to match 'Slides Per View' when loop is disabled."], 400);
                return;
            }
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
        return ['has_conflict' => false, 'message' => ''];
    }

    private function sanitize_banner_data($data) {
        $sanitized = [];
        if (!is_array($data)) return $sanitized;
    
        foreach ($data as $key => $value) {
            if ($key === 'tour_carousel' && is_array($value)) {
                $sanitized['tour_carousel'] = [];
                $sanitized['tour_carousel']['selectedTours'] = isset($value['selectedTours']) ? array_map('intval', $value['selectedTours']) : [];
                $sanitized['tour_carousel']['updateCounter'] = isset($value['updateCounter']) ? intval($value['updateCounter']) : 0;

                if (isset($value['settings']) && is_array($value['settings'])) {
                    $settings = $value['settings'];
                    $sanitized['tour_carousel']['settings'] = [
                        'slidesPerView' => isset($settings['slidesPerView']) ? intval($settings['slidesPerView']) : 3,
                        'loop' => isset($settings['loop']) ? boolval($settings['loop']) : false,
                        'spaceBetween' => isset($settings['spaceBetween']) ? intval($settings['spaceBetween']) : 22,
                        'isDoubled' => isset($settings['isDoubled']) ? boolval($settings['isDoubled']) : false,
                        'gridFill' => isset($settings['gridFill']) ? sanitize_text_field($settings['gridFill']) : 'column',
                        'direction' => isset($settings['direction']) ? sanitize_text_field($settings['direction']) : 'ltr',
                        
                        'header' => [
                            'text' => isset($settings['header']['text']) ? sanitize_text_field($settings['header']['text']) : 'Top Iran Tours',
                            'fontSize' => isset($settings['header']['fontSize']) ? intval($settings['header']['fontSize']) : 24,
                            'fontWeight' => isset($settings['header']['fontWeight']) ? sanitize_text_field($settings['header']['fontWeight']) : '700',
                            'color' => isset($settings['header']['color']) ? sanitize_hex_color($settings['header']['color']) : '#000000',
                            'lineColor' => isset($settings['header']['lineColor']) ? sanitize_hex_color($settings['header']['lineColor']) : '#00BAA4',
                            'marginTop' => isset($settings['header']['marginTop']) ? intval($settings['header']['marginTop']) : 28,
                        ],
                        
                        'autoplay' => [
                            'enabled' => isset($settings['autoplay']['enabled']) ? boolval($settings['autoplay']['enabled']) : false,
                            'delay' => isset($settings['autoplay']['delay']) ? intval($settings['autoplay']['delay']) : 3000,
                        ],
                        'navigation' => [ 'enabled' => isset($settings['navigation']['enabled']) ? boolval($settings['navigation']['enabled']) : true ],
                        'pagination' => [ 'enabled' => isset($settings['pagination']['enabled']) ? boolval($settings['pagination']['enabled']) : true ],
                    ];
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
}
endif;