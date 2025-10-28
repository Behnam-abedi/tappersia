<?php
// tappersia/public/class-shortcode-handler.php

if (!class_exists('Yab_Shortcode_Handler')) {

    // Autoload renderer classes
    spl_autoload_register(function ($class_name) {
        if (strpos($class_name, 'Yab_') === 0 && strpos($class_name, '_Renderer') !== false) {
            // Converts Yab_Welcome_Package_Banner_Renderer to welcome-package-banner
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
            $banner_types = [
                'singlebanner', 'doublebanner', 'apibanner', 'simplebanner',
                'stickysimplebanner', 'promotionbanner', 'contenthtml',
                'contenthtmlsidebar', 'tourcarousel', 'hotelcarousel', 'welcomepackage' // Added 'welcomepackage'
            ];
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
            $banner_type_slug = $this->get_type_slug_from_tag($tag);

            if (empty($banner_type_slug)) {
                 error_log("Tappersia Render Error: Unknown shortcode tag '{$tag}'.");
                 return "";
            }

            if (!$this->is_valid_banner($banner_post, $banner_type_slug, 'Embeddable')) {
                 // is_valid_banner logs specific errors
                 return "";
            }

            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
             if (empty($data)) {
                 error_log("Tappersia Render Error: Banner data missing for embeddable banner ID {$banner_post->ID}.");
                 return "";
             }
            return $this->render_banner($banner_type_slug, $data, $banner_post->ID);
        }

        public function render_fixed_banner($atts, $content = null, $tag = '') {
            global $post;
            // Allow fixed banners on more page types if needed (e.g., is_home(), is_front_page())
            if (!is_singular() && !is_category() && !is_archive() && !is_home() && !is_front_page()) return '';

            $base_tag = str_replace('_fixed', '', $tag);
            $banner_type_slug = $this->get_type_slug_from_tag($base_tag);

            if (empty($banner_type_slug)) {
                error_log("Tappersia Render Error: Unknown fixed shortcode tag '{$tag}'.");
                return ""; // Unknown type
            }

            // Only query for the specific fixed banner type needed for this shortcode
            $args = [
                'post_type' => 'yab_banner',
                'posts_per_page' => -1, // Fetch all matching fixed banners of this type
                'post_status' => 'publish',
                'meta_query' => [
                    'relation' => 'AND',
                    ['key' => '_yab_display_method', 'value' => 'Fixed'],
                    ['key' => '_yab_is_active', 'value' => true],
                    ['key' => '_yab_banner_type', 'value' => $banner_type_slug]
                ]
            ];

            $banners = get_posts($args);
            if (empty($banners)) return "";

            $queried_object_id = get_queried_object_id();

            // Find the *first* banner that matches the display conditions for the current page
            foreach ($banners as $banner_post) {
                if ($this->should_display_fixed($banner_post, $queried_object_id, $post)) {
                    $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
                     if (empty($data)) {
                        error_log("Tappersia Render Error: Banner data missing for fixed banner ID {$banner_post->ID}.");
                        continue; // Skip banner if data is missing
                    }
                    return $this->render_banner($banner_type_slug, $data, $banner_post->ID);
                }
            }

            return ""; // No matching fixed banner found for this page
        }

        // Helper function to convert shortcode tag to banner type slug
        private function get_type_slug_from_tag(string $tag): string {
             $map = [
                'singlebanner' => 'single-banner',
                'doublebanner' => 'double-banner',
                'apibanner' => 'api-banner',
                'simplebanner' => 'simple-banner',
                'stickysimplebanner' => 'sticky-simple-banner',
                'promotionbanner' => 'promotion-banner',
                'contenthtml' => 'content-html-banner',
                'contenthtmlsidebar' => 'content-html-sidebar-banner',
                'tourcarousel' => 'tour-carousel',
                'hotelcarousel' => 'hotel-carousel',
                'welcomepackage' => 'welcome-package-banner', // Added mapping
            ];
            return $map[$tag] ?? '';
        }


        private function should_display_fixed($banner_post, $queried_object_id, $global_post): bool {
            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
            if (empty($data['displayOn'])) return false; // Don't display if no conditions set

            $cond = $data['displayOn'];
            $post_ids = !empty($cond['posts']) ? array_map('intval', $cond['posts']) : [];
            $page_ids = !empty($cond['pages']) ? array_map('intval', $cond['pages']) : [];
            $cat_ids  = !empty($cond['categories']) ? array_map('intval', $cond['categories']) : [];

            // If no specific IDs are set, don't display
            if (empty($post_ids) && empty($page_ids) && empty($cat_ids)) {
                 return false;
             }

            if (is_singular('post') && in_array($queried_object_id, $post_ids)) return true;
            if (is_page() && in_array($queried_object_id, $page_ids)) return true;
            if (!empty($cat_ids) && (is_category($cat_ids) || (is_singular('post') && $global_post && has_category($cat_ids, $global_post)))) return true;

            return false;
        }

        private function is_valid_banner($banner_post, string $expected_type, string $expected_method): bool {
            if (!$banner_post || !is_a($banner_post, 'WP_Post') || $banner_post->post_type !== 'yab_banner' || $banner_post->post_status !== 'publish') {
                error_log("Tappersia Render Warning: Banner object invalid, not found, not 'yab_banner', or not published.");
                return false;
            }

            $banner_id = $banner_post->ID; // Get ID for logging
            $banner_type_meta = get_post_meta($banner_id, '_yab_banner_type', true);
            $data = get_post_meta($banner_id, '_yab_banner_data', true);

            // Check banner type first
            if ($banner_type_meta !== $expected_type) {
                error_log("Tappersia Render Warning: Banner ID {$banner_id} has type '{$banner_type_meta}' but expected '{$expected_type}'.");
                return false;
            }

             // Check active status
             if (empty($data) || !isset($data['isActive']) || $data['isActive'] !== true) {
                 error_log("Tappersia Render Warning: Banner ID {$banner_id} data is missing or banner is inactive.");
                 return false;
             }

             // Check display method match (relevant for embeddable shortcodes primarily)
             $display_method_meta = $data['displayMethod'] ?? 'Fixed'; // Default to Fixed if not set
             if ($expected_method === 'Embeddable' && $display_method_meta !== 'Embeddable') {
                 error_log("Tappersia Render Warning: Banner ID {$banner_id} is set to '{$display_method_meta}' but used with an Embeddable shortcode '[".str_replace('-', '', $expected_type)." id=\"{$banner_id}\"]'. Banner will not render.");
                 return false; // Stricter check: Embeddable shortcode requires Embeddable method.
             }
             // Note: Fixed shortcodes don't check method here, they query specifically for 'Fixed' banners.

            // If all checks pass
            return true;
        }


        private function render_banner(string $type_slug, array $data, int $banner_id): string {
            // Converts 'welcome-package-banner' to 'Welcome_Package_Banner', etc.
            $class_name_part = str_replace('-', '_', ucwords($type_slug, '-'));
            $class_name = 'Yab_' . $class_name_part . '_Renderer';

            if (class_exists($class_name)) {
                try {
                     // Ensure data integrity before passing to renderer
                     if (!isset($data['isActive']) || $data['isActive'] !== true) {
                         return "";
                     }

                     $renderer = new $class_name($data, $banner_id);
                     return $renderer->render();
                } catch (Throwable $e) { // Catch Throwable for broader error handling (PHP 7+)
                    error_log("Tappersia Render Error: Failed to instantiate or render class {$class_name} for banner ID {$banner_id}: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
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