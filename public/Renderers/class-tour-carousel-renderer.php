<?php
// tappersia/public/Renderers/class-tour-carousel-renderer.php

if (!class_exists('Yab_Tour_Carousel_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Tour_Carousel_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            if (empty($this->data['tour_carousel']) || empty($this->data['tour_carousel']['selectedTours'])) {
                return '';
            }

            $banner_id = $this->banner_id;
            // In the next steps, we will implement the actual HTML structure for the Swiper slider here.
            // For now, it will just show a placeholder.
            ob_start();
            ?>
            <div id="yab-tour-carousel-<?php echo esc_attr($banner_id); ?>" class="yab-tour-carousel-wrapper">
                <h2>Tour Carousel #<?php echo esc_attr($banner_id); ?></h2>
                <p>Selected Tours:</p>
                <ul>
                    <?php foreach ($this->data['tour_carousel']['selectedTours'] as $tour_id) : ?>
                        <li>Tour ID: <?php echo esc_html($tour_id); ?></li>
                    <?php endforeach; ?>
                </ul>
                <p><em>(Frontend renderer will be implemented in the next steps)</em></p>
            </div>
            <?php
            return ob_get_clean();
        }
    }
}