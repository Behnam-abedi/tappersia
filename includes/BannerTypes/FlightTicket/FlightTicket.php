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
    
    // --- START: Updated sanitize_banner_data ---
    private function sanitize_banner_data($data) {
        $sanitized = [];
        if (!is_array($data)) return $sanitized;
    
        foreach ($data as $key => $value) {
            if ($key === 'flight_ticket' && is_array($value)) {
                // Sanitize the flight_ticket part
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
                    'isMobileConfigured' => isset($value['isMobileConfigured']) ? boolval($value['isMobileConfigured']) : false, // <<< ADDED
                ];
                
                // Sanitize the 'design' object
                if (isset($value['design']) && is_array($value['design'])) {
                    $sanitized['flight_ticket']['design'] = $this->sanitize_design_part($value['design']);
                }

                // +++ START: ADDED mobile design sanitization +++
                if (isset($value['design_mobile']) && is_array($value['design_mobile'])) {
                    $sanitized['flight_ticket']['design_mobile'] = $this->sanitize_design_part($value['design_mobile']);
                }
                // +++ END: ADDED mobile design sanitization +++

            } elseif (is_array($value)) {
                // Recursively sanitize other arrays (like displayOn)
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
    // --- END: Updated sanitize_banner_data ---
    
    // --- START: Added sanitize_design_part (based on SingleBanner) ---
    private function sanitize_design_part($design_part) {
        $sanitized = [];
        foreach ($design_part as $key => $value) {
             if (is_array($value)) {
                // Handle nested objects like content1, content2, price, button, etc.
                // And gradientStops
                $sanitized[$key] = $this->sanitize_design_part($value);
            } elseif (is_bool($value)) {
                $sanitized[$key] = $value;
            } elseif (is_numeric($value) || $value === null) {
                $sanitized[$key] = $value;
            } else {
                switch ($key) {
                    case 'imageUrl':
                        $sanitized[$key] = esc_url_raw(trim($value));
                        break;
                    case 'text': // For content1, content2, content3
                        $sanitized[$key] = sanitize_text_field(trim($value));
                        break;
                    case 'bgColor':
                    case 'borderColor':
                    case 'color': // For text colors
                    // case 'bgColor': // For button bg - Duplicated, removed one
                    case 'BgHoverColor': // <<< افزوده شد
                        $sanitized[$key] = sanitize_text_field($value); // Use sanitize_text_field to allow rgba
                        break;
                    case 'widthUnit':
                    case 'minHeightUnit':
                    case 'imageWidthUnit':
                    case 'imageHeightUnit':
                         $sanitized[$key] = in_array($value, ['px', '%']) ? $value : 'px';
                        break;
                    default:
                        $sanitized[$key] = sanitize_text_field(trim($value));
                }
            }
        }
        return $sanitized;
    }
    // --- END: Added sanitize_design_part ---
}
endif;