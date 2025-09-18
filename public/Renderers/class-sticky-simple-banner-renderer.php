<?php
// tappersia/public/Renderers/class-sticky-simple-banner-renderer.php

if (!class_exists('Yab_Sticky_Simple_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Sticky_Simple_Banner_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            if (empty($this->data['sticky_simple'])) {
                return '';
            }
            
            $b = $this->data['sticky_simple'];
            $banner_id = $this->banner_id;
            
            ob_start();
            ?>
            <div class="yab-sticky-simple-banner-wrapper" 
                 style="width: 100%; 
                        height: <?php echo esc_attr($b['height']); ?>px; 
                        min-height: <?php echo esc_attr($b['height']); ?>px;
                        border-radius: 0; /* Ensures sharp corners for full-width */
                        <?php echo $this->get_background_style($b); ?>;
                        padding: <?php echo esc_attr($b['paddingY']); ?>px <?php echo esc_attr($b['paddingX']); ?>px;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        box-sizing: border-box;
                        direction: <?php echo $b['direction'] === 'rtl' ? 'rtl' : 'ltr'; ?>;
                        position: fixed;
                        bottom: 0;
                        left: 0;
                        right: 0; /* Ensures banner spans the full width */
                        z-index: 99999; /* High z-index to stay on top */
                        ">
                <span style="font-size: <?php echo esc_attr($b['textSize']); ?>px;
                             font-weight: <?php echo esc_attr($b['textWeight']); ?>;
                             color: <?php echo esc_attr($b['textColor']); ?>;">
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