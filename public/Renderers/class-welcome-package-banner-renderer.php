<?php
// /public/Renderers/class-welcome-package-banner-renderer.php

if (!class_exists('Yab_Welcome_Package_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Welcome_Package_Banner_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            if (empty($this->data['welcome_package']) || empty($this->data['welcome_package']['selectedPackageKey']) || empty($this->data['welcome_package']['htmlContent'])) {
                return '<!-- Welcome Package Banner: Missing data -->';
            }

            $banner_id = $this->banner_id;
            $package_key = $this->data['welcome_package']['selectedPackageKey'];
            $html_template_raw = $this->data['welcome_package']['htmlContent'];
            $unique_id_wrapper = 'yab-wpb-cont-' . $banner_id . '-' . wp_rand(1000, 9999); // Unique ID for the container
            $ajax_url = admin_url('admin-ajax.php');

            // --- Extract content within <body> or use the whole content ---
            $body_content = '';
            if (preg_match('/<body.*?>(.*?)<\/body>/is', $html_template_raw, $matches)) {
                 // Remove style tags from body content if they exist there mistakenly
                $body_content = preg_replace('/<style.*?>(.*?)<\/style>/is', '', $matches[1]);
            } else {
                // If no body tag, assume the whole input is the intended content
                // Remove html, head tags from the raw input
                $body_content = preg_replace('/<html[^>]*>(.*?)<\/html>/is', '$1', $html_template_raw); // Remove html tag but keep content
                $body_content = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $body_content); // Remove head tag and its content
            }
             // Ensure it's not empty after stripping
             $body_content = trim($body_content);
            // --- End Content Extraction ---

            // --- Extract style content ---
            $style_content = '';
             if (preg_match('/<style.*?>(.*?)<\/style>/is', $html_template_raw, $style_matches)) {
                 $style_content = $style_matches[1];
             }
            // --- End Style Extraction ---


             // Skeleton HTML to show while loading
             $skeleton_html = '<div class="yab-wpb-skeleton" style="width: 100%; min-height: 100px; background-color: #f0f0f0; border-radius: 8px; display:flex; align-items:center; justify-content:center; color:#ccc;">Loading...</div>';

            ob_start();
            ?>
            <?php // Output the extracted styles, scoped manually (basic) ?>
            <style>
                #<?php echo esc_attr($unique_id_wrapper); ?> {
                    /* Add any necessary wrapper styles here */
                     box-sizing: border-box; /* Good practice */
                     /* Ensure banner takes full width by default */
                     width: 100%;
                     display: block; /* Make sure it behaves like a block element */
                }
                /* Apply user styles prefixed with the wrapper ID */
                <?php
                // Basic prefixing - works for simple selectors, might break complex ones
                $prefixed_css = preg_replace('/(^|\})([^{]+?)({)/', '$1#'.esc_attr($unique_id_wrapper).' $2$3', "\n".$style_content."\n");
                // Attempt to fix prefixing for direct descendant selectors like > child
                $prefixed_css = preg_replace('/(#'.esc_attr($unique_id_wrapper).')\s*>\s*/', '$1 > ', $prefixed_css);
                // Attempt to fix prefixing for adjacent sibling selectors like + sibling
                 $prefixed_css = preg_replace('/(#'.esc_attr($unique_id_wrapper).')\s*\+\s*/', '$1 + ', $prefixed_css);
                // Attempt to fix prefixing for general sibling selectors like ~ sibling
                 $prefixed_css = preg_replace('/(#'.esc_attr($unique_id_wrapper).')\s*~\s*/', '$1 ~ ', $prefixed_css);
                 // Remove prefix from html, body (important!)
                 $prefixed_css = preg_replace('/#'.esc_attr($unique_id_wrapper).'\s+(html|body)\b/i', '$1', $prefixed_css);
                 // Remove prefix for direct ID targeting if user uses it
                 $prefixed_css = preg_replace('/#'.esc_attr($unique_id_wrapper).'\s+#([\w-]+)/i', '#$1', $prefixed_css);
                 // Attempt to handle media queries better (don't prefix inside) - This is complex and might not be perfect
                 $prefixed_css = preg_replace_callback('/(@media[^{]+{)(.*?)(})/is', function($matches) use ($unique_id_wrapper) {
                     $inner_css = preg_replace('/(^|\})([^{]+?)({)/', '$1#'.esc_attr($unique_id_wrapper).' $2$3', "\n".$matches[2]."\n");
                      // Clean up inner rules for html/body/ids again
                      $inner_css = preg_replace('/#'.esc_attr($unique_id_wrapper).'\s+(html|body)\b/i', '$1', $inner_css);
                      $inner_css = preg_replace('/#'.esc_attr($unique_id_wrapper).'\s+#([\w-]+)/i', '#$1', $inner_css);
                     return $matches[1] . $inner_css . $matches[3];
                 }, $prefixed_css);
                 // Handle @keyframes - Don't prefix selectors inside
                 $prefixed_css = preg_replace_callback('/(@keyframes\s+[\w-]+)\s*{.*?}/is', function($matches) {
                     return $matches[0]; // Return the whole keyframes block unchanged
                 }, $prefixed_css);


                echo trim($prefixed_css);
                ?>
            </style>

            <div id="<?php echo esc_attr($unique_id_wrapper); ?>" class="yab-welcome-package-banner-container" data-package-key="<?php echo esc_attr($package_key); ?>">
                 <?php // Output the initial content (might be skeleton or actual if no JS needed immediately) ?>
                 <?php echo $skeleton_html; // Start with skeleton ?>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('<?php echo esc_js($unique_id_wrapper); ?>');
                    const packageKey = container.getAttribute('data-package-key');
                    const ajaxUrl = '<?php echo esc_js($ajax_url); ?>';
                    // Store the raw body content (without styles, html, head) in a JS variable
                    const htmlBodyContentTemplate = <?php echo $this->json_encode_options($body_content); ?>;

                    if (!container || !packageKey) {
                        console.error('Welcome Package Banner <?php echo esc_js($unique_id_wrapper); ?>: Missing container or package key.');
                        if(container) container.innerHTML = '<!-- Error loading banner data -->';
                        return;
                    }

                    const fetchAndReplacePlaceholders = () => {
                        const formData = new URLSearchParams();
                        formData.append('action', 'yab_fetch_welcome_package_prices_live');
                        formData.append('package_key', packageKey);

                        fetch(ajaxUrl, {
                            method: 'POST',
                            body: formData,
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(result => {
                            let finalHtml = htmlBodyContentTemplate; // Start with the clean body content
                            if (result.success && result.data) {
                                const prices = result.data;
                                // Replace placeholders
                                finalHtml = finalHtml.replace(/\{\{\s*originalPrice\s*\}\}/g, prices.originalMoneyValue || 'N/A');
                                finalHtml = finalHtml.replace(/\{\{\s*discountedPrice\s*\}\}/g, prices.moneyValue || 'N/A');
                                finalHtml = finalHtml.replace(/\{\{\s*key\s*\}\}/g, prices.key || packageKey);
                            } else {
                                 console.error('Welcome Package Banner <?php echo esc_js($unique_id_wrapper); ?>: API Error -', result.data?.message || 'Failed to fetch prices.');
                                 // Replace placeholders with 'Error' in case of API failure
                                 finalHtml = finalHtml.replace(/\{\{\s*originalPrice\s*\}\}/g, 'Error');
                                 finalHtml = finalHtml.replace(/\{\{\s*discountedPrice\s*\}\}/g, 'Error');
                                 finalHtml = finalHtml.replace(/\{\{\s*key\s*\}\}/g, packageKey); // Keep the key
                                 container.style.border = '1px dashed red'; // Indicate error visually if needed
                            }
                             container.innerHTML = finalHtml; // Replace skeleton/initial content with final HTML
                        })
                        .catch(error => {
                            console.error('Welcome Package Banner <?php echo esc_js($unique_id_wrapper); ?> Fetch Error:', error);
                            // Replace placeholders with 'Error' in case of network/fetch error
                             let errorHtml = htmlBodyContentTemplate;
                             errorHtml = errorHtml.replace(/\{\{\s*originalPrice\s*\}\}/g, 'Error');
                             errorHtml = errorHtml.replace(/\{\{\s*discountedPrice\s*\}\}/g, 'Error');
                             errorHtml = errorHtml.replace(/\{\{\s*key\s*\}\}/g, packageKey); // Keep the key
                             container.innerHTML = errorHtml;
                            container.style.border = '1px dashed red'; // Indicate error
                        });
                    };

                    fetchAndReplacePlaceholders(); // Fetch on initial load
                });
            </script>
            <?php
            return ob_get_clean();
        }

        // Helper to encode JSON with options suitable for embedding in JS
        private function json_encode_options($value) {
            // Using JSON_INVALID_UTF8_IGNORE to prevent potential errors with user HTML
            return json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
        }
    }
}
?>

