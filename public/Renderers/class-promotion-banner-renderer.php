<?php
// tappersia/public/Renderers/class-promotion-banner-renderer.php

if (!class_exists('Yab_Promotion_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Promotion_Banner_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            if (empty($this->data['promotion'])) {
                return '';
            }
            
            $b = $this->data['promotion'];
            $banner_id = $this->banner_id;

            $body_text = esc_html($b['bodyText']);
            if (!empty($b['links']) && is_array($b['links'])) {
                foreach ($b['links'] as $link) {
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
            
            $overall_direction = $b['direction'] ?? 'ltr'; // Get overall direction
            $flex_direction_style = 'flex-direction: ' . ($overall_direction === 'rtl' ? 'row-reverse' : 'row') . ';';
            $text_align_style = 'text-align: ' . ($overall_direction === 'rtl' ? 'right' : 'left') . ';';

            ob_start();
            ?>
            <div class="yab-promo-banner-wrapper" 
                 style="border: <?php echo esc_attr($b['borderWidth']); ?>px solid <?php echo esc_attr($b['borderColor']); ?>;
                        border-radius: <?php echo esc_attr($b['borderRadius']); ?>px;
                        overflow: hidden;
                        width: 100%;
                        box-sizing: border-box;
                        direction: <?php echo esc_attr($overall_direction); ?>;"> <div class="yab-promo-header"
                     style="<?php echo $this->get_background_style($b, 'header'); ?>;
                            padding: <?php echo esc_attr($b['headerPaddingY']); ?>px <?php echo esc_attr($b['headerPaddingX']); ?>px;
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            box-sizing: border-box;
                            <?php echo $flex_direction_style; ?>"> <?php if (!empty($b['iconUrl'])): ?>
                        <img src="<?php echo esc_url($b['iconUrl']); ?>" alt="icon" style="width: <?php echo esc_attr($b['iconSize']); ?>px; height: <?php echo esc_attr($b['iconSize']); ?>px;">
                    <?php endif; ?>
                    <span style="color: <?php echo esc_attr($b['headerTextColor']); ?>;
                                 font-size: <?php echo esc_attr($b['headerFontSize']); ?>px;
                                 font-weight: <?php echo esc_attr($b['headerFontWeight']); ?>;
                                 <?php echo $text_align_style; ?>"> <?php echo esc_html($b['headerText']); ?>
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
                              <?php echo $text_align_style; ?>; margin: 0;">
                        <?php echo $body_text; // It's now safe to output directly ?>
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