<?php
// /includes/updater/class-yab-updater-checker.php
defined('ABSPATH') || exit;

/**
 * The main Update Checker class.
 * Hooks into WordPress to check for updates and present them to the user.
 */
class Yab_Updater_Checker {

    /** @var string The full path to the main plugin file. */
    protected $plugin_file;

    /** @var string The plugin slug (e.g., "tappersia/tappersia-main.php"). */
    protected $plugin_slug;

    /** @var string The current installed version. */
    protected $current_version;

    /** @var Yab_Updater_Client The API client. */
    protected $client;

    /** @var Yab_Updater_Validator The data validator. */
    protected $validator;

    /** @var stdClass|WP_Error|null Cached validated data for this request. */
    private $validated_response = null;

    /**
     * Constructor.
     *
     * @param string $plugin_file Full path to the main plugin file.
     * @param string $current_version The current plugin version.
     * @param Yab_Updater_Client $client The API client instance.
     * @param Yab_Updater_Validator $validator The validator instance.
     */
    public function __construct( $plugin_file, $current_version, Yab_Updater_Client $client, Yab_Updater_Validator $validator ) {
        $this->plugin_file     = $plugin_file;
        $this->plugin_slug     = plugin_basename( $plugin_file );
        $this->current_version = $current_version;
        $this->client          = $client;
        $this->validator       = $validator;
    }

    /**
     * Registers the necessary WordPress hooks.
     */
    public function init() {
        add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_for_updates' ] );
        add_filter( 'plugins_api', [ $this, 'plugin_information' ], 20, 3 );
        add_action( 'upgrader_process_complete', [ $this, 'after_update' ], 10, 2 );
    }

    /**
     * Hooks into the update check transient to inject our update info.
     *
     * @param stdClass $transient The WordPress update transient.
     * @return stdClass The modified transient.
     */
    public function check_for_updates( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $validated_data = $this->get_validated_data();
        if ( is_wp_error( $validated_data ) || ! $validated_data ) {
            // Log error if needed, but don't break the update process
            // error_log('Tappersia Updater Error: ' . $validated_data->get_error_message());
            return $transient;
        }

        // Compare versions
        if ( version_compare( $this->current_version, $validated_data->version, '<' ) ) {
            $update_obj = new stdClass();
            $update_obj->slug          = $this->plugin_slug;
            $update_obj->plugin        = $this->plugin_slug;
            $update_obj->new_version   = $validated_data->version;
            $update_obj->url           = $validated_data->homepage;
            $update_obj->package       = $validated_data->download_url;
            $update_obj->tested        = $validated_data->tested;
            $update_obj->requires      = $validated_data->requires;
            $update_obj->requires_php  = $validated_data->requires_php;
            
            $transient->response[ $this->plugin_slug ] = $update_obj;
        }

        return $transient;
    }

    /**
     * Hooks into the "View details" popup.
     *
     * @param bool|object $result The current result.
     * @param string      $action The action being performed.
     * @param object      $args   Arguments for the request.
     * @return bool|object The modified result.
     */
    public function plugin_information( $result, $action, $args ) {
        // Check if this is for our plugin
        if ( 'plugin_information' !== $action || empty( $args->slug ) || $args->slug !== $this->plugin_slug ) {
            return $result;
        }

        $validated_data = $this->get_validated_data();
        if ( is_wp_error( $validated_data ) || ! $validated_data ) {
            return $result; // Return original result (false)
        }

        $plugin_data = $this->get_plugin_data();

        $result = new stdClass();
        $result->name          = $plugin_data['Name'];
        $result->slug          = $this->plugin_slug;
        $result->version       = $validated_data->version;
        $result->author        = $plugin_data['Author'];
        $result->homepage      = $validated_data->homepage;
        $result->download_link = $validated_data->download_url;
        $result->tested        = $validated_data->tested;
        $result->requires      = $validated_data->requires;
        $result->requires_php  = $validated_data->requires_php;
        $result->last_updated  = $validated_data->last_updated;
        $result->sections      = (array) $validated_data->sections; // Must be an array

        return $result;
    }

    /**
     * Flushes rewrite rules after an update.
     *
     * @param WP_Upgrader $upgrader WP_Upgrader instance.
     * @param array       $options  Array of update options.
     */
    public function after_update( $upgrader, $options ) {
        if ( $options['action'] === 'update' && $options['type'] === 'plugin' && isset( $options['plugins'] ) ) {
            if ( in_array( $this->plugin_slug, $options['plugins'] ) ) {
                // Plugin was just updated. Flush rewrite rules, like the activator.
                flush_rewrite_rules();
            }
        }
    }

    /**
     * Helper to get validated data, caching it for the current request.
     *
     * @return stdClass|WP_Error
     */
    private function get_validated_data() {
        if ( null === $this->validated_response ) {
            $raw_data = $this->client->get_update_data();
            $this->validated_response = $this->validator->validate_metadata( $raw_data );
        }
        return $this->validated_response;
    }

    /**
     * Helper to get plugin data from the main file.
     *
     * @return array
     */
    private function get_plugin_data() {
        if ( ! function_exists( 'get_plugin_data' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        return get_plugin_data( $this->plugin_file );
    }
}