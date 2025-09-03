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
            // Fallback to desktop settings if mobile settings don't exist
            $mobile_b = $this->data['single_mobile'] ?? $desktop_b; 
            $banner_id = $this->banner_id;
            
            ob_start();
            ?>
            <style>
                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop { display: flex; }
                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile { display: none; }
                
                @media (max-width: 768px) {
                    .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop { display: none; }
                    .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile { display: flex; }
                }

                /* Hover styles */
                .yab-banner-<?php echo $banner_id; ?>-desktop .yab-button:hover { background-color: <?php echo esc_attr($desktop_b['buttonBgHoverColor'] ?? '#008a7b'); ?> !important; }
                .yab-banner-<?php echo $banner_id; ?>-mobile .yab-button:hover { background-color: <?php echo esc_attr($mobile_b['buttonBgHoverColor'] ?? '#008a7b'); ?> !important; }
            </style>
            
            <div class="yab-wrapper yab-banner-wrapper-<?php echo $banner_id; ?>" style="width: 100%; line-height: 1.2 !important;">
                <?php echo $this->render_view($desktop_b, 'desktop', $banner_id); ?>
                <?php echo $this->render_view($mobile_b, 'mobile', $banner_id); ?>
            </div>
            <?php
            return ob_get_clean();
        }

        private function render_view($b, $view, $banner_id) {
             $banner_styles = [
                'width' => !empty($b['enableCustomDimensions']) ? esc_attr($b['width'] . $b['widthUnit']) : ($view === 'desktop' ? '886px' : '100%'),
                'height' => !empty($b['enableCustomDimensions']) ? esc_attr($b['height'] . $b['heightUnit']) : ($view === 'desktop' ? '178px' : '250px'),
                'border-radius' => esc_attr($b['borderRadius'] ?? 8) . 'px',
                'position' => 'relative', 'overflow' => 'hidden', 'flex-shrink' => '0', 'justify-content' => 'center'
            ];
             if (!empty($b['enableBorder'])) { $banner_styles['border'] = esc_attr($b['borderWidth'] ?? 1) . 'px solid ' . esc_attr($b['borderColor'] ?? '#E0E0E0'); }

            $content_styles = [
                'width' => '100%', 'height' => '100%',
                'padding' => sprintf('%spx %spx %spx %spx', esc_attr($b['paddingTop'] ?? 32), esc_attr($b['paddingRight'] ?? 32), esc_attr($b['paddingBottom'] ?? 32), esc_attr($b['paddingLeft'] ?? 32)),
                'display' => 'flex', 'flex-direction' => 'column', 'z-index' => '10', 'position' => 'relative',
            ];
            
            $button_styles = [
                'text-decoration' => 'none', 'display' => 'inline-flex', 'align-items' => 'center',
                'justify-content' => 'center', 'transition' => 'background-color 0.3s', 'padding' => '8px 16px',
                'background-color' => esc_attr($b['buttonBgColor'] ?? '#00baa4'), 'color' => esc_attr($b['buttonTextColor'] ?? '#ffffff'),
                'font-size' => esc_attr($b['buttonFontSize'] ?? 10) . 'px',
                'width' => !empty($b['buttonWidth']) ? esc_attr($b['buttonWidth'] . $b['buttonWidthUnit']) : 'auto',
                'height' => !empty($b['buttonHeight']) ? esc_attr($b['buttonHeight'] . $b['buttonHeightUnit']) : 'auto',
                'min-width' => !empty($b['buttonMinWidth']) ? esc_attr($b['buttonMinWidth'] . $b['buttonMinWidthUnit']) : 'auto',
                'border-radius' => esc_attr($b['buttonBorderRadius'] ?? 4) . 'px',
                'margin-top' => (isset($b['marginBottomDescription']) && $b['marginBottomDescription'] !== '' && $b['marginBottomDescription'] !== null) ? esc_attr($b['marginBottomDescription']) . 'px' : 'auto',
            ];

            ob_start();
            ?>
            <div class="yab-banner-item yab-banner-<?php echo $banner_id; ?>-<?php echo $view; ?>" style="<?php echo $this->get_inline_style_attr($banner_styles); ?> <?php echo $this->get_background_style($b); ?>">
                <?php if (!empty($b['imageUrl'])): ?>
                    <img src="<?php echo esc_url($b['imageUrl']); ?>" alt="" style="<?php echo $this->get_image_style($b); ?>">
                <?php endif; ?>
                <div style="<?php echo $this->get_inline_style_attr($content_styles); ?> <?php echo $this->get_alignment_style($b); ?>">
                    <h4 style="font-weight: <?php echo esc_attr($b['titleWeight'] ?? '700'); ?>; color: <?php echo esc_attr($b['titleColor'] ?? '#ffffff'); ?>; font-size: <?php echo intval($b['titleSize'] ?? 15); ?>px; margin: 0;"><?php echo esc_html($b['titleText'] ?? ''); ?></h4>
                    <p style="font-weight:<?php echo esc_attr($b['descWeight'] ?? '400'); ?>; color:<?php echo esc_attr($b['descColor'] ?? '#dddddd'); ?>; font-size:<?php echo intval($b['descSize'] ?? 10); ?>px; white-space:pre-wrap; margin-top: <?php echo intval($b['marginTopDescription'] ?? 8); ?>px; margin-bottom: 0;">
                        <?php echo wp_kses_post($b['descText'] ?? ''); ?>
                    </p>
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
            foreach ($styles as $prop => $value) { $style_str .= $prop . ': ' . $value . '; '; }
            return trim($style_str);
        }
    }
}