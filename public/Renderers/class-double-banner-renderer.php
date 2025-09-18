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

            ob_start();
            ?>
            <style>
                .yab-double-banner-wrapper-<?php echo $banner_id; ?> {
                    display: flex;
                    gap: 20px;
                    width: 100%;
                    justify-content: center;
                    line-height: 1.2 !important;
                    direction: ltr;
                }
                .yab-double-banner-wrapper-<?php echo $banner_id; ?> .yab-d-mobile { display: none; }
                .yab-double-banner-wrapper-<?php echo $banner_id; ?> .yab-d-desktop { display: flex; }

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
                    .yab-double-banner-wrapper-<?php echo $banner_id; ?> {
                        flex-direction: column;
                    }
                    .yab-double-banner-wrapper-<?php echo $banner_id; ?> .yab-d-desktop { display: none; }
                    .yab-double-banner-wrapper-<?php echo $banner_id; ?> .yab-d-mobile { display: flex; }
                }
            </style>
            <div class="yab-double-banner-wrapper-<?php echo $banner_id; ?>">
                <?php 
                    echo $this->render_view($desktop_banners['left'], 'desktop', 'left', $banner_id);
                    echo $this->render_view($desktop_banners['right'], 'desktop', 'right', $banner_id);
                    echo $this->render_view($mobile_banners['left'], 'mobile', 'left', $banner_id);
                    echo $this->render_view($mobile_banners['right'], 'mobile', 'right', $banner_id);
                ?>
            </div>
            <?php
            return ob_get_clean();
        }

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
                'width' => '100%', 'height' => '100%',
                'padding' => sprintf('%spx %spx %spx %spx', esc_attr($b['paddingTop']), esc_attr($b['paddingRight']), esc_attr($b['paddingBottom']), esc_attr($b['paddingLeft'])),
                'display' => 'flex', 'flex-direction' => 'column', 'position' => 'relative', 'z-index' => 3,
                'align-items' => $alignment_style['align_items'],
                'text-align' => $alignment_style['text_align'],
            ];
            
            $button_styles = [
                'text-decoration' => 'none', 'display' => 'inline-flex', 'align-items' => 'center', 'justify-content' => 'center',
                'transition' => 'background-color 0.3s',
                'padding' => '8px 16px',
                'background-color' => esc_attr($b['buttonBgColor']), 'color' => esc_attr($b['buttonTextColor']),
                'font-size' => esc_attr($b['buttonFontSize']) . 'px',
                'min-width' => esc_attr($b['buttonMinWidth']) . esc_attr($b['buttonMinWidthUnit'] ?? 'px'),
                'border-radius' => esc_attr($b['buttonBorderRadius']) . 'px',
                'margin-top' => 'auto',
                'font-weight' => esc_attr($b['buttonFontWeight']),
                'align-self' => $alignment_style['align_self'],
            ];

            $image_z_index = ($b['layerOrder'] ?? 'image-below-overlay') === 'image-below-overlay' ? 1 : 2;
            $overlay_z_index = $image_z_index === 1 ? 2 : 1;

            ob_start();
            ?>
            <div class="yab-banner-item yab-d-<?php echo $view; ?> yab-banner-<?php echo "$banner_id-$view-$key"; ?>" style="<?php echo $this->get_inline_style_attr($banner_styles); ?>">
                
                <?php if (!empty($b['imageUrl'])): ?>
                    <img src="<?php echo esc_url($b['imageUrl']); ?>" alt="" style="<?php echo $this->get_image_style($b); ?> z-index: <?php echo $image_z_index; ?>;">
                <?php endif; ?>

                <div class="yab-banner-overlay" style="position: absolute; inset: 0; z-index: <?php echo $overlay_z_index; ?>; <?php echo $this->get_background_style($b); ?>"></div>

                <div class="yab-banner-content" style="<?php echo $this->get_inline_style_attr($content_styles); ?>">
                    <h4 style="font-weight: <?php echo esc_attr($b['titleWeight']); ?>; color: <?php echo esc_attr($b['titleColor']); ?>; font-size: <?php echo intval($b['titleSize']); ?>px; margin: 0;"><?php echo esc_html($b['titleText'] ?? ''); ?></h4>
                    <p style="margin-top: 12px; margin-bottom: 25px; font-weight: <?php echo esc_attr($b['descWeight']); ?>; color: <?php echo esc_attr($b['descColor']); ?>; font-size: <?php echo intval($b['descSize']); ?>px; white-space: pre-wrap; width: <?php echo esc_attr($b['descWidth'] . ($b['descWidthUnit'] ?? '%')); ?>;"><?php echo wp_kses_post($b['descText'] ?? ''); ?></p>
                    
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

