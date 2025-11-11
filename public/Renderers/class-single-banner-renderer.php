<?php
// tappersia/public/Renderers/class-single-banner-renderer.php
if (!class_exists('Yab_Single_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Single_Banner_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            if (empty($this->data['single'])) {
                return '';
            }
            
            $desktop_b = $this->data['single'];
            $mobile_b = $this->data['single_mobile'] ?? $desktop_b; 
            $banner_id = $this->banner_id;

            // --- START: Data for JS (Use DESKTOP image for both) ---
            $desktop_has_image = !empty($desktop_b['imageUrl']);
            $mobile_has_image = !empty($desktop_b['imageUrl']); // <-- Use desktop image
            $desktop_image_url = $desktop_has_image ? esc_js($desktop_b['imageUrl']) : '';
            $mobile_image_url = $mobile_has_image ? esc_js($desktop_b['imageUrl']) : ''; // <-- Use desktop image
            // --- END: Data for JS ---
            
            ob_start();
            ?>
            <style>
                /* --- START: Skeleton/Content Visibility --- */
                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-content-real {
                    visibility: hidden;
                    opacity: 0;
                    transition: opacity 0.3s ease-in-out;
                }
                
                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-skeleton-loader {
                    position: absolute; /* <-- Skeleton is now absolute */
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 1;
                }

                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-content-real.is-loaded {
                    visibility: visible;
                    opacity: 1;
                    position: relative; /* <-- Content is relative */
                    z-index: 2; /* Ensure content is on top */
                }
                
                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-single-skeleton.is-hidden {
                    display: none; /* Hide skeleton completely when done */
                }
                /* --- END: Skeleton/Content Visibility --- */

                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile { display: none; }
                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop { display: block; }
                
                @media (max-width: 768px) {
                    .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop { display: none; }
                    .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile { display: block; }
                }

                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop .yab-button:hover { background-color: <?php echo esc_attr($desktop_b['buttonBgHoverColor'] ?? '#10447B'); ?> !important; }
                /* Mobile uses desktop hover color */
                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile .yab-button:hover { background-color: <?php echo esc_attr($desktop_b['buttonBgHoverColor'] ?? '#10447B'); ?> !important; }
            </style>
            
            <div class="yab-wrapper yab-banner-wrapper-<?php echo $banner_id; ?>" style="width: 100%; direction: ltr; position: relative;">
                <?php 
                // --- START: Modified render calls ---
                // Pass desktop settings as the "desktop reference" for both
                echo $this->render_view_wrapper($desktop_b, 'desktop', $banner_id, $desktop_b); 
                echo $this->render_view_wrapper($mobile_b, 'mobile', $banner_id, $desktop_b); 
                // --- END: Modified render calls ---
                ?>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var wrapper_<?php echo $banner_id; ?> = document.querySelector('.yab-banner-wrapper-<?php echo $banner_id; ?>');
                    if (!wrapper_<?php echo $banner_id; ?>) return;

                    var desktop_skeleton = wrapper_<?php echo $banner_id; ?>.querySelector('.yab-skeleton-desktop');
                    var desktop_content = wrapper_<?php echo $banner_id; ?>.querySelector('.yab-content-desktop');
                    var mobile_skeleton = wrapper_<?php echo $banner_id; ?>.querySelector('.yab-skeleton-mobile');
                    var mobile_content = wrapper_<?php echo $banner_id; ?>.querySelector('.yab-content-mobile');

                    var desktop_has_image = <?php echo json_encode($desktop_has_image); ?>;
                    var mobile_has_image = <?php echo json_encode($mobile_has_image); ?>;
                    
                    var images_to_load = 0;
                    var images_loaded = 0;

                    function showBanners() {
                        if (desktop_skeleton) desktop_skeleton.classList.add('is-hidden');
                        if (desktop_content) desktop_content.classList.add('is-loaded');
                        if (mobile_skeleton) mobile_skeleton.classList.add('is-hidden');
                        if (mobile_content) mobile_content.classList.add('is-loaded');
                    }

                    function onImageLoaded() {
                        images_loaded++;
                        if (images_loaded >= images_to_load) {
                            showBanners();
                        }
                    }

                    if (desktop_has_image) {
                        images_to_load++;
                        var img_desktop = new Image();
                        img_desktop.onload = onImageLoaded;
                        img_desktop.onerror = onImageLoaded; // Count as "loaded" even on error
                        img_desktop.src = '<?php echo $desktop_image_url; ?>';
                    }

                    if (mobile_has_image && '<?php echo $mobile_image_url; ?>' !== '<?php echo $desktop_image_url; ?>') {
                        images_to_load++;
                        var img_mobile = new Image();
                        img_mobile.onload = onImageLoaded;
                        img_mobile.onerror = onImageLoaded;
                        img_mobile.src = '<?php echo $mobile_image_url; ?>';
                    }

                    if (images_to_load === 0) {
                        // No images, just show the content right away
                        // Use a small timeout to ensure styles are applied
                        setTimeout(showBanners, 50);
                    }
                });
            </script>
            <?php
            return ob_get_clean();
        }

        /**
         * New Wrapper Method: Renders the responsive container for both skeleton and real content.
         * Accepts desktop settings ($b_desktop) as a reference.
         */
        private function render_view_wrapper($b, $view, $banner_id, $b_desktop) {
            $wrapper_class = "yab-banner-item yab-banner-$view";
            ob_start();
            ?>
            <div class="<?php echo esc_attr($wrapper_class); ?>" style="position: relative;">
                <?php echo $this->render_skeleton($b, $view, $b_desktop); // Skeleton (absolute) ?>
                <?php echo $this->render_real_banner($b, $view, $b_desktop); // Real Content (relative, hidden) ?>
            </div>
            <?php
            return ob_get_clean();
        }

        /**
         * New Skeleton Method: Renders the placeholder.
         * Accepts $b_desktop.
         */
        private function render_skeleton($b, $view, $b_desktop) {
            $is_desktop = $view === 'desktop';

            $banner_styles = [
                'width' => '100%',
                'height' => 'auto',
                'min-height' => esc_attr($b['minHeight'] ?? ($is_desktop ? 190 : 145)) . 'px',
                'border-radius' => esc_attr($b['borderRadius'] ?? 16) . 'px',
                'position' => 'absolute', 
                'top' => '0',
                'left' => '0',
                'width' => '100%',
                'height' => '100%',
                'overflow' => 'hidden', 
                'background-color' => '#f4f4f4'
            ];

            // Use desktop border color
            if (!empty($b['enableBorder'])) { 
                $banner_styles['border'] = esc_attr($b['borderWidth'] ?? 1) . 'px solid ' . esc_attr($b_desktop['borderColor'] ?? '#ebebeb'); 
            }

            $content_padding = sprintf('%spx %spx', 
                esc_attr($b['paddingY'] ?? ($is_desktop ? 34 : 20)), 
                esc_attr($b['paddingX'] ?? ($is_desktop ? 34 : 22))
            );
            
            // Use DESKTOP alignment for skeleton
            $alignment = $this->get_alignment_style($b_desktop);

            $skeleton_button_margin_top = !empty($b['buttonMarginTopAuto']) ? 'auto' : (esc_attr($b['buttonMarginTop'] ?? 15) . 'px');
            $skeleton_button_margin_bottom = esc_attr($b['buttonMarginBottom'] ?? 0) . 'px';
            $skeleton_min_height = esc_attr($b['minHeight'] ?? ($is_desktop ? 190 : 145)) . 'px';

            ob_start();
            ?>
            <div class="yab-single-skeleton  yab-skeleton-<?php echo $view; ?>" style="<?php echo $this->get_inline_style_attr($banner_styles); ?>">
                <div class="yab-skeleton-content" style="padding: <?php echo $content_padding; ?>; align-items: <?php echo $alignment['align_items']; ?>; text-align: <?php echo $alignment['text_align']; ?>; display: flex; flex-direction: column; min-height: <?php echo $skeleton_min_height; ?>; box-sizing: border-box; width: 100%; height: 100%;">
                    <div style="width:100%; flex-grow: 1;">
                        <div class="yab-skeleton-text-lg yab-skeleton-loader"></div>
                        <div class="yab-skeleton-text-md yab-skeleton-loader"></div>
                    </div>
                    <div class="yab-skeleton-button yab-skeleton-loader" style="align-self: <?php echo $alignment['align_self']; ?>; margin-top: <?php echo $skeleton_button_margin_top; ?>; margin-bottom: <?php echo $skeleton_button_margin_bottom; ?>;"></div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

        /**
         * Renamed from render_view(): Renders the actual banner content (initially hidden).
         * Accepts $b (mobile settings) and $b_desktop (desktop settings).
         */
        private function render_real_banner($b, $view, $b_desktop) {
             $is_desktop = $view === 'desktop';
             
             // --- START: Use $b_desktop for content, $b for layout ---
             
             // Layer order comes from DESKTOP
             $image_z_index = ($b_desktop['layerOrder'] ?? 'image-below-overlay') === 'image-below-overlay' ? 1 : 2;
             $overlay_z_index = $image_z_index === 1 ? 2 : 1;

             // Banner layout (minHeight, border) comes from MOBILE ($b)
             $banner_styles = [
                'width' => '100%', 
                'height' => 'auto',
                'min-height' => esc_attr($b['minHeight'] ?? ($is_desktop ? 190 : 145)) . 'px', 
                'border-radius' => esc_attr($b['borderRadius'] ?? 16) . 'px',
                'position' => 'relative', 
                'overflow' => 'hidden', 
                'flex-shrink' => '0'
            ];
             if (!empty($b['enableBorder'])) { // Mobile setting
                 $banner_styles['border'] = esc_attr($b['borderWidth'] ?? 1) . 'px solid ' . esc_attr($b_desktop['borderColor'] ?? '#ebebeb'); // Mobile width, Desktop color
             }


             // Content layout (padding, width, minHeight) comes from MOBILE ($b)
             $content_styles = [
                'padding' => sprintf('%spx %spx', 
                    esc_attr($b['paddingY'] ?? ($is_desktop ? 34 : 20)), 
                    esc_attr($b['paddingX'] ?? ($is_desktop ? 34 : 22))
                ),
                'width' => esc_attr($b['contentWidth'] . ($b['contentWidthUnit'] ?? '%')),
                'min-height' => esc_attr($b['minHeight'] ?? ($is_desktop ? 190 : 145)) . 'px', 
                'display' => 'flex', 
                'flex-direction' => 'column', 
                'z-index' => '3',
                'position' => 'relative',
                'box-sizing' => 'border-box'
            ];
            
            // Alignment comes from DESKTOP
            $alignment_style = $this->get_alignment_style($b_desktop);
            
            // Title: Color from DESKTOP, Size/Weight from MOBILE
            $title_styles = [
                'font-weight' => esc_attr($b['titleWeight'] ?? '700'), // Mobile
                'color' => esc_attr($b_desktop['titleColor'] ?? '#ffffff'), // Desktop
                'font-size' => esc_attr($b['titleSize'] ?? ($is_desktop ? 24 : 14)) . 'px', // Mobile
                'line-height' => 1,
                'margin' => 0,
            ];

            // Description: Color from DESKTOP, Size/Weight/Margin from MOBILE
            $desc_styles = [
                'font-weight' => esc_attr($b['descWeight'] ?? '500'), // Mobile
                'color' => esc_attr($b_desktop['descColor'] ?? '#ffffff'), // Desktop
                'font-size' => esc_attr($b['descSize'] ?? ($is_desktop ? 14 : 12)) . 'px', // Mobile
                'margin-top' => esc_attr($b['marginTopDescription'] ?? 12) . 'px', // Mobile
                'margin-bottom' => '0',
                'line-height' => 1.5,
                'word-wrap' => 'break-word',
            ];

            // Button Margin comes from MOBILE
            $button_margin_top = !empty($b['buttonMarginTopAuto']) ? 'auto' : (esc_attr($b['buttonMarginTop'] ?? 15) . 'px');

            // Button: Colors/Weight from DESKTOP, Sizing/Padding/Radius from MOBILE
            $button_styles = [
                'text-decoration' => 'none', 'display' => 'inline-flex', 'align-items' => 'center',
                'justify-content' => 'center', 'transition' => 'background-color 0.3s',
                'padding' => sprintf('%spx %spx', // Mobile
                    esc_attr($b['buttonPaddingY'] ?? ($is_desktop ? 12 : 10)),
                    esc_attr($b['buttonPaddingX'] ?? ($is_desktop ? 24 : 16))
                ),
                'background-color' => esc_attr($b_desktop['buttonBgColor'] ?? '#124C88'), // Desktop
                'color' => esc_attr($b_desktop['buttonTextColor'] ?? '#ffffff'), // Desktop
                'font-size' => esc_attr($b['buttonFontSize'] ?? ($is_desktop ? 14 : 11)) . 'px', // Mobile
                'border-radius' => esc_attr($b['buttonBorderRadius'] ?? 8) . 'px', // Mobile
                'font-weight' => esc_attr($b_desktop['buttonFontWeight'] ?? '500'), // Desktop
                'align-self' => $alignment_style['align_self'], // Desktop
                'line-height' => 1,
                'margin-top' => $button_margin_top, // Mobile
                'margin-bottom' => esc_attr($b['buttonMarginBottom'] ?? 0) . 'px' // <-- ADDED (Mobile)
            ];
            // --- END: Use $b_desktop for content, $b for layout ---

            ob_start();
            ?>
            <div class="yab-content-real yab-content-<?php echo $view; ?>" style="<?php echo $this->get_inline_style_attr($banner_styles); ?>">
                
                <?php // Image URL from DESKTOP, Style (size/pos) from MOBILE ($b) ?>
                <?php if (!empty($b_desktop['imageUrl'])): ?>
                    <img src="<?php echo esc_url($b_desktop['imageUrl']); ?>" alt="" style="<?php echo $this->get_image_style($b); ?> z-index: <?php echo $image_z_index; ?>;">
                <?php endif; ?>

                <?php // Background from MOBILE, z-index from DESKTOP ?>
                <div class="yab-banner-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: <?php echo $overlay_z_index; ?>; <?php echo $this->get_background_style($b); ?>"></div>
                
                <?php // Content styles from MOBILE, text-align from DESKTOP ?>
                <div class="yab-banner-content" style="<?php echo $this->get_inline_style_attr($content_styles); ?> text-align: <?php echo $alignment_style['text_align']; ?>; align-items: <?php echo $alignment_style['align_items']; ?>;">
                    
                    <div style="flex-grow: 1;">
                        <?php // Text from DESKTOP, Style is mixed ?>
                        <h2 style="<?php echo $this->get_inline_style_attr($title_styles); ?>"><?php echo esc_html($b_desktop['titleText'] ?? ''); ?></h2>
                        <p style="<?php echo $this->get_inline_style_attr($desc_styles); ?>">
                            <?php echo wp_kses_post(trim($b_desktop['descText'] ?? '')); ?>
                        </p>
                    </div>

                    <?php // Text/Link from DESKTOP, Style is mixed ?>
                    <?php if(!empty($b_desktop['buttonText'])): ?>
                    <a href="<?php echo esc_url($b_desktop['buttonLink'] ?? '#'); ?>" target="_blank" class="yab-button" style="<?php echo $this->get_inline_style_attr($button_styles); ?>"><?php echo esc_html($b_desktop['buttonText']); ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
        
        private function get_inline_style_attr(array $styles): string {
            $style_str = '';
            foreach ($styles as $prop => $value) { 
                if ($value !== '' && $value !== null) {
                    $style_str .= $prop . ': ' . $value . '; '; 
                }
            }
            return trim($style_str);
        }

        // --- START: FIX - Added 'array' type hint and ': string' return type ---
        /**
         * Helper to generate image styles, using mobile layout settings.
         */
        protected function get_image_style(array $b): string {
        // --- END: FIX ---
            $style = [
                'position' => 'absolute',
                'object-fit' => 'cover',
                'object-position' => 'center center',
                'right' => esc_attr($b['imagePosRight'] ?? 0) . 'px',
                'bottom' => esc_attr($b['imagePosBottom'] ?? 0) . 'px',
            ];

            if (!empty($b['enableCustomImageSize'])) {
                $style['width'] = !empty($b['imageWidth']) ? esc_attr($b['imageWidth']) . esc_attr($b['imageWidthUnit'] ?? 'px') : 'auto';
                $style['height'] = !empty($b['imageHeight']) ? esc_attr($b['imageHeight']) . esc_attr($b['imageHeightUnit'] ?? 'px') : '100%';
            } else {
                $style['width'] = 'auto';
                $style['height'] = '100%';
            }

            return $this->get_inline_style_attr($style);
        }
    }
}