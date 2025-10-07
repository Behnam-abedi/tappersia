<?php
// tappersia/includes/BannerTypes/TourCarousel/TourCarousel.php

if (!class_exists('Yab_Tour_Carousel')) :
class Yab_Tour_Carousel {

    public function save($banner_data) {
        if (empty($banner_data['name'])) {
            wp_send_json_error(['message' => 'Banner name is required.']);
            return;
        }

        // Server-side validation for tour carousel settings
        if (isset($banner_data['tour_carousel'])) {
            $tour_carousel = $banner_data['tour_carousel'];
            $settings = $tour_carousel['settings'] ?? [];
            $selected_tours_count = isset($tour_carousel['selectedTours']) ? count($tour_carousel['selectedTours']) : 0;
            $slides_per_view = $settings['slidesPerView'] ?? 3;
            $loop = $settings['loop'] ?? false;

            if ($loop && $selected_tours_count > 0 && $selected_tours_count <= $slides_per_view) {
                wp_send_json_error(['message' => "To enable loop, you need more tours than 'Slides Per View'. Please add more tours or disable loop."], 400);
                return;
            }

            if (!$loop && $selected_tours_count > 0 && $selected_tours_count < $slides_per_view) {
                 wp_send_json_error(['message' => "You need at least {$slides_per_view} tours to match 'Slides Per View'. Please add more tours."], 400);
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
                ['key' => '_yab_display_method', 'value' => 'Fixed'],
                ['key' => '_yab_banner_type', 'value' => 'tour-carousel']
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


    private function sanitize_banner_data($data) {
        $sanitized = [];
        if (!is_array($data)) {
            return $sanitized;
        }
    
        foreach ($data as $key => $value) {
            if ($key === 'tour_carousel' && is_array($value)) {
                $sanitized['tour_carousel'] = [];
                
                if (isset($value['selectedTours']) && is_array($value['selectedTours'])) {
                    $sanitized['tour_carousel']['selectedTours'] = array_map('intval', $value['selectedTours']);
                } else {
                    $sanitized['tour_carousel']['selectedTours'] = [];
                }

                if (isset($value['settings']) && is_array($value['settings'])) {
                    $settings = $value['settings'];
                    $sanitized['tour_carousel']['settings'] = [
                        'slidesPerView' => isset($settings['slidesPerView']) ? intval($settings['slidesPerView']) : 3,
                        'loop' => isset($settings['loop']) ? boolval($settings['loop']) : false,
                        'spaceBetween' => isset($settings['spaceBetween']) ? intval($settings['spaceBetween']) : 22,
                        'pagination' => isset($settings['pagination']) && is_array($settings['pagination']) ? $settings['pagination'] : ['el' => '.swiper-pagination', 'clickable' => true],
                        'navigation' => isset($settings['navigation']) && is_array($settings['navigation']) ? $settings['navigation'] : ['nextEl' => '.tappersia-carusel-next', 'prevEl' => '.tappersia-carusel-perv'],
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