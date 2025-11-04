<?php
// tappersia/public/Renderers/class-single-banner-renderer.php
if (!class_exists('Yab_Single_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Single_Banner_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            // --- FIX: Changed $this.data to $this->data ---
            if (empty($this->data['single'])) {
                return '';
            }
            
            // --- FIX: Changed $this.data to $this->data ---
            $desktop_b = $this->data['single'];
            $mobile_b = $this->data['single_mobile'] ?? $desktop_b; 
            // --- FIX: Changed $this.banner_id to $this->banner_id ---
            $banner_id = $this->banner_id;

            // --- START: Data for JS ---
            $desktop_has_image = !empty($desktop_b['imageUrl']);
            $mobile_has_image = !empty($mobile_b['imageUrl']);
            $desktop_image_url = $desktop_has_image ? esc_js($desktop_b['imageUrl']) : '';
            $mobile_image_url = $mobile_has_image ? esc_js($mobile_b['imageUrl']) : '';
            // --- END: Data for JS ---
            
            ob_start();
            ?>
            <style>
                /* --- START: Skeleton/Content Visibility --- */
                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-content-real {
                    visibility: hidden;
                    opacity: 0;
                    transition: opacity 0.3s ease-in-out;
                    position: absolute; /* Add absolute positioning */
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                }
                
                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-skeleton-loader {
                    position: relative; /* Skeleton takes up space */
                    z-index: 1;
                }

                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-content-real.is-loaded {
                    visibility: visible;
                    opacity: 1;
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
                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile .yab-button:hover { background-color: <?php echo esc_attr($mobile_b['buttonBgHoverColor'] ?? '#008a7b'); ?> !important; }
            </style>
            
            <div class="yab-wrapper yab-banner-wrapper-<?php echo $banner_id; ?>" style="width: 100%; direction: ltr; position: relative;">
                <?php 
                // Render both Skeleton and Real Content for Desktop and Mobile
                echo $this->render_view_wrapper($desktop_b, 'desktop', $banner_id); 
                echo $this->render_view_wrapper($mobile_b, 'mobile', $banner_id); 
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

                    if (mobile_has_image) {
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
         */
        private function render_view_wrapper($b, $view, $banner_id) {
            $wrapper_class = "yab-banner-item yab-banner-$view";
            ob_start();
            ?>
            <div class="<?php echo esc_attr($wrapper_class); ?>" style="position: relative;">
                <?php echo $this->render_skeleton($b, $view); // Skeleton (relative) ?>
                <?php echo $this->render_real_banner($b, $view, $banner_id); // Real Content (absolute) ?>
            </div>
            <?php
            return ob_get_clean();
        }

        /**
         * New Skeleton Method: Renders the placeholder.
         */
        private function render_skeleton($b, $view) {
            $is_desktop = $view === 'desktop';

            // --- تغییرات ---
            $banner_styles = [
                'width' => '100%', // <--- همیشه 100%
                'height' => 'auto',
                'min-height' => esc_attr($b['minHeight'] ?? ($is_desktop ? 190 : 145)) . 'px', // <--- ساده‌سازی شد
                'border-radius' => esc_attr($b['borderRadius'] ?? 16) . 'px',
                'position' => 'relative', 
                'overflow' => 'hidden', 
                'flex-shrink' => '0',
                'background-color' => '#f4f4f4' // Skeleton base color
            ];
            // --- پایان تغییرات ---

            if (!empty($b['enableBorder'])) { 
                $banner_styles['border'] = esc_attr($b['borderWidth'] ?? 1) . 'px solid ' . esc_attr($b['borderColor'] ?? '#ebebeb'); 
            }

            // --- تغییرات پدینگ ---
            $content_padding = sprintf('%spx %spx', 
                esc_attr($b['paddingY'] ?? ($is_desktop ? 34 : 20)), 
                esc_attr($b['paddingX'] ?? ($is_desktop ? 34 : 22))
            );
            // --- پایان تغییرات ---
            
            // --- FIX: Changed $this. to $this-> ---
            $alignment = $this->get_alignment_style($b);

            ob_start();
            ?>
            <div class="yab-single-skeleton  yab-skeleton-<?php echo $view; ?>" style="<?php echo $this->get_inline_style_attr($banner_styles); ?>">
                <div class="yab-skeleton-content" style="padding: <?php echo $content_padding; ?>; align-items: <?php echo $alignment['align_items']; ?>; text-align: <?php echo $alignment['text_align']; ?>; display: flex; flex-direction: column; min-height: <?php echo esc_attr($b['minHeight'] ?? ($is_desktop ? 190 : 145)); ?>px; box-sizing: border-box;">
                    <div style="width:100%">
                        <div class="yab-skeleton-text-lg yab-skeleton-loader"></div>
                        <div class="yab-skeleton-text-md yab-skeleton-loader"></div>
                    </div>
                    <div class="yab-skeleton-button yab-skeleton-loader" style="align-self: <?php echo $alignment['align_self']; ?>; margin-top: <?php echo esc_attr($b['marginBottomDescription'] ?? 15); ?>px;"></div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

        /**
         * Renamed from render_view(): Renders the actual banner content (initially hidden).
         */
        private function render_real_banner($b, $view, $banner_id) {
             $is_desktop = $view === 'desktop';
             
             $image_z_index = ($b['layerOrder'] ?? 'image-below-overlay') === 'image-below-overlay' ? 1 : 2;
             $overlay_z_index = $image_z_index === 1 ? 2 : 1;

             // --- تغییرات ---
             $banner_styles = [
                'width' => '100%', // <--- همیشه 100%
                'height' => 'auto',
                'min-height' => esc_attr($b['minHeight'] ?? ($is_desktop ? 190 : 145)) . 'px', // <--- ساده‌سازی شد
                'border-radius' => esc_attr($b['borderRadius'] ?? 16) . 'px',
                'position' => 'relative', 'overflow' => 'hidden', 'flex-shrink' => '0'
            ];
             // --- پایان تغییرات ---
             if (!empty($b['enableBorder'])) { $banner_styles['border'] = esc_attr($b['borderWidth'] ?? 1) . 'px solid ' . esc_attr($b['borderColor'] ?? '#ebebeb'); }

            $wrapper_styles = [
                'position' => 'relative', 'width' => '100%', 'height' => 'auto',
            ];


            // --- تغییرات ---
            $content_styles = [
                'padding' => sprintf('%spx %spx', // <--- پدینگ Y/X
                    esc_attr($b['paddingY'] ?? ($is_desktop ? 34 : 20)), 
                    esc_attr($b['paddingX'] ?? ($is_desktop ? 34 : 22))
                ),
                'width' => esc_attr($b['contentWidth'] . ($b['contentWidthUnit'] ?? '%')), // <--- عرض محتوا
                'display' => 'flex', 'flex-direction' => 'column', 'z-index' => '10', 'position' => 'relative', 'flex-grow' => '1', 'width' => '100%', 'height' => '100%', 'box-sizing' => 'border-box'
            ];
            // --- پایان تغییرات ---
            
            // --- FIX: Changed $this. to $this-> ---
            $alignment_style = $this->get_alignment_style($b);
            
            $title_styles = [
                'font-weight' => esc_attr($b['titleWeight'] ?? '700'),
                'color' => esc_attr($b['titleColor'] ?? '#ffffff'),
                'font-size' => esc_attr($b['titleSize'] ?? ($is_desktop ? 24 : 14)) . 'px',
                'line-height' => 1, // <--- ثابت شد
                'margin' => 0,
            ];

            $desc_styles = [
                'font-weight' => esc_attr($b['descWeight'] ?? '500'),
                'color' => esc_attr($b['descColor'] ?? '#ffffff'),
                'font-size' => esc_attr($b['descSize'] ?? ($is_desktop ? 14 : 12)) . 'px',
                'margin-top' => esc_attr($b['marginTopDescription'] ?? 12) . 'px',
                'margin-bottom' => '0',
                'line-height' => 1.5, // <--- ثابت شد
                // 'width' => ... // <--- حذف شد
                'word-wrap' => 'break-word',
            ];

            // --- تغییرات ---
            $button_styles = [
                'text-decoration' => 'none', 'display' => 'inline-flex', 'align-items' => 'center',
                'justify-content' => 'center', 'transition' => 'background-color 0.3s',
                'padding' => sprintf('%spx %spx', // <--- پدینگ Y/X
                    esc_attr($b['buttonPaddingY'] ?? ($is_desktop ? 12 : 10)),
                    esc_attr($b['buttonPaddingX'] ?? ($is_desktop ? 24 : 16))
                ),
                'background-color' => esc_attr($b['buttonBgColor'] ?? '#124C88'), 
                'color' => esc_attr($b['buttonTextColor'] ?? '#ffffff'),
                'font-size' => esc_attr($b['buttonFontSize'] ?? ($is_desktop ? 14 : 11)) . 'px',
                'border-radius' => esc_attr($b['buttonBorderRadius'] ?? 8) . 'px',
                'font-weight' => esc_attr($b['buttonFontWeight'] ?? '500'),
                'align-self' => $alignment_style['align_self'],
                'line-height' => 1, // <--- ثابت شد
                'margin-top' => esc_attr($b['marginBottomDescription'] ?? 15) . 'px',
            ];
            // --- پایان تغییرات ---

            ob_start();
            ?>
            <div class="yab-content-real yab-content-<?php echo $view; ?>" style="<?php echo $this->get_inline_style_attr($banner_styles); ?> <?php echo $this->get_inline_style_attr($wrapper_styles); ?>">
                <?php if (!empty($b['imageUrl'])): ?>
                    <img src="<?php echo esc_url($b['imageUrl']); ?>" alt="" style="<?php echo $this->get_image_style($b); ?> z-index: <?php echo $image_z_index; ?>;">
                <?php endif; ?>

                <div class="yab-banner-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: <?php echo $overlay_z_index; ?>; <?php echo $this->get_background_style($b); ?>"></div>
                <div class="yab-banner-content" style="<?php echo $this->get_inline_style_attr($content_styles); ?> z-index: 3; text-align: <?php echo $alignment_style['text_align']; ?>; align-items: <?php echo $alignment_style['align_items']; ?>;">
                    
                    <div style="flex-grow: 1;">
                        <h2 style="<?php echo $this->get_inline_style_attr($title_styles); ?>"><?php echo esc_html($b['titleText'] ?? ''); ?></h2>
                        <p style="<?php echo $this->get_inline_style_attr($desc_styles); ?>">
                            <?php echo wp_kses_post(trim($b['descText'] ?? '')); ?>
                        </p>
                    </div>

                    <?php if(!empty($b['buttonText'])): ?>
                    <a href="<?php echo esc_url($b['buttonLink'] ?? '#'); ?>" target="_blank" class="yab-button" style="<?php echo $this->get_inline_style_attr($button_styles); ?>"><?php echo esc_html($b['buttonText']); ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
        
        private function get_inline_style_attr(array $styles): string {
            $style_str = '';
            foreach ($styles as $prop => $value) { 
                // --- FIX: Added check for null or empty string ---
                if ($value !== '' && $value !== null) {
                    $style_str .= $prop . ': ' . $value . '; '; 
                }
            }
            return trim($style_str);
        }
    }
}