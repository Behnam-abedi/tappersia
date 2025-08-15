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
                if (empty($data) || empty($data['displayOn'])) continue;

                $display_conditions = $data['displayOn'];
                $should_display = false;

                $post_ids = !empty($display_conditions['posts']) ? array_map('intval', $display_conditions['posts']) : [];
                $page_ids = !empty($display_conditions['pages']) ? array_map('intval', $display_conditions['pages']) : [];
                $cat_ids  = !empty($display_conditions['categories']) ? array_map('intval', $display_conditions['categories']) : [];

                if (is_singular('post') && in_array($queried_object_id, $post_ids)) $should_display = true;
                if (!$should_display && is_page() && in_array($queried_object_id, $page_ids)) $should_display = true;
                
                if (!$should_display && !empty($cat_ids)) {
                    if (is_category($cat_ids)) $should_display = true;
                    elseif (is_singular('post') && has_category($cat_ids, $post)) $should_display = true;
                }

                if ($should_display) {
                    return $this->generate_banner_html($data); 
                }
            }
            return '';
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
        
        private function get_alignment_style($b) {
             $align_items = 'flex-start';
             $text_align = 'left';
             $align_self = 'flex-start';

            if ($b['alignment'] === 'center') {
                $align_items = 'center';
                $text_align = 'center';
                $align_self = 'center';
            } elseif ($b['alignment'] === 'right') {
                $align_items = 'flex-end';
                $text_align = 'right';
                $align_self = 'flex-end';
            }
            return "align-items: {$align_items}; text-align: {$text_align}; --align-self: {$align_self};";
        }
        
        private function generate_banner_html($data) {
            ob_start();
            $banners = ['left' => $data['left'], 'right' => $data['right']];

            $get_bg_style = function($b) {
                if ($b['backgroundType'] === 'gradient') {
                    $angle = isset($b['gradientAngle']) ? intval($b['gradientAngle']) . 'deg' : '90deg';
                    return "background: linear-gradient({$angle}, {$b['gradientColor1']}, {$b['gradientColor2']});";
                }
                return "background-color: {$b['bgColor']};";
            };

            $get_img_style = function($b) {
                $style = 'width: 100%; height: 100%; object-fit: ' . esc_attr($b['imageFit']) . ';';
                if (!empty($b['enableCustomPosition'])) {
                    $style .= ' object-position: ' . esc_attr($b['imagePosX']) . 'px ' . esc_attr($b['imagePosY']) . 'px;';
                }
                return $style;
            };

            ?>
            <div class="yab-wrapper" style="display: flex; flex-direction: row-reverse; gap: 1rem; width: 100%; justify-content: center;">
                <?php foreach ($banners as $key => $b): ?>
                <div class="yab-banner-item" style="width: 432px; height: 177px; border-radius: 0.5rem; position: relative; overflow: hidden; display: flex; flex-shrink: 0; <?php echo esc_attr($get_bg_style($b)); ?>">
                    
                    <?php if (!empty($b['imageUrl'])): ?>
                    <div style="position: absolute; right: 0; top: 0; width: auto; height: 100%; z-index: 1;">
                        <img src="<?php echo esc_url($b['imageUrl']); ?>" style="<?php echo esc_attr($get_img_style($b)); ?>">
                    </div>
                    <?php endif; ?>

                    <div style="width: 100%; padding: 1rem 1.5rem; display: flex; flex-direction: column; z-index: 2; position: relative; <?php echo $this->get_alignment_style($b); ?>">
                        <h4 style="font-weight: <?php echo esc_attr($b['titleWeight']); ?>; color: <?php echo esc_attr($b['titleColor']); ?>; font-size: <?php echo esc_attr($b['titleSize']); ?>px; margin: 0;"><?php echo esc_html($b['titleText']); ?></h4>
                        <p style="direction:<?php echo ($b['alignment'] === 'right') ? 'ltr' : (($b['alignment'] === 'center') ? 'ltr' : 'rtl'); ?>;text-align:<?php echo ($b['alignment'] === 'right') ? 'left' : (($b['alignment'] === 'center') ? 'center' : 'right'); ?>;margin-top:0.5rem;font-weight:<?php echo esc_attr($b['descWeight']); ?>;color:<?php echo esc_attr($b['descColor']); ?>;font-size:<?php echo esc_attr($b['descSize']); ?>px;white-space:pre-wrap;"><?php echo esc_html($b['descText']); ?></p>
                        <?php if(!empty($b['buttonText'])): ?>
                        <a href="<?php echo esc_url($b['buttonLink']); ?>" target="_blank" style="margin-top: auto; padding: 0.5rem 1rem; border-radius: 0.25rem; text-decoration: none; background-color: <?php echo esc_attr($b['buttonBgColor']); ?>; color: <?php echo esc_attr($b['buttonTextColor']); ?>; font-size: <?php echo esc_attr($b['buttonFontSize']); ?>px; align-self: var(--align-self);"><?php echo esc_html($b['buttonText']); ?></a>
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