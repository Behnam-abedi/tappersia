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

            // --- Simplified Content Extraction ---
            // Remove <html> and <head> tags completely, keep everything else including <style>
            $content_to_render = preg_replace('/<html[^>]*>.*?<\/html>/is', '', $html_template_raw); // Remove <html> tag and its content entirely
            $content_to_render = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $content_to_render); // Remove <head> tag and its content entirely
             // Remove <body> tag but keep its content
             $content_to_render = preg_replace('/<body[^>]*>(.*?)<\/body>/is', '$1', $content_to_render);
             $content_to_render = trim($content_to_render); // Trim whitespace
            // --- End Simplified Content Extraction ---


             // Skeleton HTML to show while loading - Simple version
             $skeleton_html = '<div class="yab-wpb-skeleton" style="width: 100%; min-height: 100px; background-color: #f0f0f0; border-radius: 8px; display:flex; align-items:center; justify-content:center; color:#ccc;">Loading...</div>';

            ob_start();
            ?>
            <?php // Styles are now expected to be within $content_to_render ?>

            <div id="<?php echo esc_attr($unique_id_wrapper); ?>" class="yab-welcome-package-banner-container" data-package-key="<?php echo esc_attr($package_key); ?>">
                 <?php // Output the initial content (includes user's <style>, placeholders will be replaced by JS) ?>
                 <?php echo $content_to_render; // Output extracted content directly ?>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('<?php echo esc_js($unique_id_wrapper); ?>');
                    const packageKey = container.getAttribute('data-package-key');
                    const ajaxUrl = '<?php echo esc_js($ajax_url); ?>';

                    if (!container || !packageKey) {
                        console.error('Welcome Package Banner <?php echo esc_js($unique_id_wrapper); ?>: Missing container or package key.');
                        if(container) container.innerHTML = '<!-- Error loading banner data -->';
                        return;
                    }

                    // Show skeleton initially if the rendered content is empty or placeholder-like
                    if (container.innerHTML.trim().length === 0 || container.querySelector('.yab-wpb-skeleton')) {
                         container.innerHTML = <?php echo $this->json_encode_options($skeleton_html); ?>;
                    }
                    // Store the raw template that was initially rendered (might contain placeholders)
                    const initialHtmlWithPlaceholders = <?php echo $this->json_encode_options($content_to_render); ?>;


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
                             // Start with the raw template for replacement
                            let finalHtml = initialHtmlWithPlaceholders;
                            if (result.success && result.data) {
                                const prices = result.data;

                                // --- FIX: Check and convert prices to number before using toFixed ---
                                const originalPriceNum = Number(prices.originalMoneyValue);
                                const discountedPriceNum = Number(prices.moneyValue);

                                const originalPriceFormatted = !isNaN(originalPriceNum) ? originalPriceNum.toFixed(3) : 'N/A';
                                const discountedPriceFormatted = !isNaN(discountedPriceNum) ? discountedPriceNum.toFixed(3) : 'N/A';
                                // --- End FIX ---

                                // Replace placeholders using formatted values
                                finalHtml = finalHtml.replace(/\{\{\s*originalPrice\s*\}\}/g, originalPriceFormatted);
                                finalHtml = finalHtml.replace(/\{\{\s*discountedPrice\s*\}\}/g, discountedPriceFormatted);
                                finalHtml = finalHtml.replace(/\{\{\s*key\s*\}\}/g, prices.key || packageKey);
                            } else {
                                 console.error('Welcome Package Banner <?php echo esc_js($unique_id_wrapper); ?>: API Error -', result.data?.message || 'Failed to fetch prices.');
                                 // Replace placeholders with 'Error' in case of API failure
                                 finalHtml = finalHtml.replace(/\{\{\s*originalPrice\s*\}\}/g, 'Error');
                                 finalHtml = finalHtml.replace(/\{\{\s*discountedPrice\s*\}\}/g, 'Error');
                                 finalHtml = finalHtml.replace(/\{\{\s*key\s*\}\}/g, packageKey); // Keep the key
                                 container.style.outline = '1px dashed red'; // Indicate error visually if needed
                            }
                             container.innerHTML = finalHtml; // Replace skeleton/initial content with final HTML
                        })
                        .catch(error => {
                            console.error('Welcome Package Banner <?php echo esc_js($unique_id_wrapper); ?> Fetch Error:', error);
                            // Replace placeholders with 'Error' in case of network/fetch error
                             let errorHtml = initialHtmlWithPlaceholders;
                             errorHtml = errorHtml.replace(/\{\{\s*originalPrice\s*\}\}/g, 'Error');
                             errorHtml = errorHtml.replace(/\{\{\s*discountedPrice\s*\}\}/g, 'Error');
                             errorHtml = errorHtml.replace(/\{\{\s*key\s*\}\}/g, packageKey); // Keep the key
                             container.innerHTML = errorHtml;
                            container.style.outline = '1px dashed red'; // Indicate error
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

