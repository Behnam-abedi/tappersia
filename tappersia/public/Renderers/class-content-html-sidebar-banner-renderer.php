<?php
// tappersia/public/Renderers/class-content-html-sidebar-banner-renderer.php

if (!class_exists('Yab_Content_Html_Sidebar_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Content_Html_Sidebar_Banner_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            if (empty($this->data['content_html_sidebar']) || empty($this->data['content_html_sidebar']['html'])) {
                return '';
            }
            
            return $this->data['content_html_sidebar']['html'];
        }
    }
}