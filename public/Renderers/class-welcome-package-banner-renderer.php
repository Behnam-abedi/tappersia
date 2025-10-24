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

            // --- Refined Content Extraction ---
            $style_content = '';
            $body_content = '';

            // 1. Extract style content
            if (preg_match('/<style.*?>(.*?)<\/style>/is', $html_template_raw, $style_matches)) {
                $style_content = trim($style_matches[1]);
            }

            // 2. Extract body content
            if (preg_match('/<body.*?>(.*?)<\/body>/is', $html_template_raw, $body_matches)) {
                $body_content = trim($body_matches[1]);
            } else {
                // If no body tag, extract everything excluding html, head, style
                $body_content = preg_replace('/<html[^>]*>.*?<\/html>/is', '', $html_template_raw);
                $body_content = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $body_content);
                $body_content = preg_replace('/<style.*?>(.*?)<\/style>/is', '', $body_content); // Remove style from body if no body tag found
                $body_content = trim($body_content);
            }
            // --- End Refined Content Extraction ---

            // --- Refined CSS Scoping Logic ---
            $scoped_css = '';
            if (!empty($style_content)) {
                // Basic prefixing - add ID before each selector group
                // This won't handle complex cases like @media, @keyframes perfectly but is safer
                $scoped_css = preg_replace_callback(
                    '/([^{}]+)({[^{}]+})/s', // Match selectors { declarations }
                    function ($matches) use ($unique_id_wrapper) {
                        $selectors = trim($matches[1]);
                        $declarations = trim($matches[2]);

                        // Avoid prefixing keyframes, media queries definitions, :root, html, body
                        if (preg_match('/^@(keyframes|media|font-face)/i', $selectors) || preg_match('/^(html|body|:root)\b/i', $selectors)) {
                            // For @media, try to prefix rules inside (basic attempt)
                            if (preg_match('/^@media/i', $selectors)) {
                                $declarations_inner = preg_replace_callback(
                                    '/([^{}]+)({[^{}]+})/s',
                                    function ($inner_matches) use ($unique_id_wrapper) {
                                        $inner_selectors = trim($inner_matches[1]);
                                        $inner_declarations = trim($inner_matches[2]);
                                        if (preg_match('/^(html|body|:root)\b/i', $inner_selectors)) {
                                             return $inner_matches[0]; // Don't prefix html/body inside media query
                                        }
                                        $prefixed_inner_selectors = preg_replace('/([^,\s]+)/', '#' . $unique_id_wrapper . ' $1', $inner_selectors);
                                        return $prefixed_inner_selectors . ' ' . $inner_declarations;
                                    },
                                    trim(substr($declarations, 1, -1)) // Process content inside {}
                                );
                                return $selectors . ' {' . $declarations_inner . '}';
                            }
                             return $matches[0]; // Return @keyframes, :root etc. as is
                        }

                        // Prefix each selector in a comma-separated list
                        $prefixed_selectors = implode(', ', array_map(function ($selector) use ($unique_id_wrapper) {
                             $selector = trim($selector);
                             // Avoid double prefixing if ID already exists (less likely but safe)
                             if (strpos($selector, '#' . $unique_id_wrapper) === 0) {
                                 return $selector;
                             }
                            return '#' . $unique_id_wrapper . ' ' . $selector;
                        }, explode(',', $selectors)));

                        return $prefixed_selectors . ' ' . $declarations;
                    },
                    $style_content
                );
            }
            // --- End Refined CSS Scoping Logic ---


             // Skeleton HTML
             $skeleton_html = '<div class="yab-wpb-skeleton" style="width: 100%; min-height: 100px; background-color: #f0f0f0; border-radius: 8px; display:flex; align-items:center; justify-content:center; color:#ccc;">Loading...</div>';

            ob_start();
            ?>
            <?php // Output the scoped styles ?>
            <style>
                <?php echo $scoped_css; ?>
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
                    // Store the raw BODY content (without styles, html, head) in a JS variable
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
                             // Start with the raw body template for replacement
                            let finalHtml = htmlBodyContentTemplate;
                            if (result.success && result.data) {
                                const prices = result.data;

                                // Check and convert prices to number before using toFixed
                                const originalPriceNum = Number(prices.originalMoneyValue);
                                const discountedPriceNum = Number(prices.moneyValue);

                                const originalPriceFormatted = !isNaN(originalPriceNum) ? originalPriceNum.toFixed(3) : 'N/A';
                                const discountedPriceFormatted = !isNaN(discountedPriceNum) ? discountedPriceNum.toFixed(3) : 'N/A';

                                // Replace placeholders using formatted values
                                finalHtml = finalHtml.replace(/\{\{\s*originalPrice\s*\}\}/g, originalPriceFormatted);
                                finalHtml = finalHtml.replace(/\{\{\s*discountedPrice\s*\}\}/g, discountedPriceFormatted);
                                finalHtml = finalHtml.replace(/\{\{\s*key\s*\}\}/g, prices.key || packageKey);
                            } else {
                                 console.error('Welcome Package Banner <?php echo esc_js($unique_id_wrapper); ?>: API Error -', result.data?.message || 'Failed to fetch prices.');
                                 finalHtml = finalHtml.replace(/\{\{\s*originalPrice\s*\}\}/g, 'Error');
                                 finalHtml = finalHtml.replace(/\{\{\s*discountedPrice\s*\}\}/g, 'Error');
                                 finalHtml = finalHtml.replace(/\{\{\s*key\s*\}\}/g, packageKey);
                                 container.style.outline = '1px dashed red';
                            }
                             container.innerHTML = finalHtml; // Replace skeleton with final HTML
                        })
                        .catch(error => {
                            console.error('Welcome Package Banner <?php echo esc_js($unique_id_wrapper); ?> Fetch Error:', error);
                             let errorHtml = htmlBodyContentTemplate;
                             errorHtml = errorHtml.replace(/\{\{\s*originalPrice\s*\}\}/g, 'Error');
                             errorHtml = errorHtml.replace(/\{\{\s*discountedPrice\s*\}\}/g, 'Error');
                             errorHtml = errorHtml.replace(/\{\{\s*key\s*\}\}/g, packageKey);
                             container.innerHTML = errorHtml;
                            container.style.outline = '1px dashed red';
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

