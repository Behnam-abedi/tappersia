<?php
// /includes/updater/class-yab-updater-validator.php
defined('ABSPATH') || exit;

/**
 * Handles validation and sanitization of the API response.
 * Its single responsibility is to ensure data is safe and in the correct format.
 */
class Yab_Updater_Validator {

    /**
     * Validates and sanitizes the raw metadata object.
     *
     * @param stdClass|WP_Error $data The raw data from the client.
     * @return stdClass|WP_Error A sanitized data object or WP_Error on failure.
     */
    public function validate_metadata( $data ) {
        if ( is_wp_error( $data ) ) {
            return $data; // Pass through WP_Error
        }

        if ( ! is_object( $data ) ) {
            return new WP_Error( 'validator_invalid_data', 'Update data is not an object.' );
        }

        // --- Required Field Checks ---
        if ( ! isset( $data->version ) || ! version_compare( $data->version, '0', '>' ) ) {
            return new WP_Error( 'validator_invalid_version', 'Invalid or missing "version" field.' );
        }

        if ( ! isset( $data->download_url ) || filter_var( $data->download_url, FILTER_VALIDATE_URL ) === false ) {
            return new WP_Error( 'validator_invalid_url', 'Invalid or missing "download_url" field.' );
        }

        if ( ! isset( $data->sections ) || ! is_object( $data->sections ) ) {
            return new WP_Error( 'validator_invalid_sections', 'Invalid or missing "sections" object.' );
        }

        // --- Sanitize and Build Validated Object ---
        $validated_data = new stdClass();
        $validated_data->version      = sanitize_text_field( $data->version );
        $validated_data->download_url = esc_url_raw( $data->download_url );
        $validated_data->tested       = isset( $data->tested ) ? sanitize_text_field( $data->tested ) : '';
        $validated_data->requires     = isset( $data->requires ) ? sanitize_text_field( $data->requires ) : '';
        $validated_data->requires_php = isset( $data->requires_php ) ? sanitize_text_field( $data->requires_php ) : '';
        $validated_data->last_updated = isset( $data->last_updated ) ? sanitize_text_field( $data->last_updated ) : '';
        $validated_data->homepage     = isset( $data->homepage ) ? esc_url_raw( $data->homepage ) : '';

        // Sanitize sections (allows basic HTML)
        $validated_data->sections = new stdClass();
        $validated_data->sections->description = isset( $data->sections->description ) ? wp_kses_post( $data->sections->description ) : '';
        $validated_data->sections->changelog   = isset( $data->sections->changelog ) ? wp_kses_post( $data->sections->changelog ) : '';
        
        return $validated_data;
    }
}