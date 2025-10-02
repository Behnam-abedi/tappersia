<?php
// tappersia/admin/class-ajax-handler.php

if (!class_exists('Yab_Ajax_Handler')) {
    class Yab_Ajax_Handler {

        public function __construct() {
            $this->load_dependencies();
            $this->register_handlers();
        }

        private function load_dependencies() {
            require_once YAB_PLUGIN_DIR . 'admin/ajax/class-yab-ajax-api-handler.php';
            require_once YAB_PLUGIN_DIR . 'admin/ajax/class-yab-ajax-banner-handler.php';
            require_once YAB_PLUGIN_DIR . 'admin/ajax/class-yab-ajax-content-handler.php';
        }

        private function register_handlers() {
            $api_handler = new Yab_Ajax_Api_Handler();
            $api_handler->register_hooks();

            $banner_handler = new Yab_Ajax_Banner_Handler();
            $banner_handler->register_hooks();

            $content_handler = new Yab_Ajax_Content_Handler();
            $content_handler->register_hooks();
        }
    }
}