<?php
// tappersia/public/Renderers/class-api-banner-renderer.php

if (!class_exists('Yab_Api_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Api_Banner_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            if (empty($this->data['api'])) { return ''; }

            $banner_id = $this->banner_id;
            $desktop_design = $this->data['api']['design'] ?? [];
            $mobile_design = $this->data['api']['design_mobile'] ?? $desktop_design;

            ob_start();
            ?>
            <div id="yab-api-banner-placeholder-<?php echo $banner_id; ?>" class="yab-api-banner-placeholder" style="width: 100%; direction: ltr;">
                <div class="yab-skeleton-desktop">
                    <?php echo $this->render_skeleton_view($desktop_design, 'desktop'); ?>
                </div>
                <div class="yab-skeleton-mobile">
                    <?php echo $this->render_skeleton_view($mobile_design, 'mobile'); ?>
                </div>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            'action': 'yab_fetch_api_banner_html',
                            'banner_id': '<?php echo $banner_id; ?>'
                        })
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            const placeholder = document.getElementById('yab-api-banner-placeholder-<?php echo $banner_id; ?>');
                            if (placeholder) {
                                placeholder.innerHTML = result.data.html;
                            }
                        } else {
                             console.error('Failed to load API banner:', result.data.message);
                        }
                    })
                    .catch(error => console.error('Error fetching API banner:', error));
                });
            </script>
            
            <style>
                .yab-api-banner-placeholder .yab-skeleton-mobile { display: none; }
                .yab-api-banner-placeholder .yab-skeleton-desktop { display: block; }
                @media (max-width: 768px) {
                    .yab-api-banner-placeholder .yab-skeleton-desktop { display: none; }
                    .yab-api-banner-placeholder .yab-skeleton-mobile { display: block; }
                }
            </style>
            <?php
            return ob_get_clean();
        }
        
        public function render_live_html(): string {
             if (empty($this->data['api']['selectedHotel']) && empty($this->data['api']['selectedTour'])) {
                return '';
            }

            $banner_id = $this->banner_id;
            $desktop_design = $this->data['api']['design'] ?? [];
            $mobile_design = $this->data['api']['design_mobile'] ?? $desktop_design;

            ob_start();
            ?>
            <style>
                .yab-api-banner-wrapper-<?php echo $banner_id; ?> .yab-api-banner-mobile { display: none; }
                .yab-api-banner-wrapper-<?php echo $banner_id; ?> .yab-api-banner-desktop { display: flex; }
                @media (max-width: 768px) {
                    .yab-api-banner-wrapper-<?php echo $banner_id; ?> .yab-api-banner-desktop { display: none; }
                    .yab-api-banner-wrapper-<?php echo $banner_id; ?> .yab-api-banner-mobile { display: flex; }
                }
            </style>
            <div class="yab-api-banner-wrapper-<?php echo $banner_id; ?>" style="width: 100%; direction: ltr;">
                <?php echo $this->render_view($desktop_design, 'desktop'); ?>
                <?php echo $this->render_view($mobile_design, 'mobile'); ?>
            </div>
            <?php
            return ob_get_clean();
        }
        
        private function render_skeleton_view($design, $view) {
            $is_right_layout = ($design['layout'] ?? 'left') === 'right';
            $default_height = ($view === 'desktop') ? 150 : 80;
            $default_image_width = ($view === 'desktop') ? 360 : 140;
            $default_image_width_unit = 'px';

             $wrapper_styles = [
                'min-height' => esc_attr($design['minHeight'] ?? $default_height) . 'px', // *** 2. تغییر کرد ***
                'height' => 'auto',
                'width' => esc_attr($design['width'] ?? 100) . esc_attr($design['widthUnit'] ?? '%'), // *** 2. اضافه شد ***
                'border-radius' => esc_attr($design['borderRadius'] ?? 16) . 'px',
                'display' => 'flex',
                'align-items' => 'stretch',
                'overflow' => 'hidden',
                'background-color' => '#f4f4f4',
                'margin' => '20px 0',
                'flex-direction' => $is_right_layout ? 'row-reverse' : 'row',
            ];
            
            $image_width = esc_attr($design['imageContainerWidth'] ?? $default_image_width) . esc_attr($design['imageContainerWidthUnit'] ?? $default_image_width_unit); // *** 1. تغییر کرد ***

            ob_start();
            ?>
            <div>
                <?php // *** 2. استایل‌های width و min-height به div بیرونی منتقل شد *** ?>
                <div style="<?php echo $this->get_inline_style_attr($wrapper_styles); ?>">
                    <div style="width: <?php echo $image_width; ?>; height: auto; border-radius: 16px; background-color: #ebebeb; flex-shrink: 0;" class="yab-skeleton-loader"></div> <?php // *** 1. *** ?>
                    <div style="width: 100%">
                        <div style="flex-grow: 1; padding: 15px; display: flex; flex-direction: column; justify-content: space-between; gap: 8px;">
                            <div style="height: 26px; background-color: #ebebeb; border-radius: 4px; width: 75%;" class="yab-skeleton-loader"></div>
                            <div style="height: 16px; background-color: #ebebeb; border-radius: 4px; width: 50%;" class="yab-skeleton-loader"></div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-end; padding-top: 10px;">
                                <div style="height: 26px; background-color: #ebebeb; border-radius: 4px; width: 40%;" class="yab-skeleton-loader"></div>
                                <div style="height: 26px; background-color: #ebebeb; border-radius: 4px; width: 30%;" class="yab-skeleton-loader"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

        private function render_view($design, $view) {
            $is_hotel = !empty($this->data['api']['selectedHotel']);
            $item = $is_hotel ? $this->data['api']['selectedHotel'] : $this->data['api']['selectedTour'];
            $detail_url = $item['detailUrl'] ?? '#';

            $get_design = function($key, $default) use ($design) { return esc_attr($design[$key] ?? $default); };
            $style = function($styles) {
                $style_str = '';
                foreach ($styles as $prop => $value) { if ($value !== null && $value !== '') { $style_str .= esc_attr($prop) . ': ' . esc_attr($value) . ';'; } }
                return $style_str;
            };

            $is_right_layout = $get_design('layout', 'left') === 'right';
            $default_height = ($view === 'desktop') ? 150 : 80;
            $default_image_width = ($view === 'desktop') ? 360 : 140;
            $default_image_width_unit = 'px';


            $wrapper_styles = [
                'font-family' => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif", 
                'width' => $get_design('width', 100) . $get_design('widthUnit', '%'), // *** 2. تغییر کرد ***
                'min-height' => $get_design('minHeight', $default_height) . 'px', // *** 2. تغییر کرد ***
                'height' => 'auto', 
                'margin' => '20px 0',
                'border-radius' => $get_design('borderRadius', 16) . 'px', 'border' => $get_design('enableBorder', false) ? $get_design('borderWidth', 1) . 'px solid ' . $get_design('borderColor', '#ebebeb') : 'none',
                'overflow' => 'hidden', 'align-items' => 'stretch',
                'flex-direction' => $is_right_layout ? 'row-reverse' : 'row', 'position' => 'relative',
                'text-decoration' => 'none', 'color' => 'inherit'
            ];
            
            // *** 2. منطق enableCustomDimensions حذف شد ***

            $cover_image_url = $is_hotel ? ($item['coverImage']['url'] ?? '') : ($item['bannerImage']['url'] ?? '');
            $title = $item['title'] ?? ''; $star_rating = $is_hotel ? ($item['star'] ?? 0) : ceil($item['rate'] ?? 0);
            $location_name = $is_hotel ? ($item['province']['name'] ?? '') : ($item['startProvince']['name'] ?? '');
            $rating_score = $is_hotel ? ($item['avgRating'] ?? null) : ($item['rate'] ?? null);
            $review_count = $is_hotel ? ($item['reviewCount'] ?? null) : ($item['rateCount'] ?? null);
            $price = $is_hotel ? ($item['minPrice'] ?? 0) : ($item['price'] ?? 0); $price_suffix = $is_hotel ? '/ night' : '';

            ob_start();
            ?>
            <a href="<?php echo esc_url($detail_url); ?>" target="_blank" class="yab-api-banner-<?php echo $view; ?>" style="<?php echo $style($wrapper_styles); ?>">
                <div class="yab-api-banner-background" style="<?php echo $this->get_background_style($design); ?> position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1;"></div>
                <div style="<?php echo $style([
                    'flex-shrink' => '0',
                    'width' => $get_design('imageContainerWidth', $default_image_width) . $get_design('imageContainerWidthUnit', $default_image_width_unit), // *** 1. تغییر کرد ***
                    'z-index' => 2,
                    'background-image' => 'url(\'' . esc_url($cover_image_url) . '\')',
                    'background-size' => 'cover',
                    'background-position' => 'center center'
                ]); ?>">
                </div>
                <div style="<?php echo $style([
                    'flex-grow' => '1', 'display' => 'flex', 'flex-direction' => 'column', 'justify-content' => 'space-between',
                    'position' => 'relative', 'z-index' => 2, 'direction' => $is_right_layout ? 'rtl' : 'ltr',
                    'padding' => sprintf('%spx %spx', $get_design('paddingY', 12), $get_design('paddingX', 20)), // *** 3. تغییر کرد ***
                    'text-align' => $is_right_layout ? 'right' : 'left',
                ]); ?>">
                    <div>
                        <h3 style="<?php echo $style(['justifyContent' => 'space-between','margin' => '0', 'color' => $get_design('titleColor', '#000'), 'font-size' => $get_design('titleSize', 16).'px', 'font-weight' => $get_design('titleWeight', '700'), 'line-height' => '1.4']); ?>"><?php echo esc_html($title); ?></h3>
                        <div style="<?php echo $style(['display' => 'flex', 'align-items' => 'center', 'margin-top' => ($view === 'mobile' ? '4px' : '9px'), 'justify-content' => $is_right_layout ? 'flex-end' : 'flex-start']); ?>">
                            <div style="<?php echo $style(['color' => '#ffc107', 'display' => 'flex']); ?>">
                                <?php for ($i = 0; $i < 5; $i++) : ?>
                                    <span style="<?php echo $style(['font-size' => $get_design('starSize', 11).'px', 'width' => $get_design('starSize', 11).'px', 'height' => $get_design('starSize', 11).'px', 'line-height' => 1]); ?>"><?php echo ($i < $star_rating) ? '★' : '☆'; ?></span>
                                <?php endfor; ?>
                            </div>
                            <div style="<?php echo $style(['border-left' => '1px solid #cccccc', 'height' => '16px', 'margin' => '0 13px']); ?>"></div>
                            <span style="<?php echo $style(['color' => $get_design('cityColor', '#000'), 'font-size' => $get_design('citySize', 11).'px']); ?>"><?php echo esc_html($location_name); ?></span>
                        </div>
                    </div>
                    <div style="<?php echo $style(['margin-top' => '11px', 'display' => 'flex', 'align-items' => 'center', 'justify-content' => 'space-between']); ?>">
                        <?php if ($view === 'desktop'): ?>
                        <div style="<?php echo $style(['display' => 'flex', 'align-items' => 'center', 'flex-direction' => $is_right_layout ? 'row-reverse' : 'row']); ?>">
                            <?php if(isset($rating_score)): ?>
                                <div style="<?php echo $style(['display' => 'flex', 'align-items' => 'center', 'justify-content' => 'center', 'border-radius' => '4px', 'padding' => '0px 10px', 'line-height' => '1.6', 'background-color' => $get_design('ratingBoxBgColor', '#5191FA')]); ?>">
                                    <span style="<?php echo $style(['font-weight' => $get_design('ratingBoxWeight', '500'), 'color' => $get_design('ratingBoxColor', '#FFF'), 'font-size' => $get_design('ratingBoxSize', 10).'px']); ?>"><?php echo esc_html($this->format_rating($rating_score)); ?></span>
                                </div>
                            <?php endif; ?>
                            <span style="<?php echo $style(['margin' => '0 8px', 'color' => $get_design('ratingTextColor', '#5191FA'), 'font-size' => $get_design('ratingTextSize', 10).'px', 'font-weight' => $get_design('ratingTextWeight', '700')]); ?>"><?php echo esc_html($this->get_rating_label($rating_score)); ?></span>
                            <?php if(isset($review_count)): ?>
                                <span style="<?php echo $style(['color' => $get_design('reviewColor', '#999'), 'font-size' => $get_design('reviewSize', 8).'px']); ?>">(<?php echo esc_html($review_count); ?> reviews)</span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <div style="<?php echo $style(['display' => 'flex', 'align-items' => 'baseline', 'gap' => '4px', 'flex-direction' => $is_right_layout ? 'row-reverse' : 'row']); ?>">
                            <span style="<?php echo $style(['color' => $get_design('priceFromColor', '#999'), 'font-size' => $get_design('priceFromSize', 9).'px']); ?>">from</span>
                            <span style="<?php echo $style(['color' => $get_design('priceAmountColor', '#00BAA4'), 'font-size' => $get_design('priceAmountSize', 12).'px', 'font-weight' => $get_design('priceAmountWeight', '700')]); ?>">€<?php echo esc_html(number_format($price, 2)); ?></span>
                            <?php if ($is_hotel): ?>
                                <span style="<?php echo $style(['color' => $get_design('priceNightColor', '#999'), 'font-size' => $get_design('priceFromSize', 9).'px']); ?>"><?php echo esc_html($price_suffix); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </a>
            <?php
            return ob_get_clean();
        }

        private function get_rating_label($score) {
            if (!isset($score) || $score == 0) return 'New'; if ($score >= 4.6) return 'Excellent'; if ($score >= 4.1) return 'Very Good';
            if ($score >= 3.6) return 'Good'; if ($score >= 3.0) return 'Average'; return 'Poor';
        }
        
        private function format_rating($score) {
            if ($score === null) return ''; if (floor($score) == $score) return (int) $score; return floor($score * 10) / 10;
        }

        private function get_inline_style_attr(array $styles): string {
            $style_str = '';
            foreach ($styles as $prop => $value) { if ($value !== '' && $value !== null) { $style_str .= $prop . ': ' . $value . '; '; } }
            return trim($style_str);
        }
    }
}