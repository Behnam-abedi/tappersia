<?php
// tappersia/admin/class-ajax-handler.php

if (!class_exists('Yab_Ajax_Handler')) {
    class Yab_Ajax_Handler {

        // *** ADD THIS ***
        private $api_key;

        public function __construct() {
            // *** ADD THIS: Load manager and get key ***
            if (!class_exists('Yab_License_Manager')) {
                require_once YAB_PLUGIN_DIR . 'includes/license/class-yab-license-manager.php';
            }
            $license_manager = new Yab_License_Manager();
            $this->api_key = $license_manager->get_api_key();
            // *** END ADD ***

            $this->load_dependencies();
            $this->register_handlers();
        }

        private function load_dependencies() {
            require_once YAB_PLUGIN_DIR . 'admin/ajax/class-yab-ajax-api-handler.php';
            require_once YAB_PLUGIN_DIR . 'admin/ajax/class-yab-ajax-banner-handler.php';
            require_once YAB_PLUGIN_DIR . 'admin/ajax/class-yab-ajax-content-handler.php';
        }

        private function register_handlers() {
            // *** MODIFY THIS: Pass the api_key to the handler ***
            $api_handler = new Yab_Ajax_Api_Handler($this->api_key);
            $api_handler->register_hooks(); // This already registers welcome package related hooks

            $banner_handler = new Yab_Ajax_Banner_Handler();
            $banner_handler->register_hooks();

            $content_handler = new Yab_Ajax_Content_Handler();
            $content_handler->register_hooks();
        }
    }
}