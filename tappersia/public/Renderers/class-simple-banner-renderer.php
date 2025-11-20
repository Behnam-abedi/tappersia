<?php
// tappersia/public/Renderers/class-simple-banner-renderer.php

if (!class_exists('Yab_Simple_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Simple_Banner_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            if (empty($this->data['simple'])) {
                return '';
            }
            
            $desktop_b = $this->data['simple'];
            $mobile_b = $this->data['simple_mobile'] ?? $desktop_b; 
            // Ensure mobile view inherits the direction from desktop settings explicitly
            $mobile_b['direction'] = $desktop_b['direction'] ?? 'ltr';
            $banner_id = $this->banner_id;
            
            // +++ START: افزودن استایل هاور +++
            $desktop_hover_color = esc_attr($desktop_b['buttonBgHoverColor'] ?? $desktop_b['buttonBgColor']);
            // *** FIX: Mobile hover color should use mobile settings first, then desktop ***
            $mobile_hover_color = esc_attr($mobile_b['buttonBgHoverColor'] ?? $desktop_hover_color); 
            // +++ END: افزودن استایل هاور +++

            ob_start();
            ?>
            <style>
                .yab-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile { display: none; }
                .yab-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop { display: flex; }
                
                /* +++ START: افزودن استایل هاور +++ */
                .yab-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop .yab-simple-button:hover {
                    background-color: <?php echo $desktop_hover_color; ?> !important;
                }
                /* +++ END: افزودن استایل هاور +++ */

                @media (max-width: 768px) {
                    .yab-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop { display: none; }
                    .yab-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile { display: flex; }
                
                    /* +++ START: افزودن استایل هاور +++ */
                    .yab-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile .yab-simple-button:hover {
                         background-color: <?php echo $mobile_hover_color; ?> !important;
                    }
                    /* +++ END: افزودن استایل هاور +++ */
                }
            </style>
            
            <div class="yab-wrapper yab-simple-banner-wrapper-<?php echo $banner_id; ?>" style="width: 100%; direction: ltr;">
                <?php // *** FIX: Pass desktop settings as content source for both *** ?>
                <?php echo $this->render_view($desktop_b, 'desktop', $banner_id, $desktop_b); ?>
                <?php echo $this->render_view($mobile_b, 'mobile', $banner_id, $desktop_b); ?>
            </div>
            <?php
            return ob_get_clean();
        }

        /**
         * @param array $b The settings for the current view (desktop or mobile) - USED FOR STYLES
         * @param string $view The view name ('desktop' or 'mobile')
         * @param int $banner_id The banner ID
         * @param array $content_source The desktop settings - USED FOR CONTENT (text, links)
         */
        private function render_view($b, $view, $banner_id, $content_source) {
             ob_start();
             
             // --- START: Added Border Logic ---
            $border_style = 'border-radius: ' . esc_attr($b['borderRadius'] ?? 10) . 'px;';
            if (!empty($b['enableBorder']) && $b['enableBorder']) {
                // Use desktop border color as fallback for mobile
                $border_color = $b['borderColor'] ?? ($content_source['borderColor'] ?? '#E0E0E0');
                $border_style .= ' border: ' . esc_attr($b['borderWidth'] ?? 1) . 'px solid ' . esc_attr($border_color) . ';';
            } else {
                $border_style .= ' border: none;';
            }
            // --- END: Added Border Logic ---

            // --- START: Use $b for styles, $content_source for content ---
            $text_color = esc_attr($b['textColor'] ?? $content_source['textColor']);
            $button_bg_color = esc_attr($b['buttonBgColor'] ?? $content_source['buttonBgColor']);
            $button_text_color = esc_attr($b['buttonTextColor'] ?? $content_source['buttonTextColor']);
            
            $text = esc_html($content_source['text']);
            $button_text = esc_html($content_source['buttonText']);
            $button_link = esc_url($content_source['buttonLink']);
            // --- END: Use $b for styles, $content_source for content ---
            
            ?>
            <div class="yab-banner-item yab-banner-<?php echo $view; ?>" 
                 style="width: 100%; 
                        height: auto; 
                        min-height: <?php echo esc_attr($b['minHeight']); ?>px;
                        <?php echo $border_style; ?> 
                        <?php echo $this->get_background_style($b); ?>;
                        padding: <?php echo esc_attr($b['paddingY']); ?>px <?php echo esc_attr($b['paddingX'] . ($b['paddingXUnit'] ?? 'px')); ?>;
                        align-items: center;
                        justify-content: space-between;
                        box-sizing: border-box;
                        flex-direction: <?php echo ($b['direction'] === 'rtl') ? 'row-reverse' : 'row'; ?>;
                        gap: 15px;
                        ">
                <span style="font-size: <?php echo esc_attr($b['textSize']); ?>px;
                             font-weight: <?php echo esc_attr($b['textWeight']); ?>;
                             color: <?php echo $text_color; ?>;
                             flex-grow: 1;
                             max-width: <?php echo esc_attr(($b['contentWidth'] ?? 100) . ($b['contentWidthUnit'] ?? '%')); ?>;
                             text-align: <?php echo esc_attr($b['direction'] === 'rtl' ? 'right' : 'left'); ?>;">
                    <?php echo $text; ?>
                </span>
                <a href="<?php echo $button_link; ?>" 
                   target="_blank" 
                   class="yab-simple-button" 
                   style="background-color: <?php echo $button_bg_color; ?>;
                          border-radius: <?php echo esc_attr($b['buttonBorderRadius']); ?>px;
                          color: <?php echo $button_text_color; ?>;
                          font-size: <?php echo esc_attr($b['buttonFontSize']); ?>px;
                          font-weight: <?php echo esc_attr($b['buttonFontWeight']); ?>;
                          padding: <?php echo esc_attr($b['buttonPaddingY']); ?>px <?php echo esc_attr($b['buttonPaddingX']); ?>px;
                          min-width: <?php echo esc_attr($b['buttonMinWidth']); ?>px;
                          text-decoration: none;
                          text-align: center;
                          box-sizing: border-box;
                          flex-shrink: 0;
                          line-height: 1;
                          transition: background-color 0.3s; 
                          ">
                    <?php echo $button_text; ?>
                </a>
            </div>
            <?php
            return ob_get_clean();
        }
    }
}