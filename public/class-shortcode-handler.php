<?php
// tappersia/public/class-shortcode-handler.php

if (!class_exists('Yab_Shortcode_Handler')) {

    // Autoload renderer classes
    spl_autoload_register(function ($class_name) {
        if (strpos($class_name, 'Yab_') === 0 && strpos($class_name, '_Renderer') !== false) {
            // Adjusted file name generation
            $file_part = strtolower(str_replace('_', '-', str_replace(['Yab_', '_Renderer'], '', $class_name)));
            $file = YAB_PLUGIN_DIR . 'public/Renderers/class-' . $file_part . '-renderer.php';

            if (file_exists($file)) {
                require_once $file;
            } else {
                 error_log("Tappersia Autoload: Renderer file not found: " . $file);
            }
        }
    });

    class Yab_Shortcode_Handler {

        public function register_shortcodes() {
            // Added 'hotelcarousel'
            $banner_types = ['singlebanner', 'doublebanner', 'apibanner', 'simplebanner', 'stickysimplebanner', 'promotionbanner', 'contenthtml', 'contenthtmlsidebar', 'tourcarousel', 'hotelcarousel'];
            foreach ($banner_types as $type) {
                add_shortcode($type, [$this, 'render_embeddable_banner']);
                add_shortcode($type . '_fixed', [$this, 'render_fixed_banner']);
            }
        }

        public function render_embeddable_banner($atts, $content = null, $tag = '') {
            $atts = shortcode_atts(['id' => 0], $atts, $tag);
            if (empty($atts['id'])) {
                return "";
            }

            $banner_post = get_post(intval($atts['id']));

            // Determine banner_type_slug based on the tag
            $banner_type_slug = '';
            if (strpos($tag, 'banner') !== false) {
                 $banner_type_slug = str_replace('banner', '-banner', $tag);
            } elseif ($tag === 'contenthtml') {
                $banner_type_slug = 'content-html-banner';
            } elseif ($tag === 'contenthtmlsidebar') {
                $banner_type_slug = 'content-html-sidebar-banner';
            } elseif ($tag === 'tourcarousel') {
                $banner_type_slug = 'tour-carousel';
            } elseif ($tag === 'hotelcarousel') { // Added condition
                $banner_type_slug = 'hotel-carousel';
            }

            if (empty($banner_type_slug) || !$this->is_valid_banner($banner_post, $banner_type_slug, 'Embeddable')) {
                 return "";
            }

            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
            return $this->render_banner($banner_type_slug, $data, $banner_post->ID);
        }

        public function render_fixed_banner($atts, $content = null, $tag = '') {
            global $post;
            if (!$post && !is_category() && !is_archive()) return '';

            $base_tag = str_replace('_fixed', '', $tag);
            // Determine banner_type_slug based on the base_tag
            $banner_type_slug = '';
            if (strpos($base_tag, 'banner') !== false) {
                 $banner_type_slug = str_replace('banner', '-banner', $base_tag);
            } elseif ($base_tag === 'contenthtml') {
                $banner_type_slug = 'content-html-banner';
            } elseif ($base_tag === 'contenthtmlsidebar') {
                $banner_type_slug = 'content-html-sidebar-banner';
            } elseif ($base_tag === 'tourcarousel') {
                $banner_type_slug = 'tour-carousel';
            } elseif ($base_tag === 'hotelcarousel') { // Added condition
                $banner_type_slug = 'hotel-carousel';
            }


            if (empty($banner_type_slug)) return ''; // Return empty if type couldn't be determined


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

        private function should_display_fixed($banner_post, $queried_object_id, $global_post): bool {
            // ... (no changes needed here) ...
             $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
            if (empty($data['displayOn'])) return false;

            $cond = $data['displayOn'];
            $post_ids = !empty($cond['posts']) ? array_map('intval', $cond['posts']) : [];
            $page_ids = !empty($cond['pages']) ? array_map('intval', $cond['pages']) : [];
            $cat_ids  = !empty($cond['categories']) ? array_map('intval', $cond['categories']) : [];

            if (is_singular('post') && in_array($queried_object_id, $post_ids)) return true;
            if (is_page() && in_array($queried_object_id, $page_ids)) return true;
            if (!empty($cat_ids) && (is_category($cat_ids) || (is_singular('post') && $global_post && has_category($cat_ids, $global_post)))) return true;

            return false;
        }

        private function is_valid_banner($banner_post, string $expected_type, string $expected_method): bool {
             // ... (Adjusted logic for Carousel embeddable) ...
            if (!$banner_post || $banner_post->post_type !== 'yab_banner' || $banner_post->post_status !== 'publish') {
                return false;
            }
            $banner_type = get_post_meta($banner_post->ID, '_yab_banner_type', true);
            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);

            // Special check for Embeddable Carousels (Tour or Hotel) - They are active regardless of displayMethod meta if type matches
             if (($expected_type === 'tour-carousel' || $expected_type === 'hotel-carousel') && $expected_method === 'Embeddable') {
                 return $banner_type === $expected_type &&
                        !empty($data) &&
                        !empty($data['isActive']); // Only check type and active status
             }

            // Standard check for other types or Fixed display method
            return $banner_type === $expected_type &&
                   !empty($data) &&
                   !empty($data['isActive']) &&
                   ($data['displayMethod'] ?? 'Fixed') === $expected_method;
        }

        private function render_banner(string $type_slug, array $data, int $banner_id): string {
             // ... (Adjusted class name generation) ...
            // Converts 'hotel-carousel' to 'Hotel_Carousel', etc.
            $class_name_part = str_replace('-', '_', ucwords($type_slug, '-'));
            $class_name = 'Yab_' . $class_name_part . '_Renderer';

            if (class_exists($class_name)) {
                try {
                     $renderer = new $class_name($data, $banner_id);
                     return $renderer->render();
                } catch (Error $e) {
                    error_log("Tappersia Render Error: Failed to instantiate or render class {$class_name}: " . $e->getMessage());
                     return "";
                }
            } else {
                 error_log("Tappersia Render Error: Renderer class {$class_name} not found for type {$type_slug}.");
                 return "";
            }
        }
    }
}
?>