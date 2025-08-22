<?php
// tappersia/public/Renderers/class-api-banner-renderer.php

if (!class_exists('Yab_Api_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Api_Banner_Renderer extends Yab_Abstract_Banner_Renderer {
        
        private function get_rating_label($score) {
            if (!isset($score) || $score == 0) {
                return 'New';
            }
            if ($score >= 4.6) {
                return 'Excellent';
            } elseif ($score >= 4.1) {
                return 'Very Good';
            } elseif ($score >= 3.6) {
                return 'Good';
            } elseif ($score >= 3.0) {
                return 'Average';
            } else {
                return 'Poor';
            }
        }

        public function render(): string {
            if (empty($this->data['api']['selectedHotel'])) {
                return '';
            }

            $hotel = $this->data['api']['selectedHotel'];
            $design = $this->data['api']['design'] ?? [];
            $banner_id = $this->banner_id;
            $rating_label = $this->get_rating_label($hotel['avgRating'] ?? null);

            // Helper for safely getting design values with defaults
            $get_design = function($key, $default) use ($design) {
                return esc_attr($design[$key] ?? $default);
            };

            // Helper for inline styles
            $style = function($styles) {
                $style_str = '';
                foreach ($styles as $prop => $value) {
                    if ($value !== null && $value !== '') {
                        $style_str .= esc_attr($prop) . ': ' . esc_attr($value) . ';';
                    }
                }
                return $style_str;
            };
            
            $is_right_layout = $get_design('layout', 'left') === 'right';

            ob_start();
            ?>
            <div class="yab-api-banner-wrapper" style="<?php echo $style([
                'font-family' => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif",
                'width' => '864px',
                'height' => '128px',
                'margin' => '20px auto',
                'border-radius' => $get_design('borderRadius', 15) . 'px',
                'border-width' => $get_design('borderWidth', 1) . 'px',
                'border-style' => 'solid',
                'border-color' => $get_design('borderColor', '#E0E0E0'),
                'background-color' => $get_design('bgColor', '#ffffff'),
                'overflow' => 'hidden',
                'box-shadow' => '0 4px 12px rgba(0,0,0,0.08)',
                'display' => 'flex',
                'align-items' => 'stretch',
                'flex-direction' => $is_right_layout ? 'row-reverse' : 'row'
            ]); ?>">
                <div style="<?php echo $style(['flex-shrink' => '0', 'width' => '360px', 'height' => '100%']); ?>">
                    <img src="<?php echo esc_url($hotel['coverImage']['url']); ?>" alt="<?php echo esc_attr($hotel['title']); ?>" style="<?php echo $style(['width' => '100%', 'height' => '100%', 'object-fit' => 'cover', 'display' => 'block']); ?>">
                </div>
                <div style="<?php echo $style([
                    'flex-grow' => '1',
                    'display' => 'flex',
                    'flex-direction' => 'column',
                    'position' => 'relative',
                    'padding-left' => $is_right_layout ? '30px' : '55px',
                    'padding-right' => $is_right_layout ? '55px' : '30px',
                    'text-align' => $is_right_layout ? 'right' : 'left'
                ]); ?>">
                    <h3 style="<?php echo $style([
                        'margin' => '0',
                        'padding-top' => '18px',
                        'padding-bottom' => '12px',
                        'color' => $get_design('titleColor', '#000000'),
                        'font-size' => $get_design('titleSize', 18) . 'px',
                        'font-weight' => $get_design('titleWeight', '700')
                    ]); ?>"><?php echo esc_html($hotel['title']); ?></h3>
                    
                    <div style="<?php echo $style(['display' => 'flex', 'align-items' => 'center', 'justify-content' => $is_right_layout ? 'flex-end' : 'flex-start']); ?>">
                        <div style="<?php echo $style(['color' => '#ffc107', 'display' => 'flex']); ?>">
                            <?php for ($i = 0; $i < 5; $i++) : ?>
                                <span style="<?php echo $style(['font-size' => $get_design('starSize', 13).'px', 'width' => $get_design('starSize', 13).'px', 'height' => $get_design('starSize', 13).'px']); ?>"><?php echo ($i < ($hotel['star'] ?? 0)) ? '★' : '☆'; ?></span>
                            <?php endfor; ?>
                        </div>
                        <div style="<?php echo $style(['border-left' => '1px solid #cccccc', 'height' => '16px', 'margin' => '0 13px']); ?>"></div>
                        <span style="<?php echo $style(['color' => $get_design('cityColor', '#000000'), 'font-size' => $get_design('citySize', 10) . 'px']); ?>">
                            <?php echo esc_html($hotel['province']['name']); ?>
                        </span>
                    </div>

                    <div style="<?php echo $style(['margin-top' => 'auto', 'display' => 'flex', 'align-items' => 'center', 'justify-content' => 'space-between', 'padding-bottom' => '20px']); ?>">
                         <div style="<?php echo $style(['display' => 'flex', 'align-items' => 'center', 'flex-direction' => $is_right_layout ? 'row-reverse' : 'row']); ?>">
                            <?php if(isset($hotel['avgRating'])): ?>
                            <div style="<?php echo $style(['display' => 'flex', 'align-items' => 'center', 'justify-content' => 'center', 'border-radius' => '4px', 'min-width' => '25px', 'padding' => '0 6px', 'height' => '15px', 'background-color' => $get_design('ratingBoxBgColor', '#5191FA')]); ?>">
                                <span style="<?php echo $style(['font-weight' => 'bold', 'color' => $get_design('ratingBoxColor', '#FFFFFF'), 'font-size' => $get_design('ratingBoxSize', 10) . 'px']); ?>"><?php echo esc_html($hotel['avgRating']); ?></span>
                            </div>
                            <?php endif; ?>
                            <span style="<?php echo $style(['margin' => '0 7px', 'color' => $get_design('ratingTextColor', '#5191FA'), 'font-size' => $get_design('ratingTextSize', 10) . 'px']); ?>"><?php echo esc_html($rating_label); ?></span>
                            <?php if(isset($hotel['reviewCount'])): ?>
                            <span style="<?php echo $style(['color' => $get_design('reviewColor', '#999999'), 'font-size' => $get_design('reviewSize', 10) . 'px']); ?>">(<?php echo esc_html($hotel['reviewCount']); ?> reviews)</span>
                            <?php endif; ?>
                        </div>
                        <div style="<?php echo $style(['display' => 'flex', 'align-items' => 'baseline', 'gap' => '4px', 'flex-direction' => $is_right_layout ? 'row-reverse' : 'row']); ?>">
                            <span style="<?php echo $style(['color' => $get_design('priceFromColor', '#999999'), 'font-size' => $get_design('priceFromSize', 10) . 'px']); ?>">from</span>
                            <span style="<?php echo $style(['color' => $get_design('priceAmountColor', '#00BAA4'), 'font-size' => $get_design('priceAmountSize', 16) . 'px', 'font-weight' => $get_design('priceAmountWeight', '700')]); ?>">€<?php echo esc_html(number_format($hotel['minPrice'], 2)); ?></span>
                            <span style="<?php echo $style(['color' => $get_design('priceNightColor', '#999999'), 'font-size' => $get_design('priceNightSize', 10) . 'px']); ?>">/ night</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
    }
}