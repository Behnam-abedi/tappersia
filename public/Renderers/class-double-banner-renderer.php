<?php
// tappersia/public/Renderers/class-double-banner-renderer.php

if (!class_exists('Yab_Double_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Double_Banner_Renderer extends Yab_Abstract_Banner_Renderer {
        
        public function render(): string {
            if (empty($this->data['left']) || empty($this->data['right'])) {
                return '';
            }

            $banners = ['left' => $this->data['left'], 'right' => $this->data['right']];
            $banner_id = $this->banner_id;

            ob_start();
            ?>
            <style>
                <?php foreach ($banners as $key => $b): ?>
                    <?php if(!empty($b['buttonText']) && !empty($b['buttonBgHoverColor'])): ?>
                    .yab-banner-<?php echo $banner_id; ?>-<?php echo $key; ?> .yab-button:hover {
                        background-color: <?php echo esc_attr($b['buttonBgHoverColor']); ?> !important;
                    }
                    <?php endif; ?>
                <?php endforeach; ?>
            </style>
            <div class="yab-wrapper" style="display: flex; flex-direction: row; gap: 1rem; width: 100%; justify-content: center;line-height:1.2!important">
                <?php foreach ($banners as $key => $b): ?>
                <div class="yab-banner-item yab-banner-<?php echo $banner_id; ?>-<?php echo $key; ?>" style="width: 432px; height: 177px; border-radius: 0.5rem; position: relative; overflow: hidden;display: flex; flex-shrink: 0; <?php echo $this->get_background_style($b); ?>">
                    
                    <?php if (!empty($b['imageUrl'])): ?>
                        <img src="<?php echo esc_url($b['imageUrl']); ?>" alt="" style="<?php echo $this->get_image_style($b); ?>">
                    <?php endif; ?>

                    <div style="width: 100%; height:100%; padding: 1.5rem; display: flex; flex-direction: column; z-index: 10; position: relative; <?php echo $this->get_alignment_style($b); ?>">
                        <h4 style="font-weight: <?php echo esc_attr($b['titleWeight'] ?? '700'); ?>; color: <?php echo esc_attr($b['titleColor'] ?? '#ffffff'); ?>; font-size: <?php echo intval($b['titleSize'] ?? 15); ?>px; margin: 0;"><?php echo esc_html($b['titleText'] ?? ''); ?></h4>
                        <p style="margin-top:0.5rem;margin-bottom:1.5rem;font-weight:<?php echo esc_attr($b['descWeight'] ?? '400'); ?>;color:<?php echo esc_attr($b['descColor'] ?? '#dddddd'); ?>;font-size:<?php echo intval($b['descSize'] ?? 10); ?>px;white-space:pre-wrap;"><?php echo wp_kses_post($b['descText'] ?? ''); ?></p>
                        <?php if(!empty($b['buttonText'])): ?>
                        <a href="<?php echo esc_url($b['buttonLink'] ?? '#'); ?>" target="_blank" class="yab-button" style="margin-top: auto; padding: 0.5rem 1rem; border-radius: 0.25rem; text-decoration: none; background-color: <?php echo esc_attr($b['buttonBgColor'] ?? '#00baa4'); ?>; color: <?php echo esc_attr($b['buttonTextColor'] ?? '#ffffff'); ?>; font-size: <?php echo intval($b['buttonFontSize'] ?? 10); ?>px; align-self: var(--align-self); transition: background-color 0.3s;"><?php echo esc_html($b['buttonText']); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php
            return ob_get_clean();
        }
    }
}