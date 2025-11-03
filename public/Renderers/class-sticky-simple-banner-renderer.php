<?php
// tappersia/public/Renderers/class-sticky-simple-banner-renderer.php

if (!class_exists('Yab_Sticky_Simple_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Sticky_Simple_Banner_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            if (empty($this->data['sticky_simple'])) {
                return '';
            }
            
            $desktop_b = $this->data['sticky_simple'];
            $mobile_b = $this->data['sticky_simple_mobile'] ?? $desktop_b; 
            // Ensure mobile view inherits the direction from desktop settings explicitly
            $mobile_b['direction'] = $desktop_b['direction'] ?? 'ltr';
            $banner_id = $this->banner_id;
            
            // +++ START: افزودن استایل هاور +++
            $desktop_hover_color = esc_attr($desktop_b['buttonBgHoverColor'] ?? $desktop_b['buttonBgColor']);
            $mobile_hover_color = esc_attr($mobile_b['buttonBgHoverColor'] ?? $desktop_hover_color);
            // +++ END: افزودن استایل هاور +++
            
            ob_start();
            ?>
            <style>
                .yab-sticky-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile { display: none; }
                .yab-sticky-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop { display: flex; }
                
                /* +++ START: افزودن استایل هاور +++ */
                .yab-sticky-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop .yab-sticky-button:hover {
                    background-color: <?php echo $desktop_hover_color; ?> !important;
                }
                /* +++ END: افزودن استایل هاور +++ */
                
                @media (max-width: 768px) {
                    .yab-sticky-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop { display: none; }
                    .yab-sticky-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile { display: flex; }

                    /* +++ START: افزودن استایل هاور +++ */
                    .yab-sticky-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile .yab-sticky-button:hover {
                        background-color: <?php echo $mobile_hover_color; ?> !important;
                    }
                    /* +++ END: افزودن استایل هاور +++ */
                }
            </style>

            <div class="yab-wrapper yab-sticky-simple-banner-wrapper-<?php echo $banner_id; ?>" style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 99999; width: 100%; direction: ltr;">
                <?php echo $this->render_view($desktop_b, 'desktop', $banner_id); ?>
                <?php echo $this->render_view($mobile_b, 'mobile', $banner_id); ?>
            </div>

            <?php
            return ob_get_clean();
        }

        private function render_view($b, $view, $banner_id) {
             ob_start();

             // --- START: Added Border Logic ---
            $border_style = 'border-radius: ' . esc_attr($b['borderRadius'] ?? 0) . 'px;';
            if (!empty($b['enableBorder']) && $b['enableBorder']) {
                $border_style .= ' border: ' . esc_attr($b['borderWidth'] ?? 1) . 'px solid ' . esc_attr($b['borderColor'] ?? '#E0E0E0') . ';';
            } else {
                $border_style .= ' border: none;';
            }
            // --- END: Added Border Logic ---

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
                             color: <?php echo esc_attr($b['textColor']); ?>;
                             flex-grow: 1;
                             max-width: <?php echo esc_attr(($b['contentWidth'] ?? 100) . ($b['contentWidthUnit'] ?? '%')); ?>;
                             text-align: <?php echo esc_attr($b['direction'] === 'rtl' ? 'right' : 'left'); ?>;">
                    <?php echo esc_html($b['text']); ?>
                </span>
                <a href="<?php echo esc_url($b['buttonLink']); ?>" 
                   target="_blank" 
                   class="yab-sticky-button" 
                   style="background-color: <?php echo esc_attr($b['buttonBgColor']); ?>;
                          border-radius: <?php echo esc_attr($b['buttonBorderRadius']); ?>px;
                          color: <?php echo esc_attr($b['buttonTextColor']); ?>;
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
                    <?php echo esc_html($b['buttonText']); ?>
                </a>
            </div>
            <?php
            return ob_get_clean();
        }
    }
}