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

            // --- CSS Isolation Logic ---
            $scoped_css = '';
            $html_content_without_style = preg_replace_callback(
                '/<style.*?>(.*?)<\/style>/is', // Match style tags (non-greedy)
                function($matches) use (&$scoped_css, $unique_id_wrapper) {
                    $css_content = $matches[1];
                    // Basic prefixing (might need refinement for complex selectors)
                    $prefixed_rules = preg_replace_callback(
                         '/([^\r\n ,{}]+)(,(?=[^}]*{)|\s*{)/', // Selectors before commas or opening braces
                        function($selector_matches) use ($unique_id_wrapper) {
                            $selector = trim($selector_matches[1]);
                             // Avoid prefixing html, body, @keyframes, @media etc.
                             if (preg_match('/^(html|body|@|:)/i', $selector)) {
                                 return $selector_matches[0]; // Return original selector + comma/brace
                             }
                            // Basic prefixing: Add the ID before each selector part
                            $parts = preg_split('/\s+/', $selector); // Split complex selectors like .class1 .class2
                            $prefixed_parts = array_map(function($part) use ($unique_id_wrapper) {
                                // Add ID unless it's a pseudo-element/class starting with ':'
                                return (strpos($part, ':') === 0) ? $part : '#' . $unique_id_wrapper . ' ' . $part;
                            }, $parts);

                            return implode(' ', $prefixed_parts) . $selector_matches[2]; // Re-add comma or brace
                        },
                        $css_content
                    );
                    $scoped_css .= $prefixed_rules . "\n";
                    return ''; // Remove the original style tag from HTML
                },
                $html_template_raw
            );
            // --- End CSS Isolation Logic ---

             // Skeleton HTML to show while loading
             $skeleton_html = '<div class="yab-wpb-skeleton" style="width: 100%; min-height: 100px; background-color: #f0f0f0; border-radius: 8px; display:flex; align-items:center; justify-content:center; color:#ccc;">Loading...</div>';

            ob_start();
            ?>
            <style>
                <?php echo $scoped_css; /* Output scoped CSS */ ?>
            </style>
            <div id="<?php echo esc_attr($unique_id_wrapper); ?>" class="yab-welcome-package-banner-container" data-package-key="<?php echo esc_attr($package_key); ?>">
                 <?php echo $skeleton_html; // Initial skeleton ?>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('<?php echo esc_js($unique_id_wrapper); ?>');
                    const packageKey = container.getAttribute('data-package-key');
                    // Use the HTML template *without* the style tags
                    const htmlTemplate = <?php echo $this->json_encode_options($html_content_without_style); ?>;
                    const ajaxUrl = '<?php echo esc_js($ajax_url); ?>';

                    if (!container || !packageKey || !htmlTemplate) {
                        console.error('Welcome Package Banner <?php echo esc_js($unique_id_wrapper); ?>: Missing required data.');
                        if(container) container.innerHTML = '<!-- Error loading banner data -->';
                        return;
                    }

                    const fetchAndRenderPrices = () => {
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
                            if (result.success && result.data) {
                                const prices = result.data;
                                let finalHtml = htmlTemplate;

                                // Replace placeholders
                                finalHtml = finalHtml.replace(/\{\{\s*originalPrice\s*\}\}/g, prices.originalMoneyValue || 'N/A');
                                finalHtml = finalHtml.replace(/\{\{\s*discountedPrice\s*\}\}/g, prices.moneyValue || 'N/A');
                                finalHtml = finalHtml.replace(/\{\{\s*key\s*\}\}/g, prices.key || packageKey);

                                container.innerHTML = finalHtml; // Replace skeleton with final content
                            } else {
                                throw new Error(result.data?.message || 'Failed to fetch or find package prices.');
                            }
                        })
                        .catch(error => {
                            console.error('Welcome Package Banner <?php echo esc_js($unique_id_wrapper); ?> Error:', error);
                            // Display template with error messages or keep skeleton
                            container.innerHTML = htmlTemplate
                                .replace(/\{\{\s*originalPrice\s*\}\}/g, 'Error')
                                .replace(/\{\{\s*discountedPrice\s*\}\}/g, 'Error')
                                .replace(/\{\{\s*key\s*\}\}/g, packageKey);
                            container.style.border = '1px dashed red'; // Indicate error
                        });
                    };

                    fetchAndRenderPrices(); // Fetch on initial load
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

