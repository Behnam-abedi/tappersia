<?php
if (!class_exists('Yab_Shortcode_Handler')) :
    class Yab_Shortcode_Handler {

        public function register_shortcodes() {
            add_shortcode('doublebanner_fixed', array($this, 'render_fixed_banner'));
            add_shortcode('doublebanner', array($this, 'render_embeddable_banner'));
        }

        public function render_fixed_banner($atts) {
            global $post;
            if (!$post) {
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
            $output = '';
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

                if (is_singular('post') && in_array($queried_object_id, $post_ids)) {
                    $should_display = true;
                }

                if (!$should_display && is_page() && in_array($queried_object_id, $page_ids)) {
                    $should_display = true;
                }

                if (!$should_display && !empty($cat_ids)) {
                    if (is_category($cat_ids)) {
                        $should_display = true;
                    } elseif (is_singular('post') && has_category($cat_ids, $post)) {
                        $should_display = true;
                    }
                }

                if ($should_display) {
                    $output .= $this->generate_banner_html($data);
                }
            }
            return $output;
        }

        public function render_embeddable_banner($atts) {
            $atts = shortcode_atts(array('id' => 0), $atts, 'doublebanner');
            if ( empty($atts['id']) ) return '';

            $banner_post = get_post(intval($atts['id']));
            if (!$banner_post || $banner_post->post_type !== 'yab_banner' || $banner_post->post_status !== 'publish') return '';

            $data = get_post_meta($banner_post->ID, '_yab_banner_data', true);
            if (empty($data) || !isset($data['isActive']) || !$data['isActive']) return '';
            
            return $this->generate_banner_html($data);
        }
        
        private function generate_banner_html($data) {
            ob_start();
            $left = $data['left']; $right = $data['right'];
            $get_bg_style = function($banner_part) {
                return $banner_part['backgroundType'] === 'gradient' ? "background: linear-gradient(90deg, {$banner_part['gradientColor1']}, {$banner_part['gradientColor2']});" : "background-color: {$banner_part['bgColor']};";
            };

            $get_img_style = function($banner_part) {
                $style = 'position: absolute; width: 100%; height: 100%; object-fit: ' . esc_attr($banner_part['imageFit']) . ';';
                if (isset($banner_part['enableCustomPosition']) && $banner_part['enableCustomPosition']) {
                    $style .= ' object-position: ' . esc_attr($banner_part['imagePosX']) . 'px ' . esc_attr($banner_part['imagePosY']) . 'px;';
                }
                return $style;
            };

            ?>
            <div class="yab-wrapper" style="display: flex; flex-direction: column; gap: 1rem; width: 100%;">
                <div style="width: 100%; border-radius: 0.5rem; position: relative; overflow: hidden; display: flex; <?php echo esc_attr($get_bg_style($left)); ?> min-height: <?php echo esc_attr($left['imageSize']); ?>px;">
                    <?php if ($left['alignment'] === 'right'): ?>
                        <div style="width: 50%; position: relative;">
                            <?php if(!empty($left['imageUrl'])): ?>
                                <img src="<?php echo esc_url($left['imageUrl']); ?>" style="<?php echo $get_img_style($left); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div style="width: 50%; padding: 1rem; display: flex; flex-direction: column; align-items: <?php echo $left['alignment'] === 'left' ? 'flex-start' : 'flex-end'; ?>; text-align: <?php echo esc_attr($left['alignment']); ?>; z-index: 1;">
                        <h4 style="font-weight: <?php echo esc_attr($left['titleWeight']); ?>; color: <?php echo esc_attr($left['titleColor']); ?>; font-size: <?php echo esc_attr($left['titleSize']); ?>px; margin: 0;"><?php echo esc_html($left['titleText']); ?></h4>
                        <p style="margin-top: 0.5rem; font-weight: <?php echo esc_attr($left['descWeight']); ?>; color: <?php echo esc_attr($left['descColor']); ?>; font-size: <?php echo esc_attr($left['descSize']); ?>px;"><?php echo wp_kses_post($left['descText']); ?></p>
                        <a href="<?php echo esc_url($left['buttonLink']); ?>" target="_blank" style="margin-top: auto; padding: 0.5rem 1rem; border-radius: 0.25rem; text-decoration: none; background-color: <?php echo esc_attr($left['buttonBgColor']); ?>; color: <?php echo esc_attr($left['buttonTextColor']); ?>; font-size: <?php echo esc_attr($left['buttonFontSize']); ?>px;"><?php echo esc_html($left['buttonText']); ?></a>
                    </div>
                    <?php if ($left['alignment'] === 'left'): ?>
                        <div style="width: 50%; position: relative;">
                            <?php if(!empty($left['imageUrl'])): ?>
                                <img src="<?php echo esc_url($left['imageUrl']); ?>" style="<?php echo $get_img_style($left); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div style="width: 100%; border-radius: 0.5rem; position: relative; overflow: hidden; display: flex; <?php echo esc_attr($get_bg_style($right)); ?> min-height: <?php echo esc_attr($right['imageSize']); ?>px;">
                    <?php if ($right['alignment'] === 'right'): ?>
                        <div style="width: 50%; position: relative;">
                            <?php if(!empty($right['imageUrl'])): ?>
                                <img src="<?php echo esc_url($right['imageUrl']); ?>" style="<?php echo $get_img_style($right); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div style="width: 50%; padding: 1rem; display: flex; flex-direction: column; align-items: <?php echo $right['alignment'] === 'left' ? 'flex-start' : 'flex-end'; ?>; text-align: <?php echo esc_attr($right['alignment']); ?>; z-index: 1;">
                        <h4 style="font-weight: <?php echo esc_attr($right['titleWeight']); ?>; color: <?php echo esc_attr($right['titleColor']); ?>; font-size: <?php echo esc_attr($right['titleSize']); ?>px; margin: 0;"><?php echo esc_html($right['titleText']); ?></h4>
                        <p style="margin-top: 0.5rem; font-weight: <?php echo esc_attr($right['descWeight']); ?>; color: <?php echo esc_attr($right['descColor']); ?>; font-size: <?php echo esc_attr($right['descSize']); ?>px;"><?php echo wp_kses_post($right['descText']); ?></p>
                        <a href="<?php echo esc_url($right['buttonLink']); ?>" target="_blank" style="margin-top: auto; padding: 0.5rem 1rem; border-radius: 0.25rem; text-decoration: none; background-color: <?php echo esc_attr($right['buttonBgColor']); ?>; color: <?php echo esc_attr($right['buttonTextColor']); ?>; font-size: <?php echo esc_attr($right['buttonFontSize']); ?>px;"><?php echo esc_html($right['buttonText']); ?></a>
                    </div>
                    <?php if ($right['alignment'] === 'left'): ?>
                        <div style="width: 50%; position: relative;">
                            <?php if(!empty($right['imageUrl'])): ?>
                                <img src="<?php echo esc_url($right['imageUrl']); ?>" style="<?php echo $get_img_style($right); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
    }
endif;