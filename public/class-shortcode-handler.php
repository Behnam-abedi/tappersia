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
            // Added 'welcomepackagebanner'
            $banner_types = [
                'singlebanner', 'doublebanner', 'apibanner', 'simplebanner',
                'stickysimplebanner', 'promotionbanner', 'contenthtml',
                'contenthtmlsidebar', 'tourcarousel', 'hotelcarousel',
                'welcomepackagebanner' // New type added
            ];
            foreach ($banner_types as $type) {
                add_shortcode($type, [$this, 'render_embeddable_banner']);
                add_shortcode($type . '_fixed', [$this, 'render_fixed_banner']);
            }
        }

        public function render_embeddable_banner($atts, $content = null, $tag = '') {
            $atts = shortcode_atts(['id' => 0], $atts, $tag);
            if (empty($atts['id'])) {
                return "<!-- Banner ID missing -->";
            }

            $banner_post = get_post(intval($atts['id']));

            // Determine banner_type_slug based on the tag
            $banner_type_slug = $this->get_type_slug_from_tag($tag);

            if (empty($banner_type_slug) || !$this->is_valid_banner($banner_post, $banner_type_slug, 'Embeddable')) {
                 return "<!-- Invalid Banner ID or Type/Method Mismatch -->";
            }

            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
            return $this->render_banner($banner_type_slug, $data, $banner_post->ID);
        }

        public function render_fixed_banner($atts, $content = null, $tag = '') {
            global $post;
            // Allow fixed banners on more page types if needed (e.g., is_home(), is_front_page())
            if (!is_singular() && !is_category() && !is_archive() && !is_home() && !is_front_page()) return '';

            $base_tag = str_replace('_fixed', '', $tag);
            $banner_type_slug = $this->get_type_slug_from_tag($base_tag);

            if (empty($banner_type_slug)) return "<!-- Unknown fixed banner type: $base_tag -->";

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
            if (empty($banners)) return "<!-- No active fixed banner found for type: $banner_type_slug -->";

            $queried_object_id = get_queried_object_id();

            // Find the *first* banner that matches the display conditions for the current page
            foreach ($banners as $banner_post) {
                if ($this->should_display_fixed($banner_post, $queried_object_id, $post)) {
                    $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
                    return $this->render_banner($banner_type_slug, $data, $banner_post->ID);
                }
            }

            return "<!-- No fixed banner of type $banner_type_slug matched display conditions -->";
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
                'welcomepackagebanner' => 'welcome-package-banner' // Added mapping
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

            // If no specific IDs are set, don't display (prevent accidental global display)
            // Or, you could change this to display everywhere if empty, based on desired logic.
            if (empty($post_ids) && empty($page_ids) && empty($cat_ids)) {
                 return false;
             }


            if (is_singular('post') && in_array($queried_object_id, $post_ids)) return true;
            if (is_page() && in_array($queried_object_id, $page_ids)) return true;
            // Check category archive pages and single posts within those categories
            if (!empty($cat_ids) && (is_category($cat_ids) || (is_singular('post') && $global_post && has_category($cat_ids, $global_post)))) return true;
             // Add checks for other conditions if needed (e.g., is_home(), is_front_page())

            return false;
        }

        private function is_valid_banner($banner_post, string $expected_type, string $expected_method): bool {
            if (!$banner_post || $banner_post->post_type !== 'yab_banner' || $banner_post->post_status !== 'publish') {
                return false;
            }
            $banner_type_meta = get_post_meta($banner_post->ID, '_yab_banner_type', true);
            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
            $display_method_meta = $data['displayMethod'] ?? 'Fixed'; // Default to Fixed if not set

            // Check type match
             if ($banner_type_meta !== $expected_type) {
                 error_log("Tappersia Render Warning: Banner ID {$banner_post->ID} has type '{$banner_type_meta}' but expected '{$expected_type}'.");
                 return false; // Type must match
             }

             // Check active status
             if (empty($data) || empty($data['isActive'])) {
                 return false; // Banner data missing or inactive
             }

             // Check display method match
             if ($display_method_meta !== $expected_method) {
                // Allow embeddable carousels to render even if method is fixed (legacy or error?)
                // Or be stricter: return false;
                 error_log("Tappersia Render Warning: Banner ID {$banner_post->ID} has method '{$display_method_meta}' but expected '{$expected_method}'. Rendering anyway (or return false based on strictness).");
                // return false; // Uncomment for stricter check
             }

            // Specific check for Embeddable Carousels (Tour or Hotel) - They are active regardless of displayMethod meta if type matches
            // This is somewhat redundant now with the checks above but kept for clarity/legacy
             if (($expected_type === 'tour-carousel' || $expected_type === 'hotel-carousel') && $expected_method === 'Embeddable') {
                 return true; // Already passed type and active check
             }

            // Standard check passed
            return true;
        }

        private function render_banner(string $type_slug, array $data, int $banner_id): string {
            // Converts 'hotel-carousel' to 'Hotel_Carousel', etc.
            $class_name_part = str_replace('-', '_', ucwords($type_slug, '-'));
            $class_name = 'Yab_' . $class_name_part . '_Renderer';

            if (class_exists($class_name)) {
                try {
                     $renderer = new $class_name($data, $banner_id);
                     return $renderer->render();
                } catch (Throwable $e) { // Catch Throwable for broader error handling
                    error_log("Tappersia Render Error: Failed to instantiate or render class {$class_name} for banner ID {$banner_id}: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
                     return "<!-- Error rendering banner ID {$banner_id} -->";
                }
            } else {
                 error_log("Tappersia Render Error: Renderer class {$class_name} not found for type {$type_slug}.");
                 return "<!-- Renderer not found for banner type {$type_slug} -->";
            }
        }
    }
}
?>
