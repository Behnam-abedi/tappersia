<?php
// /includes/updater/class-yab-updater-factory.php
defined('ABSPATH') || exit;

/**
 * Factory for creating the update checker and its dependencies.
 * This adheres to SOLID principles by separating creation logic from Yab_Main.
 */
class Yab_Updater_Factory {

    /**
     * Builds the complete updater system.
     *
     * @param string $plugin_file     The full path to the main plugin file.
     * @param string $current_version The current plugin version.
     * @return Yab_Updater_Checker An initialized instance of the checker.
     */
    public static function build( $plugin_file, $current_version ) {
        // Load dependencies
        require_once YAB_PLUGIN_DIR . 'includes/updater/class-yab-updater-client.php';
        require_once YAB_PLUGIN_DIR . 'includes/updater/class-yab-updater-validator.php';
        require_once YAB_PLUGIN_DIR . 'includes/updater/class-yab-updater-checker.php';

        // Inject dependencies
        $client    = new Yab_Updater_Client( $plugin_file );
        $validator = new Yab_Updater_Validator();
        $checker   = new Yab_Updater_Checker( $plugin_file, $current_version, $client, $validator );

        return $checker;
    }
}