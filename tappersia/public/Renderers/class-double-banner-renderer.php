<?php
// tappersia/public/Renderers/class-double-banner-renderer.php

if (!class_exists('Yab_Double_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Double_Banner_Renderer extends Yab_Abstract_Banner_Renderer {
        
        public function render(): string {
            if (empty($this->data['double']['desktop'])) {
                return '';
            }

            $desktop_banners = $this->data['double']['desktop'];
            $mobile_banners = $this->data['double']['mobile'] ?? $desktop_banners;
            $banner_id = $this->banner_id;

            // --- START: Data for JS ---
            $desktop_l_has_img = !empty($desktop_banners['left']['imageUrl']);
            $desktop_r_has_img = !empty($desktop_banners['right']['imageUrl']);
            $mobile_l_has_img = !empty($mobile_banners['left']['imageUrl']);
            $mobile_r_has_img = !empty($mobile_banners['right']['imageUrl']);
            
            $desktop_l_img_url = $desktop_l_has_img ? esc_js($desktop_banners['left']['imageUrl']) : '';
            $desktop_r_img_url = $desktop_r_has_img ? esc_js($desktop_banners['right']['imageUrl']) : '';
            $mobile_l_img_url = $mobile_l_has_img ? esc_js($mobile_banners['left']['imageUrl']) : '';
            $mobile_r_img_url = $mobile_r_has_img ? esc_js($mobile_banners['right']['imageUrl']) : '';
            // --- END: Data for JS ---

            ob_start();
            ?>
            <style>
                /* --- START: Skeleton/Content Visibility --- */
                .yab-double-banner-wrapper-<?php echo $banner_id; ?> .yab-double-content-wrapper {
                    visibility: hidden;
                    opacity: 0;
                    transition: opacity 0.3s ease-in-out;
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                }
                .yab-double-banner-wrapper-<?php echo $banner_id; ?> .yab-double-skeleton-wrapper {
                    position: relative;
                    z-index: 1;
                }
                .yab-double-banner-wrapper-<?php echo $banner_id; ?> .yab-double-content-wrapper.is-loaded {
                    visibility: visible;
                    opacity: 1;
                    z-index: 2;
                }
                .yab-double-banner-wrapper-<?php echo $banner_id; ?> .yab-double-skeleton-wrapper.is-hidden {
                    display: none;
                }
                /* --- END: Skeleton/Content Visibility --- */

                .yab-double-banner-wrapper-<?php echo $banner_id; ?> {
                    display: flex; /* Changed from flex to block to let children control layout */
                    width: 100%;
                    justify-content: center;
                    direction: ltr;
                }
                 .yab-double-banner-wrapper-<?php echo $banner_id; ?> .yab-d-mobile { display: none; }
                .yab-double-banner-wrapper-<?php echo $banner_id; ?> .yab-d-desktop {
                    display: flex;
                    gap: 20px;
                    width: 100%;
                    position: relative; /* Needed for absolute content */
                }

                <?php foreach (['desktop', 'mobile'] as $view): ?>
                    <?php $banners_to_style = ($view === 'desktop') ? $desktop_banners : $mobile_banners; ?>
                    <?php foreach ($banners_to_style as $key => $b): ?>
                        <?php if(!empty($b['buttonText']) && !empty($b['buttonBgHoverColor'])): ?>
                        .yab-banner-<?php echo $banner_id; ?>-<?php echo $view; ?>-<?php echo $key; ?> .yab-button:hover {
                            background-color: <?php echo esc_attr($b['buttonBgHoverColor']); ?> !important;
                        }
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>

                @media (max-width: 768px) {
                    .yab-double-banner-wrapper-<?php echo $banner_id; ?> .yab-d-desktop { display: none; }
                    .yab-double-banner-wrapper-<?php echo $banner_id; ?> .yab-d-mobile {
                        display: flex;
                        flex-direction: column;
                        gap: 20px;
                        width: 100%;
                        position: relative; /* Needed for absolute content */
                    }
                }
            </style>
            
            <div class="yab-double-banner-wrapper-<?php echo $banner_id; ?>">
                
                <div class="yab-d-desktop">
                    <div class="yab-double-skeleton-wrapper yab-skeleton-desktop" style="display: flex; gap: 20px; width: 100%;">
                        <?php 
                        echo $this->render_skeleton($desktop_banners['left'], 'desktop', 'left');
                        echo $this->render_skeleton($desktop_banners['right'], 'desktop', 'right');
                        ?>
                    </div>
                    <div class="yab-double-content-wrapper yab-content-desktop" style="display: flex; gap: 20px; width: 100%;">
                        <?php 
                        echo $this->render_view($desktop_banners['left'], 'desktop', 'left', $banner_id);
                        echo $this->render_view($desktop_banners['right'], 'desktop', 'right', $banner_id);
                        ?>
                    </div>
                </div>

                <div class="yab-d-mobile">
                    <div class="yab-double-skeleton-wrapper yab-skeleton-mobile" style="display: flex; flex-direction: column; gap: 20px; width: 100%;">
                        <?php 
                        echo $this->render_skeleton($mobile_banners['left'], 'mobile', 'left');
                        echo $this->render_skeleton($mobile_banners['right'], 'mobile', 'right');
                        ?>
                    </div>
                    <div class="yab-double-content-wrapper yab-content-mobile" style="display: flex; flex-direction: column; gap: 20px; width: 100%;">
                        <?php 
                        echo $this->render_view($mobile_banners['left'], 'mobile', 'left', $banner_id);
                        echo $this->render_view($mobile_banners['right'], 'mobile', 'right', $banner_id);
                        ?>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var wrapper_<?php echo $banner_id; ?> = document.querySelector('.yab-double-banner-wrapper-<?php echo $banner_id; ?>');
                    if (!wrapper_<?php echo $banner_id; ?>) return;

                    var desktop_skeleton = wrapper_<?php echo $banner_id; ?>.querySelector('.yab-skeleton-desktop');
                    var desktop_content = wrapper_<?php echo $banner_id; ?>.querySelector('.yab-content-desktop');
                    var mobile_skeleton = wrapper_<?php echo $banner_id; ?>.querySelector('.yab-skeleton-mobile');
                    var mobile_content = wrapper_<?php echo $banner_id; ?>.querySelector('.yab-content-mobile');

                    var desktop_l_has_img = <?php echo json_encode($desktop_l_has_img); ?>;
                    var desktop_r_has_img = <?php echo json_encode($desktop_r_has_img); ?>;
                    var mobile_l_has_img = <?php echo json_encode($mobile_l_has_img); ?>;
                    var mobile_r_has_img = <?php echo json_encode($mobile_r_has_img); ?>;

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

                    function preloadImage(src) {
                        images_to_load++;
                        var img = new Image();
                        img.onload = onImageLoaded;
                        img.onerror = onImageLoaded; // Count as "loaded" even on error
                        img.src = src;
                    }

                    if (desktop_l_has_img) { preloadImage('<?php echo $desktop_l_img_url; ?>'); }
                    if (desktop_r_has_img) { preloadImage('<?php echo $desktop_r_img_url; ?>'); }
                    if (mobile_l_has_img) { preloadImage('<?php echo $mobile_l_img_url; ?>'); }
                    if (mobile_r_has_img) { preloadImage('<?php echo $mobile_r_img_url; ?>'); }

                    if (images_to_load === 0) {
                        setTimeout(showBanners, 50);
                    }
                });
            </script>
            <?php
            return ob_get_clean();
        }

        /**
         * Renders the placeholder skeleton.
         */
        private function render_skeleton($b, $view, $key) {
            $is_desktop = $view === 'desktop';

            $banner_styles = [
                'width' => $is_desktop ? ($b['enableCustomDimensions'] ? esc_attr($b['width'] . $b['widthUnit']) : '50%') : '100%',
                'min-height' => esc_attr($b['minHeight'] . ($b['minHeightUnit'] ?? 'px')),
                'height' => 'auto',
                'border-radius' => esc_attr($b['borderRadius'] ?? 16) . 'px',
                'position' => 'relative', 'overflow' => 'hidden', 'flex-shrink' => '0',
                'background-color' => '#f4f4f4' // Skeleton base color
            ];
            if (!empty($b['enableBorder'])) { 
                $banner_styles['border'] = esc_attr($b['borderWidth'] ?? 0) . 'px solid ' . esc_attr($b['borderColor'] ?? '#FFFFFF');
            }

            // Get content padding for internal layout
            $content_padding = sprintf('%spx %spx %spx %spx', 
                esc_attr($b['paddingTop']), 
                esc_attr($b['paddingRight']), 
                esc_attr($b['paddingBottom']), 
                esc_attr($b['paddingLeft'])
            );
            
            $alignment = $this->get_alignment_style($b);

            ob_start();
            ?>
            <div class="yab-double-skeleton " style="<?php echo $this->get_inline_style_attr($banner_styles); ?>">
                <div class="yab-skeleton-content" style="padding: <?php echo $content_padding; ?>; align-items: <?php echo $alignment['align_items']; ?>; text-align: <?php echo $alignment['text_align']; ?>; display: flex; flex-direction: column; height: <?php echo esc_attr($b['minHeight'] . ($b['minHeightUnit'] ?? 'px')); ?>; box-sizing: border-box;">
                    <div style="width:100%">
                        <div class="yab-skeleton-text-lg yab-skeleton-loader"></div>
                        <div class="yab-skeleton-text-md yab-skeleton-loader"></div>
                    </div>
                    <div class="yab-skeleton-button yab-skeleton-loader" style="align-self: <?php echo $alignment['align_self']; ?>; margin-top: <?php echo esc_attr($b['buttonMarginTop']); ?>px; margin-bottom: <?php echo esc_attr($b['buttonMarginBottom']); ?>px;"></div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }


        /**
         * Renders the actual banner content (initially hidden by CSS).
         */
        private function render_view($b, $view, $key, $banner_id) {
            $is_desktop = $view === 'desktop';
            $banner_styles = [
                'width' => $is_desktop ? ($b['enableCustomDimensions'] ? esc_attr($b['width'] . $b['widthUnit']) : '50%') : '100%',
                'min-height' => esc_attr($b['minHeight'] . ($b['minHeightUnit'] ?? 'px')),
                'height' => 'auto',
                'border-radius' => esc_attr($b['borderRadius'] ?? 16) . 'px',
                'position' => 'relative', 'overflow' => 'hidden', 'flex-shrink' => '0',
            ];
            if (!empty($b['enableBorder'])) { 
                $banner_styles['border'] = esc_attr($b['borderWidth'] ?? 0) . 'px solid ' . esc_attr($b['borderColor'] ?? '#FFFFFF');
            }

            $alignment_style = $this->get_alignment_style($b);

            $content_styles = [
                'width' => '100%',
                'height' => '100%', // Ensure content div fills the wrapper
                'padding' => sprintf('%spx %spx %spx %spx', esc_attr($b['paddingTop']), esc_attr($b['paddingRight']), esc_attr($b['paddingBottom']), esc_attr($b['paddingLeft'])),
                'display' => 'flex', 'flex-direction' => 'column', 'position' => 'relative', 'z-index' => 3,
                'align-items' => $alignment_style['align_items'],
                'text-align' => $alignment_style['text_align'],
                'box-sizing' => 'border-box', // Ensure padding is included
            ];
            
            $button_styles = [
                'text-decoration' => 'none', 'display' => 'inline-flex', 'align-items' => 'center', 'justify-content' => 'center',
                'transition' => 'background-color 0.3s',
                'padding' => sprintf('%spx %spx %spx %spx', esc_attr($b['buttonPaddingTop']), esc_attr($b['buttonPaddingRight']), esc_attr($b['buttonPaddingBottom']), esc_attr($b['buttonPaddingLeft'])),
                'background-color' => esc_attr($b['buttonBgColor']), 'color' => esc_attr($b['buttonTextColor']),
                'font-size' => esc_attr($b['buttonFontSize']) . 'px',
                'border-radius' => esc_attr($b['buttonBorderRadius']) . 'px',
                'font-weight' => esc_attr($b['buttonFontWeight']),
                'align-self' => $alignment_style['align_self'],
                'line-height' => esc_attr($b['buttonLineHeight']),
                'margin-top' => esc_attr($b['buttonMarginTop']) . 'px',
                'margin-bottom' => esc_attr($b['buttonMarginBottom']) . 'px',
            ];

            $image_z_index = ($b['layerOrder'] ?? 'image-below-overlay') === 'image-below-overlay' ? 1 : 2;
            $overlay_z_index = $image_z_index === 1 ? 2 : 1;

            ob_start();
            ?>
            <div class="yab-banner-item yab-banner-<?php echo "$banner_id-$view-$key"; ?>" style="<?php echo $this->get_inline_style_attr($banner_styles); ?>">
                
                <?php if (!empty($b['imageUrl'])): ?>
                    <img src="<?php echo esc_url($b['imageUrl']); ?>" alt="" style="<?php echo $this->get_image_style($b); ?> z-index: <?php echo $image_z_index; ?>;">
                <?php endif; ?>

                <div class="yab-banner-overlay" style="position: absolute; inset: 0; z-index: <?php echo $overlay_z_index; ?>; <?php echo $this->get_background_style($b); ?>"></div>

                <div class="yab-banner-content" style="<?php echo $this->get_inline_style_attr($content_styles); ?>">
                    <div style="flex-grow: 1;">
                        <h2 style="font-weight: <?php echo esc_attr($b['titleWeight']); ?>; color: <?php echo esc_attr($b['titleColor']); ?>; font-size: <?php echo intval($b['titleSize']); ?>px; margin: 0; line-height: <?php echo esc_attr($b['titleLineHeight']); ?>;"><?php echo esc_html($b['titleText'] ?? ''); ?></h2>
                        <p style="font-weight: <?php echo esc_attr($b['descWeight']); ?>; color: <?php echo esc_attr($b['descColor']); ?>; font-size: <?php echo intval($b['descSize']); ?>px; white-space: pre-wrap; width: <?php echo esc_attr($b['descWidth'] . ($b['descWidthUnit'] ?? '%')); ?>; margin-top: <?php echo esc_attr($b['marginTopDescription']); ?>px; margin-bottom: 0; line-height: <?php echo esc_attr($b['descLineHeight']); ?>;"><?php echo wp_kses_post($b['descText'] ?? ''); ?></p>
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
                if ($value !== '' && $value !== null) {
                    $style_str .= $prop . ': ' . $value . '; '; 
                }
            }
            return trim($style_str);
        }
    }
}