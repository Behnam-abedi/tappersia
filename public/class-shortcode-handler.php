<?php
// tappersia/public/class-shortcode-handler.php

if (!class_exists('Yab_Shortcode_Handler')) {
    
    // Autoload renderer classes
    spl_autoload_register(function ($class_name) {
        if (strpos($class_name, 'Yab_') === 0 && strpos($class_name, '_Renderer') !== false) {
            $file = YAB_PLUGIN_DIR . 'public/Renderers/class-' . strtolower(str_replace('_', '-', str_replace('Yab_', '', $class_name))) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    });

    class Yab_Shortcode_Handler {

        public function register_shortcodes() {
            $banner_types = ['singlebanner', 'doublebanner', 'apibanner', 'simplebanner'];
            foreach ($banner_types as $type) {
                add_shortcode($type, [$this, 'render_embeddable_banner']);
                add_shortcode($type . '_fixed', [$this, 'render_fixed_banner']);
            }
        }

        /**
         * Handles all embeddable banner shortcodes.
         */
        public function render_embeddable_banner($atts, $content = null, $tag = '') {
            $atts = shortcode_atts(['id' => 0], $atts, $tag);
            if (empty($atts['id'])) {
                return "";
            }
            
            $banner_post = get_post(intval($atts['id']));
            
            $banner_type_slug = str_replace('banner', '-banner', $tag);

            if (!$this->is_valid_banner($banner_post, $banner_type_slug, 'Embeddable')) {
                 return "";
            }
            
            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
            return $this->render_banner($banner_type_slug, $data, $banner_post->ID);
        }

        /**
         * Handles all fixed banner shortcodes.
         */
        public function render_fixed_banner($atts, $content = null, $tag = '') {
            global $post;
            if (!$post && !is_category() && !is_archive()) return '';

            $base_tag = str_replace('_fixed', '', $tag);
            $banner_type_slug = str_replace('banner', '-banner', $base_tag);
            
            $args = [
                'post_type' => 'yab_banner', 'posts_per_page' => -1, 'post_status' => 'publish',
                'meta_query' => [
                    'relation' => 'AND',
                    ['key' => '_yab_display_method', 'value' => 'Fixed'],
                    ['key' => '_yab_is_active', 'value' => true],
                    ['key' => '_yab_banner_type', 'value' => $banner_type_slug]
                ]
            ];

            $banners = get_posts($args);
            if (empty($banners)) return '';

            $queried_object_id = get_queried_object_id();

            foreach ($banners as $banner_post) {
                if ($this->should_display_fixed($banner_post, $queried_object_id, $post)) {
                    $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
                    return $this->render_banner($banner_type_slug, $data, $banner_post->ID);
                }
            }

            return '';
        }

        /**
         * Checks if a fixed banner should be displayed based on conditions.
         */
        private function should_display_fixed($banner_post, $queried_object_id, $global_post): bool {
            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
            if (empty($data['displayOn'])) return false;

            $cond = $data['displayOn'];
            $post_ids = !empty($cond['posts']) ? array_map('intval', $cond['posts']) : [];
            $page_ids = !empty($cond['pages']) ? array_map('intval', $cond['pages']) : [];
            $cat_ids  = !empty($cond['categories']) ? array_map('intval', $cond['categories']) : [];

            if (is_singular('post') && in_array($queried_object_id, $post_ids)) return true;
            if (is_page() && in_array($queried_object_id, $page_ids)) return true;
            if (!empty($cat_ids) && (is_category($cat_ids) || (is_singular('post') && has_category($cat_ids, $global_post)))) return true;
            
            return false;
        }

        /**
         * Validates a banner post object for rendering.
         */
        private function is_valid_banner($banner_post, string $expected_type, string $expected_method): bool {
            if (!$banner_post || $banner_post->post_type !== 'yab_banner' || $banner_post->post_status !== 'publish') {
                return false;
            }
            $banner_type = get_post_meta($banner_post->ID, '_yab_banner_type', true);
            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);

            return $banner_type === $expected_type &&
                   !empty($data) &&
                   !empty($data['isActive']) &&
                   ($data['displayMethod'] ?? 'Fixed') === $expected_method;
        }

        /**
         * Instantiates the correct renderer and calls its render method.
         */
        private function render_banner(string $type_slug, array $data, int $banner_id): string {
            $class_name = 'Yab_' . str_replace('-', '_', ucwords($type_slug, '-')) . '_Renderer';
            
            if (class_exists($class_name)) {
                $renderer = new $class_name($data, $banner_id);
                return $renderer->render();
            }

            return "";
        }
    }
}