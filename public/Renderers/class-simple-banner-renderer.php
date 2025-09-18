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
            $banner_id = $this->banner_id;
            
            ob_start();
            ?>
            <style>
                .yab-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile { display: none; }
                .yab-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop { display: flex; }
                
                @media (max-width: 768px) {
                    .yab-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-desktop { display: none; }
                    .yab-simple-banner-wrapper-<?php echo $banner_id; ?> .yab-banner-mobile { display: flex; flex-direction: column; height: auto !important; min-height: fit-content; gap: 15px; }
                }
            </style>
            
            <div class="yab-wrapper yab-simple-banner-wrapper-<?php echo $banner_id; ?>" style="width: 100%; direction: ltr;">
                <?php echo $this->render_view($desktop_b, 'desktop', $banner_id); ?>
                <?php echo $this->render_view($mobile_b, 'mobile', $banner_id); ?>
            </div>
            <?php
            return ob_get_clean();
        }

        private function render_view($b, $view, $banner_id) {
             ob_start();
            ?>
            <div class="yab-banner-item yab-banner-<?php echo $view; ?>" 
                 style="width: 100%; 
                        height: <?php echo esc_attr($b['height']); ?>px; 
                        min-height: <?php echo esc_attr($b['height']); ?>px;
                        border-radius: <?php echo esc_attr($b['borderRadius']); ?>px; 
                        <?php echo $this->get_background_style($b); ?>;
                        padding: <?php echo esc_attr($b['paddingY']); ?>px <?php echo esc_attr($b['paddingX']); ?>px;
                        align-items: center;
                        justify-content: space-between;
                        box-sizing: border-box;
                        direction: <?php echo $b['direction'] === 'rtl' ? 'rtl' : 'ltr'; ?>;
                        flex-direction: <?php echo $b['direction'] === 'rtl' ? 'row-reverse' : 'row'; ?>;
                        ">
                <span style="font-size: <?php echo esc_attr($b['textSize']); ?>px;
                             font-weight: <?php echo esc_attr($b['textWeight']); ?>;
                             color: <?php echo esc_attr($b['textColor']); ?>;
                             text-align: <?php echo $b['direction'] === 'rtl' ? 'right' : 'left'; ?>;">
                    <?php echo esc_html($b['text']); ?>
                </span>
                <a href="<?php echo esc_url($b['buttonLink']); ?>" 
                   target="_blank" 
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
                          flex-shrink: 0;">
                    <?php echo esc_html($b['buttonText']); ?>
                </a>
            </div>
            <?php
            return ob_get_clean();
        }
    }
}