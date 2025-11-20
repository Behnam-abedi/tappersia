<?php
// tappersia/includes/license/class-yab-license-manager.php
defined('ABSPATH') || exit;

require_once YAB_PLUGIN_DIR . 'includes/license/class-yab-license-validator.php';

class Yab_License_Manager {

    const LICENSE_KEY_OPTION = 'yab_license_key';
    const LICENSE_STATUS_OPTION = 'yab_license_status';
    const NEEDS_ACTIVATION_OPTION = 'yab_needs_activation_redirect';

    private $validator;

    public function __construct() {
        $this->validator = new Yab_License_Validator();
    }

    /**
     * Gets the stored API key.
     * @return string|null
     */
    public function get_api_key() {
        return get_option(self::LICENSE_KEY_OPTION, null);
    }

    /**
     * Gets the stored license status data.
     * @return array Default to 'invalid' status.
     */
    public function get_license_status() {
        return get_option(self::LICENSE_STATUS_OPTION, [
            'status' => 'invalid',
            'last_checked' => 0,
            'message' => 'License has not been activated.'
        ]);
    }

    /**
     * Checks if the license is currently considered valid.
     * @return bool
     */
    public function is_license_valid() {
        $status_data = $this->get_license_status();
        
        if ($status_data['status'] === 'valid') {
            // Optional: Add periodic check here as per requirements.
            // For now, a simple 'valid' check is sufficient.
            // e.g., if (time() - $status_data['last_checked'] > DAY_IN_SECONDS) {
            //    return $this->check_and_update_status();
            // }
            return true;
        }
        return false;
    }

    /**
     * Attempts to activate a new license key.
     * @param string $api_key The key to validate.
     * @return array ['success' => bool, 'message' => string]
     */
    public function activate_license($api_key) {
        $result = $this->validator->validate_license_key($api_key);

        if ($result['success'] && $result['status'] === 'valid') {
            update_option(self::LICENSE_KEY_OPTION, $api_key);
            update_option(self::LICENSE_STATUS_OPTION, [
                'status' => 'valid',
                'last_checked' => time(),
                'expires_at' => $result['expires_at'] ?? null,
                'message' => $result['message'] ?? 'License activated.'
            ]);
            delete_option(self::NEEDS_ACTIVATION_OPTION); // Clear redirect flag
            return ['success' => true, 'message' => $result['message'] ?? 'License activated successfully.'];
        } else {
            // Clear any old data if activation fails
            $this->deactivate_license();
            return ['success' => false, 'message' => $result['message'] ?? 'Invalid API key or validation failed.'];
        }
    }

    /**
     * Deactivates the current license from the site.
     */
    public function deactivate_license() {
        delete_option(self::LICENSE_KEY_OPTION);
        delete_option(self::LICENSE_STATUS_OPTION);
        // Set redirect flag for next time
        update_option(self::NEEDS_ACTIVATION_OPTION, true); 
        
        // Optional: Call validator/api_client to notify remote server of deactivation
        // $this->validator->deactivate_license_key($this->get_api_key());
    }

    /**
     * Checks if the plugin is in a state that requires activation.
     * @return bool
     */
    public function needs_activation_flow() {
        // True if no valid status is stored
        return !$this->is_license_valid();
    }
}