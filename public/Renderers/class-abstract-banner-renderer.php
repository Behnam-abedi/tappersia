<?php
// tappersia/public/Renderers/class-abstract-banner-renderer.php

if (!class_exists('Yab_Abstract_Banner_Renderer')) {
    abstract class Yab_Abstract_Banner_Renderer {
        
        protected $data;
        protected $banner_id;

        public function __construct(array $data, int $banner_id) {
            $this->data = $data;
            $this->banner_id = $banner_id;
        }

        abstract public function render(): string;

        protected function get_background_style(array $b): string {
            if (($b['backgroundType'] ?? 'solid') === 'gradient') {
                if (empty($b['gradientStops']) || !is_array($b['gradientStops'])) {
                    return "background: transparent;";
                }

                usort($b['gradientStops'], function($a, $b) {
                    return ($a['stop'] ?? 0) <=> ($b['stop'] ?? 0);
                });

                $stops_css = [];
                foreach ($b['gradientStops'] as $stop) {
                    $color = isset($stop['color']) ? trim($stop['color']) : 'transparent';
                    $sanitized_color = (strtolower($color) === 'transparent') ? 'transparent' : esc_attr($color);
                    
                    $position = isset($stop['stop']) ? intval($stop['stop']) : 0;
                    $stops_css[] = $sanitized_color . ' ' . esc_attr($position) . '%';
                }

                if (empty($stops_css)) return "background: transparent;";

                $angle = isset($b['gradientAngle']) ? intval($b['gradientAngle']) . 'deg' : '90deg';
                return "background: linear-gradient({$angle}, " . implode(', ', $stops_css) . ");";
            }
            return "background-color: " . esc_attr($b['bgColor'] ?? '#ffffff') . ";";
        }

        protected function get_image_style(array $b): string {
            $right = isset($b['imagePosRight']) && $b['imagePosRight'] !== null ? intval($b['imagePosRight']) . 'px' : '0';
            $bottom = isset($b['imagePosBottom']) && $b['imagePosBottom'] !== null ? intval($b['imagePosBottom']) . 'px' : '0';
            $style = "position: absolute; object-fit: cover; right: {$right}; bottom: {$bottom};";

            if (!empty($b['enableCustomImageSize'])) {
                $width_unit = isset($b['imageWidthUnit']) && in_array($b['imageWidthUnit'], ['px', '%']) ? $b['imageWidthUnit'] : 'px';
                $height_unit = isset($b['imageHeightUnit']) && in_array($b['imageHeightUnit'], ['px', '%']) ? $b['imageHeightUnit'] : 'px';

                $width = isset($b['imageWidth']) && $b['imageWidth'] !== null && $b['imageWidth'] !== '' ? intval($b['imageWidth']) . $width_unit : 'auto';
                $height = isset($b['imageHeight']) && $b['imageHeight'] !== null && $b['imageHeight'] !== '' ? intval($b['imageHeight']) . $height_unit : '100%';
                
                $style .= "width: {$width}; height: {$height};";
            } else {
                $style .= 'width: auto; height: 100%;';
            }
            return $style;
        }

        protected function get_alignment_style(array $b): array {
            $alignment = $b['alignment'] ?? 'left';
            $align_items = 'flex-start';
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
            
            return [
                'align_items' => $align_items,
                'text_align' => $text_align,
                'align_self' => $align_self
            ];
        }
    }
}