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
            
            ob_start();
            ?>
            <style>
                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile { display: none; }
                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop { display: flex; }
                
                @media (max-width: 768px) {
                    .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop { display: none; }
                    .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile { display: flex; }
                }

                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop .yab-button:hover { background-color: <?php echo esc_attr($desktop_b['buttonBgHoverColor'] ?? '#10447B'); ?> !important; }
                .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile .yab-button:hover { background-color: <?php echo esc_attr($mobile_b['buttonBgHoverColor'] ?? '#008a7b'); ?> !important; }
            </style>
            
            <div class="yab-wrapper yab-banner-wrapper-<?php echo $banner_id; ?>" style="width: 100%; direction: ltr;">
                <?php echo $this->render_view($desktop_b, 'desktop', $banner_id); ?>
                <?php echo $this->render_view($mobile_b, 'mobile', $banner_id); ?>
            </div>
            <?php
            return ob_get_clean();
        }

        private function render_view($b, $view, $banner_id) {
             $is_desktop = $view === 'desktop';
             
             $banner_styles = [
                'width' => !empty($b['enableCustomDimensions']) ? esc_attr($b['width'] . $b['widthUnit']) : '100%',
                'height' => 'auto',
                'min-height' => !empty($b['enableCustomDimensions']) ? esc_attr($b['minHeight'] . ($b['minHeightUnit'] ?? 'px')) : ($is_desktop ? '190px' : '145px'),
                'border-radius' => esc_attr($b['borderRadius'] ?? 16) . 'px',
                'position' => 'relative', 'overflow' => 'hidden', 'flex-shrink' => '0'
            ];
             if (!empty($b['enableBorder'])) { $banner_styles['border'] = esc_attr($b['borderWidth'] ?? 1) . 'px solid ' . esc_attr($b['borderColor'] ?? '#E0E0E0'); }

            $content_styles = [
                'padding' => sprintf('%spx %spx %spx %spx', 
                    esc_attr($b['paddingTop'] ?? ($is_desktop ? 34 : 20)), 
                    esc_attr($b['paddingRight'] ?? ($is_desktop ? 34 : 22)), 
                    esc_attr($b['paddingBottom'] ?? ($is_desktop ? 34 : 15)), 
                    esc_attr($b['paddingLeft'] ?? ($is_desktop ? 34 : 22))
                ),
                'display' => 'flex', 'flex-direction' => 'column', 'z-index' => '10', 'position' => 'relative', 'flex-grow' => '1', 'width' => '100%'
            ];
            
            $alignment_style = $this->get_alignment_style($b);
            
            $title_styles = [
                'font-weight' => esc_attr($b['titleWeight'] ?? '700'),
                'color' => esc_attr($b['titleColor'] ?? '#ffffff'),
                'font-size' => esc_attr($b['titleSize'] ?? ($is_desktop ? 24 : 14)) . 'px',
                'line-height' => esc_attr($b['titleLineHeight'] ?? ($is_desktop ? 1 : 1.4)),
                'margin' => 0,
            ];

            $desc_styles = [
                'font-weight' => esc_attr($b['descWeight'] ?? '500'),
                'color' => esc_attr($b['descColor'] ?? '#ffffff'),
                'font-size' => esc_attr($b['descSize'] ?? ($is_desktop ? 14 : 12)) . 'px',
                'margin-top' => esc_attr($b['marginTopDescription'] ?? 12) . 'px',
                'margin-bottom' => '0',
                'line-height' => esc_attr($b['descLineHeight'] ?? 1.5),
                'width' => !empty($b['descWidth']) ? esc_attr($b['descWidth'] . ($b['descWidthUnit'] ?? '%')) : '100%',
                'word-wrap' => 'break-word',
            ];

            $button_styles = [
                'text-decoration' => 'none', 'display' => 'inline-flex', 'align-items' => 'center',
                'justify-content' => 'center', 'transition' => 'background-color 0.3s',
                'padding' => sprintf('%spx %spx %spx %spx', 
                    esc_attr($b['buttonPaddingTop'] ?? ($is_desktop ? 12 : 10)),
                    esc_attr($b['buttonPaddingRight'] ?? ($is_desktop ? 24 : 16)),
                    esc_attr($b['buttonPaddingBottom'] ?? ($is_desktop ? 12 : 10)),
                    esc_attr($b['buttonPaddingLeft'] ?? ($is_desktop ? 24 : 16))
                ),
                'background-color' => esc_attr($b['buttonBgColor'] ?? '#124C88'), 
                'color' => esc_attr($b['buttonTextColor'] ?? '#ffffff'),
                'font-size' => esc_attr($b['buttonFontSize'] ?? ($is_desktop ? 14 : 11)) . 'px',
                'border-radius' => esc_attr($b['buttonBorderRadius'] ?? 8) . 'px',
                'font-weight' => esc_attr($b['buttonFontWeight'] ?? '500'),
                'align-self' => $alignment_style['align_self'],
                'line-height' => esc_attr($b['buttonLineHeight'] ?? 1),
                'margin-top' => esc_attr($b['marginBottomDescription'] ?? 15) . 'px',
            ];

            ob_start();
            ?>
            <div class="yab-banner-item yab-banner-<?php echo $view; ?>" style="<?php echo $this->get_inline_style_attr($banner_styles); ?>">
                <?php if (!empty($b['imageUrl'])): ?>
                    <img src="<?php echo esc_url($b['imageUrl']); ?>" alt="" style="<?php echo $this->get_image_style($b); ?> z-index: 1;">
                <?php endif; ?>

                <div class="yab-banner-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 2; <?php echo $this->get_background_style($b); ?>"></div>

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
            foreach ($styles as $prop => $value) { $style_str .= $prop . ': ' . $value . '; '; }
            return trim($style_str);
        }
    }
}