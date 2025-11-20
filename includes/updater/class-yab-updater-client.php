<?php
// /includes/updater/class-yab-updater-client.php
defined('ABSPATH') || exit;

/**
 * Handles communication with the GitHub repository.
 * Its single responsibility is to fetch and cache the update data.
 */
class Yab_Updater_Client {

    /** @var string The URL to the update-info.json file. */
    private $metadata_url;

    /** @var string The transient cache key. */
    private $transient_key;

    /**
     * Constructor.
     *
     * @param string $plugin_file Full path to the main plugin file.
     */
    public function __construct( $plugin_file ) {
        $headers = [
            'GitHub Plugin URI' => 'GitHub Plugin URI',
            'GitHub Branch'     => 'GitHub Branch',
        ];

        // Get GitHub data from the plugin's file headers
        $file_data = get_file_data( $plugin_file, $headers );

        $github_uri = $file_data['GitHub Plugin URI'] ?? '';
        $branch     = $file_data['GitHub Branch'] ?? 'main';

        if ( empty( $github_uri ) ) {
            return;
        }

        $this->metadata_url  = "https://raw.githubusercontent.com/{$github_uri}/{$branch}/update-info.json";
        $this->transient_key = 'yab_updater_cache_' . md5( $github_uri );
    }

    /**
     * Fetches the update data from GitHub or the cache.
     *
     * @return stdClass|WP_Error The decoded JSON data on success, or WP_Error on failure.
     */
    public function get_update_data() {
        if ( empty( $this->metadata_url ) ) {
            return new WP_Error( 'updater_client_no_uri', 'GitHub Plugin URI header is missing from the main plugin file.' );
        }

        // 1. Try to get from cache first
        $cached_data = get_transient( $this->transient_key );
        if ( false !== $cached_data ) {
            return $cached_data;
        }

        // 2. Not in cache, fetch from GitHub
        $response = wp_remote_get( $this->metadata_url, [
            'timeout'   => 10,
            'sslverify' => true,
        ] );

        // 3. Validate the HTTP response
        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'updater_client_request_failed', 'WP_Error: ' . $response->get_error_message() );
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        if ( 200 !== $response_code ) {
            return new WP_Error( 'updater_client_http_error', "GitHub request failed with HTTP code: {$response_code}" );
        }

        // 4. Process the response body
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_Error( 'updater_client_json_error', 'Failed to decode JSON from update-info.json.' );
        }

        // 5. Cache the successful result (Standard 12 hours)
        set_transient( $this->transient_key, $data, 12 * HOUR_IN_SECONDS );

        return $data;
    }

    /**
     * Deletes the update transient cache.
     * This forces a fresh fetch on the next call.
     */
    public function delete_cache() {
        if ( ! empty( $this->transient_key ) ) {
            delete_transient( $this->transient_key );
        }
    }
}