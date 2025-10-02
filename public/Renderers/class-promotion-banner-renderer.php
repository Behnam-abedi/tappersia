<?php
// tappersia/public/Renderers/class-promotion-banner-renderer.php

if (!class_exists('Yab_Promotion_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Promotion_Banner_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            if (empty($this->data['promotion'])) {
                return '';
            }
            
            $desktop_b = $this->data['promotion'];
            $mobile_b = $this->data['promotion_mobile'] ?? $desktop_b;
            $banner_id = $this->banner_id;

            ob_start();
            ?>
            <style>
                .yab-promo-banner-wrapper-<?php echo $banner_id; ?> .yab-promo-mobile { display: none; }
                .yab-promo-banner-wrapper-<?php echo $banner_id; ?> .yab-promo-desktop { display: block; }
                
                @media (max-width: 768px) {
                    .yab-promo-banner-wrapper-<?php echo $banner_id; ?> .yab-promo-desktop { display: none; }
                    .yab-promo-banner-wrapper-<?php echo $banner_id; ?> .yab-promo-mobile { display: block; }
                }
            </style>

            <div class="yab-wrapper yab-promo-banner-wrapper-<?php echo $banner_id; ?>" style="width: 100%; direction: ltr;">
                <div class="yab-promo-desktop">
                    <?php echo $this->render_view($desktop_b, $desktop_b); ?>
                </div>
                 <div class="yab-promo-mobile">
                    <?php echo $this->render_view($mobile_b, $desktop_b); ?>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

        private function render_view($b, $desktop_b) {
            $body_text = esc_html($desktop_b['bodyText']);
            if (!empty($desktop_b['links']) && is_array($desktop_b['links'])) {
                foreach ($desktop_b['links'] as $link) {
                    if (!empty($link['placeholder']) && !empty($link['url'])) {
                        $placeholder = '[[' . $link['placeholder'] . ']]';
                        $link_html = sprintf(
                            '<a href="%s" target="_blank" style="color: %s; text-decoration: underline; padding: 0 5px;">%s</a>',
                            esc_url($link['url']),
                            esc_attr($link['color']),
                            esc_html($link['placeholder'])
                        );
                        $body_text = str_replace(esc_html($placeholder), $link_html, $body_text);
                    }
                }
            }
            
            $overall_direction = $desktop_b['direction'] ?? 'ltr';
            $flex_direction_style = 'flex-direction: ' . ($overall_direction === 'rtl' ? 'row-reverse' : 'row') . ';';
            $text_align_style = 'text-align: ' . ($overall_direction === 'rtl' ? 'right' : 'left') . ';';

            ob_start();
            ?>
            <div class="yab-promo-banner-inner-wrapper" 
                 style="border-radius: <?php echo esc_attr($b['borderRadius']); ?>px;
                        overflow: hidden;
                        width: 100%;
                        position: relative;
                        direction: <?php echo esc_attr($overall_direction); ?>;">
                
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; border-radius: inherit; box-shadow: inset 0 0 0 <?php echo esc_attr($b['borderWidth']); ?>px <?php echo esc_attr($b['borderColor']); ?>; z-index: 10; pointer-events: none;"></div>

                <div class="yab-promo-header"
                     style="<?php echo $this->get_background_style($b, 'header'); ?>;
                            padding: <?php echo esc_attr($b['headerPaddingY']); ?>px <?php echo esc_attr($b['headerPaddingX']); ?>px;
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            box-sizing: border-box;
                            <?php echo $flex_direction_style; ?>">
                    <?php if (!empty($desktop_b['iconUrl'])): ?>
                        <img src="<?php echo esc_url($desktop_b['iconUrl']); ?>" alt="icon" style="width: <?php echo esc_attr($b['iconSize']); ?>px; height: <?php echo esc_attr($b['iconSize']); ?>px;">
                    <?php endif; ?>
                    <span style="color: <?php echo esc_attr($b['headerTextColor']); ?>;
                                 font-size: <?php echo esc_attr($b['headerFontSize']); ?>px;
                                 font-weight: <?php echo esc_attr($b['headerFontWeight']); ?>;
                                 <?php echo $text_align_style; ?> flex-grow: 1;">
                        <?php echo esc_html($desktop_b['headerText']); ?>
                    </span>
                </div>

                <div class="yab-promo-body"
                     style="<?php echo $this->get_background_style($b, 'body'); ?>;
                            padding: <?php echo esc_attr($b['bodyPaddingY']); ?>px <?php echo esc_attr($b['bodyPaddingX']); ?>px;
                            box-sizing: border-box;">
                    <p style="color: <?php echo esc_attr($b['bodyTextColor']); ?>;
                              font-size: <?php echo esc_attr($b['bodyFontSize']); ?>px;
                              font-weight: <?php echo esc_attr($b['bodyFontWeight']); ?>;
                              line-height: <?php echo esc_attr($b['bodyLineHeight']); ?>;
                              <?php echo $text_align_style; ?> margin: 0;">
                        <?php echo $body_text; ?>
                    </p>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }


        protected function get_background_style(array $b, string $section = 'header'): string {
            $prefix = $section;
            $type_key = $prefix . 'BackgroundType';
            $color_key = $prefix . 'BgColor';
            $grad1_key = $prefix . 'GradientColor1';
            $grad2_key = $prefix . 'GradientColor2';
            $angle_key = $prefix . 'GradientAngle';

            if (($b[$type_key] ?? 'solid') === 'gradient') {
                $angle = isset($b[$angle_key]) ? intval($b[$angle_key]) . 'deg' : '90deg';
                $color1 = esc_attr($b[$grad1_key] ?? '#ffffff');
                $color2 = esc_attr($b[$grad2_key] ?? '#ffffff');
                return "background: linear-gradient({$angle}, {$color1}, {$color2});";
            }
            return "background-color: " . esc_attr($b[$color_key] ?? '#ffffff') . ";";
        }
    }
}