<?php
if (!class_exists('Yab_Shortcode_Handler')) :
    class Yab_Shortcode_Handler {

        public function register_shortcodes() {
            add_shortcode('doublebanner_fixed', [$this, 'render_fixed_banner']);
            add_shortcode('doublebanner', [$this, 'render_embeddable_banner']);
        }

        public function render_fixed_banner($atts) {
            global $post;
            if (!$post && !is_category() && !is_archive()) {
                return '';
            }

            $args = [
                'post_type' => 'yab_banner',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_query' => [
                    'relation' => 'AND',
                    ['key' => '_yab_display_method', 'value' => 'Fixed', 'compare' => '='],
                    ['key' => '_yab_is_active', 'value' => true, 'compare' => '=']
                ]
            ];

            $banners = get_posts($args);
            $queried_object_id = get_queried_object_id();
            
            foreach ($banners as $banner_post) {
                $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
                if (empty($data) || empty($data['displayOn'])) {
                    continue;
                }

                $display_conditions = $data['displayOn'];
                $should_display = false;

                $post_ids = !empty($display_conditions['posts']) ? array_map('intval', $display_conditions['posts']) : [];
                $page_ids = !empty($display_conditions['pages']) ? array_map('intval', $display_conditions['pages']) : [];
                $cat_ids  = !empty($display_conditions['categories']) ? array_map('intval', $display_conditions['categories']) : [];

                if (is_singular('post') && in_array($queried_object_id, $post_ids)) $should_display = true;
                if (!$should_display && is_page() && in_array($queried_object_id, $page_ids)) $should_display = true;
                
                if (!$should_display && !empty($cat_ids)) {
                    if (is_category($cat_ids)) {
                        $should_display = true;
                    } elseif (is_singular('post') && has_category($cat_ids, $post)) {
                        $should_display = true;
                    }
                }

                if ($should_display) {
                    return $this->generate_banner_html($data); // Return and stop after finding the first matching banner
                }
            }
            return ''; // No banner found for this page
        }

        public function render_embeddable_banner($atts) {
            $atts = shortcode_atts(['id' => 0], $atts, 'doublebanner');
            if (empty($atts['id'])) return '';

            $banner_post = get_post(intval($atts['id']));
            if (!$banner_post || $banner_post->post_type !== 'yab_banner' || $banner_post->post_status !== 'publish') return '';

            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
            if (empty($data) || !isset($data['isActive']) || !$data['isActive']) return '';
            
            return $this->generate_banner_html($data);
        }
        
        private function get_alignment_style($align) {
            switch ($align) {
                case 'right': return 'align-items: flex-end; text-align: right;';
                case 'center': return 'align-items: center; text-align: center;';
                default: return 'align-items: flex-start; text-align: left;';
            }
        }
        
        private function generate_banner_html($data) {
            ob_start();
            $banners = ['left' => $data['left'], 'right' => $data['right']];

            $get_bg_style = function($banner_part) {
                if ($banner_part['backgroundType'] === 'gradient') {
                    $angle = isset($banner_part['gradientAngle']) ? intval($banner_part['gradientAngle']) . 'deg' : '90deg';
                    return "background: linear-gradient({$angle}, {$banner_part['gradientColor1']}, {$banner_part['gradientColor2']});";
                }
                return "background-color: {$banner_part['bgColor']};";
            };

            ?>
            <div class="yab-wrapper" style="display: flex; flex-direction: column; gap: 1rem; width: 100%;">
                <?php foreach ($banners as $key => $b): ?>
                <div class="yab-banner-item" style="width: 100%; height: <?php echo esc_attr($b['imageSize']); ?>px; border-radius: 0.5rem; position: relative; overflow: hidden; display: flex; <?php echo esc_attr($get_bg_style($b)); ?>">
                    
                    <?php if (!empty($b['imageUrl'])): ?>
                    <div style="position: absolute; inset: 0; z-index: 0;">
                        <img src="<?php echo esc_url($b['imageUrl']); ?>" style="width: 100%; height: 100%; object-fit: <?php echo esc_attr($b['imageFit']); ?>;">
                    </div>
                    <?php endif; ?>

                    <div style="width: 100%; padding: 1rem; display: flex; flex-direction: column; z-index: 1; <?php echo $this->get_alignment_style($b['alignment']); ?>">
                        <h4 style="font-weight: <?php echo esc_attr($b['titleWeight']); ?>; color: <?php echo esc_attr($b['titleColor']); ?>; font-size: <?php echo esc_attr($b['titleSize']); ?>px; margin: 0;"><?php echo esc_html($b['titleText']); ?></h4>
                        <p style="margin-top: 0.5rem; font-weight: <?php echo esc_attr($b['descWeight']); ?>; color: <?php echo esc_attr($b['descColor']); ?>; font-size: <?php echo esc_attr($b['descSize']); ?>px;"><?php echo nl2br(esc_html($b['descText'])); ?></p>
                        <?php if(!empty($b['buttonText'])): ?>
                        <a href="<?php echo esc_url($b['buttonLink']); ?>" target="_blank" style="margin-top: auto; padding: 0.5rem 1rem; border-radius: 0.25rem; text-decoration: none; background-color: <?php echo esc_attr($b['buttonBgColor']); ?>; color: <?php echo esc_attr($b['buttonTextColor']); ?>; font-size: <?php echo esc_attr($b['buttonFontSize']); ?>px;"><?php echo esc_html($b['buttonText']); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php
            return ob_get_clean();
        }
    }
endif;