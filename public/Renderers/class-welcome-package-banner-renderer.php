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

            // --- Robust Content Extraction ---
            $style_content = '';
            $body_content = ''; // This should ONLY contain the main HTML structure

            // 1. Extract style content
            if (preg_match('/<style.*?>(.*?)<\/style>/is', $html_template_raw, $style_matches)) {
                $style_content = trim($style_matches[1]);
            }

            // 2. Extract the main banner structure (assuming it's the first top-level element after potential head/style)
            //    Or specifically look for <article class="banner">
             if (preg_match('/<article class="banner".*?<\/article>/is', $html_template_raw, $article_matches)) {
                 $body_content = $article_matches[0];
             } else if (preg_match('/<body.*?>(.*?)<\/body>/is', $html_template_raw, $body_matches)) {
                // Fallback: Try to get content from body, excluding style tag if it was inside body
                $temp_body = trim($body_matches[1]);
                $body_content = preg_replace('/<style.*?>(.*?)<\/style>/is', '', $temp_body);
            } else {
                // Fallback: Remove html, head, style, body tags and hope the rest is the main content
                $body_content = preg_replace('/<html[^>]*>.*?<\/html>/is', '', $html_template_raw);
                $body_content = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $body_content);
                $body_content = preg_replace('/<style.*?>(.*?)<\/style>/is', '', $body_content);
                $body_content = preg_replace('/<body[^>]*>/i', '', $body_content); // Remove opening body tag
                $body_content = preg_replace('/<\/body>/i', '', $body_content); // Remove closing body tag
                $body_content = trim($body_content);
            }
            // --- End Robust Content Extraction ---

             // --- Simplified CSS Scoping (Output styles within the container) ---
             $scoped_style_tag = '';
             if (!empty($style_content)) {
                 // We simply wrap the user's styles in a style tag INSIDE the container.
                 // This relies on the container ID for scoping via descendant selectors in user's CSS.
                 // Note: Targeting 'html' or 'body' within user styles might still cause issues.
                 $scoped_style_tag = "<style>\n" . $style_content . "\n</style>";
             }
             // --- End Simplified CSS Scoping ---

             // Skeleton HTML
             $skeleton_html = '<div class="yab-wpb-skeleton" style="width: 100%; min-height: 100px; background-color: #f0f0f0; border-radius: 8px; display:flex; align-items:center; justify-content:center; color:#ccc;">Loading Prices...</div>';

            ob_start();
            ?>
            <div id="<?php echo esc_attr($unique_id_wrapper); ?>" class="yab-welcome-package-banner-container" data-package-key="<?php echo esc_attr($package_key); ?>">
                <?php // Output scoped styles FIRST, then the skeleton ?>
                <?php echo $scoped_style_tag; ?>
                <?php echo $skeleton_html; // Start with skeleton ?>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('<?php echo esc_js($unique_id_wrapper); ?>');
                    const packageKey = container.getAttribute('data-package-key');
                    const ajaxUrl = '<?php echo esc_js($ajax_url); ?>';
                    // Store ONLY the clean extracted BODY/ARTICLE content
                    const htmlBodyContentTemplate = <?php echo $this->json_encode_options($body_content); ?>;
                     // Store the style tag separately if needed later by JS (unlikely for now)
                     // const htmlStyleContent = <?php // echo $this->json_encode_options($scoped_style_tag); ?>;


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
                             // Start with the clean body template for replacement
                            let finalHtml = htmlBodyContentTemplate;
                            if (result.success && result.data) {
                                const prices = result.data;

                                // Check and convert prices to number before using toFixed
                                const originalPriceNum = Number(prices.originalMoneyValue);
                                const discountedPriceNum = Number(prices.moneyValue);

                                // --- Format Price (Example: add currency, format decimals) ---
                                // You might want to adjust formatting based on currency/locale
                                const formatCurrency = (num) => {
                                    if (isNaN(num)) return 'N/A';
                                    // Example: Format to 2 decimal places with comma separators
                                    return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                };
                                const originalPriceFormatted = formatCurrency(originalPriceNum);
                                const discountedPriceFormatted = formatCurrency(discountedPriceNum);
                                // --- End Format Price ---


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
                             // Inject the final HTML content *after* the existing style tag
                             const skeleton = container.querySelector('.yab-wpb-skeleton');
                             if (skeleton) {
                                 skeleton.outerHTML = finalHtml; // Replace skeleton specifically
                             } else {
                                 // If skeleton wasn't found (maybe already replaced), clear and set
                                 const styleTag = container.querySelector('style');
                                 container.innerHTML = (styleTag ? styleTag.outerHTML : '') + finalHtml;
                             }
                        })
                        .catch(error => {
                            console.error('Welcome Package Banner <?php echo esc_js($unique_id_wrapper); ?> Fetch Error:', error);
                             let errorHtml = htmlBodyContentTemplate;
                             errorHtml = errorHtml.replace(/\{\{\s*originalPrice\s*\}\}/g, 'Error');
                             errorHtml = errorHtml.replace(/\{\{\s*discountedPrice\s*\}\}/g, 'Error');
                             errorHtml = errorHtml.replace(/\{\{\s*key\s*\}\}/g, packageKey);
                             // Inject error HTML *after* the existing style tag
                              const skeleton = container.querySelector('.yab-wpb-skeleton');
                              if (skeleton) {
                                  skeleton.outerHTML = errorHtml; // Replace skeleton specifically
                              } else {
                                   const styleTag = container.querySelector('style');
                                   container.innerHTML = (styleTag ? styleTag.outerHTML : '') + errorHtml;
                              }
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

