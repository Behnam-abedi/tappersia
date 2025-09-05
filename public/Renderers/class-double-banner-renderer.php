<?php
// tappersia/public/Renderers/class-double-banner-renderer.php

if (!class_exists('Yab_Double_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Double_Banner_Renderer extends Yab_Abstract_Banner_Renderer {
        public function render(): string {
            if (empty($this->data['left']) || empty($this->data['right'])) {
                return '';
            }

            $desktop = ['left' => $this->data['left'], 'right' => $this->data['right']];
            $mobile = [
                'left' => $this->data['left_mobile'] ?? $this->data['left'],
                'right' => $this->data['right_mobile'] ?? $this->data['right'],
            ];
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
                <?php foreach (['desktop' => $desktop, 'mobile' => $mobile] as $view => $banners): ?>
                    <?php foreach ($banners as $key => $b): if (!empty($b['buttonText']) && !empty($b['buttonBgHoverColor'])): ?>
                        .yab-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-<?php echo $view; ?> .yab-banner-<?php echo $key; ?> .yab-button:hover { background-color: <?php echo esc_attr($b['buttonBgHoverColor']); ?> !important; }
                    <?php endif; endforeach; ?>
                <?php endforeach; ?>
            </style>
            <div class="yab-wrapper yab-banner-wrapper-<?php echo $banner_id; ?>" style="width:100%; line-height:1.2!important; direction:ltr;">
                <?php echo $this->render_view($desktop, 'desktop'); ?>
                <?php echo $this->render_view($mobile, 'mobile'); ?>
            </div>
            <?php
            return ob_get_clean();
        }

        private function render_view(array $banners, string $view) {
            $wrapper_style = $view === 'desktop' ? 'flex-direction: row;' : 'flex-direction: column;';
            ob_start(); ?>
            <div class="yab-banner-<?php echo $view; ?>" style="display:flex; <?php echo $wrapper_style; ?> gap:20px; width:100%; justify-content:center;">
                <?php foreach ($banners as $key => $b): ?>
                    <div class="yab-banner-item yab-banner-<?php echo $key; ?>" style="<?php echo $this->get_inline_style_attr($this->build_container_styles($b)); ?>">
                        <?php echo $this->render_layers($b); ?>
                        <?php $alignment = $this->get_alignment_style($b); ?>
                        <div class="yab-banner-content" style="<?php echo $this->get_inline_style_attr($this->build_content_styles($b)); ?> text-align: <?php echo $alignment['text_align']; ?>; align-items: <?php echo $alignment['align_items']; ?>;">
                            <h4 style="font-weight: <?php echo esc_attr($b['titleWeight'] ?? '700'); ?>; color: <?php echo esc_attr($b['titleColor'] ?? '#ffffff'); ?>; font-size: <?php echo intval($b['titleSize'] ?? 20); ?>px; margin:0;">
                                <?php echo esc_html($b['titleText'] ?? ''); ?>
                            </h4>
                            <p style="<?php echo $this->get_inline_style_attr($this->build_desc_styles($b)); ?>">
                                <?php echo wp_kses_post(trim($b['descText'] ?? '')); ?>
                            </p>
                            <?php if (!empty($b['buttonText'])): ?>
                                <a href="<?php echo esc_url($b['buttonLink'] ?? '#'); ?>" target="_blank" class="yab-button" style="<?php echo $this->get_inline_style_attr($this->build_button_styles($b, $alignment)); ?>">
                                    <?php echo esc_html($b['buttonText']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php return ob_get_clean();
        }

        private function render_layers(array $b): string {
            $layer = $b['layerOrder'] ?? 'overlay-top';
            ob_start();
            if ($layer === 'overlay-top') {
                if (!empty($b['imageUrl'])) {
                    echo '<img src="' . esc_url($b['imageUrl']) . '" alt="" style="' . $this->get_image_style($b) . ' z-index:1;">';
                }
                echo '<div class="yab-banner-overlay" style="position:absolute;top:0;left:0;right:0;bottom:0;z-index:2;' . $this->get_background_style($b) . '"></div>';
            } else {
                echo '<div class="yab-banner-overlay" style="position:absolute;top:0;left:0;right:0;bottom:0;z-index:1;' . $this->get_background_style($b) . '"></div>';
                if (!empty($b['imageUrl'])) {
                    echo '<img src="' . esc_url($b['imageUrl']) . '" alt="" style="' . $this->get_image_style($b) . ' z-index:2;">';
                }
            }
            return ob_get_clean();
        }

        private function build_container_styles(array $b): array {
            $styles = [
                'width' => esc_attr(($b['width'] ?? 50) . ($b['widthUnit'] ?? '%')),
                'height' => 'auto',
                'min-height' => esc_attr(($b['minHeight'] ?? 185) . ($b['minHeightUnit'] ?? 'px')),
                'border-radius' => esc_attr($b['borderRadius'] ?? 16) . 'px',
                'position' => 'relative',
                'overflow' => 'hidden',
                'flex-shrink' => '0',
            ];
            if (!empty($b['enableBorder'])) {
                $styles['border'] = esc_attr($b['borderWidth'] ?? 1) . 'px solid ' . esc_attr($b['borderColor'] ?? '#E0E0E0');
            }
            return $styles;
        }

        private function build_content_styles(array $b): array {
            return [
                'width' => '100%',
                'padding' => sprintf('%spx %spx %spx %spx', esc_attr($b['paddingTop'] ?? 35), esc_attr($b['paddingRight'] ?? 31), esc_attr($b['paddingBottom'] ?? 35), esc_attr($b['paddingLeft'] ?? 31)),
                'display' => 'flex',
                'flex-direction' => 'column',
                'z-index' => '10',
                'position' => 'relative',
                'flex-grow' => '1',
            ];
        }

        private function build_desc_styles(array $b): array {
            return [
                'font-weight' => esc_attr($b['descWeight'] ?? '500'),
                'color' => esc_attr($b['descColor'] ?? '#ffffff'),
                'font-size' => esc_attr($b['descSize'] ?? 12) . 'px',
                'margin-top' => esc_attr($b['marginTopDescription'] ?? 12) . 'px',
                'margin-bottom' => '25px',
                'line-height' => esc_attr($b['descLineHeight'] ?? 1.1),
                'width' => esc_attr($b['descWidth'] ?? 100) . esc_attr($b['descWidthUnit'] ?? '%'),
                'word-wrap' => 'break-word',
            ];
        }

        private function build_button_styles(array $b, array $alignment): array {
            return [
                'text-decoration' => 'none',
                'display' => 'inline-flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'transition' => 'background-color 0.3s',
                'padding' => sprintf('%spx %spx', esc_attr($b['buttonPaddingY'] ?? 9), esc_attr($b['buttonPaddingX'] ?? 23)),
                'background-color' => esc_attr($b['buttonBgColor'] ?? '#124C88'),
                'color' => esc_attr($b['buttonTextColor'] ?? '#ffffff'),
                'font-size' => esc_attr($b['buttonFontSize'] ?? 14) . 'px',
                'width' => !empty($b['buttonWidth']) ? esc_attr($b['buttonWidth'] . ($b['buttonWidthUnit'] ?? 'px')) : 'auto',
                'height' => !empty($b['buttonHeight']) ? esc_attr($b['buttonHeight'] . ($b['buttonHeightUnit'] ?? 'px')) : 'auto',
                'min-width' => esc_attr($b['buttonMinWidth'] ?? 143) . 'px',
                'border-radius' => esc_attr($b['buttonBorderRadius'] ?? 5) . 'px',
                'margin-top' => 'auto',
                'font-weight' => esc_attr($b['buttonFontWeight'] ?? '600'),
                'align-self' => $alignment['align_self'],
                'line-height' => '1.15',
            ];
        }

        private function get_inline_style_attr(array $styles): string {
            $style_str = '';
            foreach ($styles as $prop => $value) {
                $style_str .= $prop . ': ' . $value . '; ';
            }
            return trim($style_str);
        }
    }
}
