<?php
// tappersia/public/Renderers/class-content-html-banner-renderer.php

if (!class_exists('Yab_Content_Html_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Content_Html_Banner_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            if (empty($this->data['content_html']) || empty($this->data['content_html']['html'])) {
                return '';
            }
            
            // The content is output directly as it was saved by the admin.
            // This allows for full HTML, CSS, and JS flexibility.
            return $this->data['content_html']['html'];
        }
    }
}