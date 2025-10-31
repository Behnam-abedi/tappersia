<?php
// tappersia/includes/BannerTypes/FlightTicket/FlightTicket.php

if (!class_exists('Yab_Flight_Ticket')) :
class Yab_Flight_Ticket {
    
    public function save($banner_data) {
        if (empty($banner_data['name'])) {
            wp_send_json_error(['message' => 'Banner name is required.']);
            return;
        }

        // Add any specific validation for flight ticket banner here in the future.

        if (($banner_data['displayMethod'] ?? 'Embeddable') === 'Fixed') {
            // Conflict check logic can be added here if needed
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
            update_post_meta($result, '_yab_banner_type', 'flight-ticket');
            update_post_meta($result, '_yab_display_method', $sanitized_data['displayMethod']);
            update_post_meta($result, '_yab_is_active', $sanitized_data['isActive']);
            
            wp_send_json_success(['message' => 'Banner saved successfully!', 'banner_id' => $result]);
        }

        wp_die();
    }
    
    private function sanitize_banner_data($data) {
        $sanitized = [];
        if (!is_array($data)) return $sanitized;
    
        foreach ($data as $key => $value) {
            if ($key === 'flight_ticket' && is_array($value)) {
                // START: Updated logic to save full airport objects
                $sanitized['flight_ticket'] = [
                    'from' => isset($value['from']) && is_array($value['from']) ? [
                        'countryName' => isset($value['from']['countryName']) ? sanitize_text_field($value['from']['countryName']) : null,
                        'city' => isset($value['from']['city']) ? sanitize_text_field($value['from']['city']) : null,
                        'iataCode' => isset($value['from']['iataCode']) ? sanitize_text_field($value['from']['iataCode']) : null,
                    ] : null,
                    'to' => isset($value['to']) && is_array($value['to']) ? [
                        'countryName' => isset($value['to']['countryName']) ? sanitize_text_field($value['to']['countryName']) : null,
                        'city' => isset($value['to']['city']) ? sanitize_text_field($value['to']['city']) : null,
                        'iataCode' => isset($value['to']['iataCode']) ? sanitize_text_field($value['to']['iataCode']) : null,
                    ] : null,
                ];
                // END: Updated logic
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