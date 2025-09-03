<?php
// tappersia/public/Renderers/class-abstract-banner-renderer.php

if (!class_exists('Yab_Abstract_Banner_Renderer')) {
    abstract class Yab_Abstract_Banner_Renderer {
        
        /**
         * The main banner data array.
         * @var array
         */
        protected $data;

        /**
         * The banner's post ID.
         * @var int
         */
        protected $banner_id;

        /**
         * Constructor.
         * @param array $data The banner data from post meta.
         * @param int $banner_id The banner's post ID.
         */
        public function __construct(array $data, int $banner_id) {
            $this->data = $data;
            $this->banner_id = $banner_id;
        }

        /**
         * Main render method to be implemented by child classes.
         * @return string The generated HTML for the banner.
         */
        abstract public function render(): string;

        /**
         * Sanitizes and generates inline styles for the background.
         * @param array $b The specific banner part data (e.g., 'single', 'left').
         * @return string Sanitized background CSS.
         */
        protected function get_background_style(array $b): string {
            if (($b['backgroundType'] ?? 'solid') === 'gradient') {
                $angle = isset($b['gradientAngle']) ? intval($b['gradientAngle']) . 'deg' : '90deg';
                $color1 = esc_attr($b['gradientColor1'] ?? '#ffffff');
                $color2 = esc_attr($b['gradientColor2'] ?? '#ffffff');
                return "background: linear-gradient({$angle}, {$color1}, {$color2});";
            }
            return "background-color: " . esc_attr($b['bgColor'] ?? '#ffffff') . ";";
        }

        /**
         * Sanitizes and generates inline styles for the banner image.
         * @param array $b The specific banner part data.
         * @return string Sanitized image CSS.
         */
        protected function get_image_style(array $b): string {
            $right = isset($b['imagePosRight']) && $b['imagePosRight'] !== null ? intval($b['imagePosRight']) . 'px' : '0px';
            $bottom = isset($b['imagePosBottom']) && $b['imagePosBottom'] !== null ? intval($b['imagePosBottom']) . 'px' : '0px';
            $style = "position: absolute; right: {$right}; bottom: {$bottom};";

            if (!empty($b['enableCustomImageSize'])) {
                $width = isset($b['imageWidth']) && $b['imageWidth'] !== null ? intval($b['imageWidth']) . 'px' : 'auto';
                $height = isset($b['imageHeight']) && $b['imageHeight'] !== null ? intval($b['imageHeight']) . 'px' : 'auto';
                $style .= "width: {$width}; height: {$height};";
            } else {
                $style .= 'object-fit: ' . esc_attr($b['imageFit'] ?? 'none') . ';';
            }
            return $style;
        }

        /**
         * Generates alignment styles for content.
         * @param array $b The specific banner part data.
         * @return string Sanitized alignment CSS.
         */
        protected function get_alignment_style(array $b): string {
            $alignment = $b['alignment'] ?? 'left';
            $align_items = 'flex-start'; // Default to left
            $text_align = 'left';
            $align_self = 'flex-start';

            if ($alignment === 'center') {
                $align_items = 'center';
                $text_align = 'center';
                $align_self = 'center';
            } elseif ($alignment === 'right') {
                $align_items = 'flex-end';
                $text_align = 'right';
                $align_self = 'flex-end';
            }
            
            return "align-items: {$align_items}; text-align: {$text_align}; --align-self: {$align_self};";
        }
    }
}