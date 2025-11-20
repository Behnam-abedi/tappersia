<?php
// tappersia/includes/license/class-yab-license-validator.php
defined('ABSPATH') || exit;

require_once YAB_PLUGIN_DIR . 'includes/license/class-yab-api-client.php';

class Yab_License_Validator {

    private $api_client;

    public function __construct() {
        $this->api_client = new Yab_Api_Client();
    }

    /**
     * Validates a new API key.
     * @param string $api_key
     * @return array ['success' => bool, 'status' => 'valid'|'invalid', 'message' => string, ...]
     */
    public function validate_license_key($api_key) {
        $response = $this->api_client->send_validation_request($api_key);

        if (is_wp_error($response)) {
            return ['success' => false, 'status' => 'invalid', 'message' => $response->get_error_message()];
        }

        // Handle expected API response
        if (isset($response['success']) && $response['success'] === true) {
            if (isset($response['valid']) && $response['valid'] === true) {
                return [
                    'success' => true,
                    'status' => 'valid',
                    'message' => $response['message'] ?? 'License is valid.',
                    'expires_at' => $response['expires_at'] ?? null,
                    'customer_info' => $response['customer_info'] ?? null
                ];
            } else {
                return ['success' => false, 'status' => 'invalid', 'message' => $response['message'] ?? 'License is not valid.'];
            }
        }

        return ['success' => false, 'status' => 'invalid', 'message' => 'An unknown error occurred during validation.'];
    }

    // You can add check_license_status() here for periodic background checks
}