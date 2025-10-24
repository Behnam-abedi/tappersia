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
            $html_template = $this->data['welcome_package']['htmlContent']; // Use the saved HTML content
            $unique_id = 'yab-wpb-' . $banner_id . '-' . wp_rand(1000, 9999); // Unique ID for the wrapper

            ob_start();
            ?>
            <div id="<?php echo esc_attr($unique_id); ?>" class="yab-welcome-package-banner-container" data-package-key="<?php echo esc_attr($package_key); ?>" style="visibility: hidden; min-height: 50px;"> <!-- Start hidden, add min-height -->
                <!-- Placeholder for dynamic content -->
                <div class="yab-skeleton-loader" style="width: 100%; height: 100px; background-color: #f0f0f0; border-radius: 8px;"></div> <!-- Basic Skeleton -->
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('<?php echo esc_js($unique_id); ?>');
                    const packageKey = container.getAttribute('data-package-key');
                    const htmlTemplate = <?php echo json_encode($html_template); ?>; // Pass template to JS
                    const ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';

                    if (!container || !packageKey || !htmlTemplate) {
                        console.error('Welcome Package Banner <?php echo esc_js($unique_id); ?>: Missing required data.');
                        container.style.visibility = 'visible'; // Show skeleton on error
                        container.innerHTML = '<!-- Error loading banner -->';
                        return;
                    }

                    // Function to fetch prices and render
                    const fetchAndRenderPrices = () => {
                        const formData = new URLSearchParams();
                        formData.append('action', 'yab_fetch_welcome_package_prices_live');
                        formData.append('package_key', packageKey);
                        // Add nonce if needed for security, though less critical for public-facing price fetch
                        // formData.append('_ajax_nonce', 'your_nonce_here'); // Generate a nonce if required

                        fetch(ajaxUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
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

                                // Replace placeholders - use specific placeholders
                                finalHtml = finalHtml.replace(/\{\{\s*originalPrice\s*\}\}/g, prices.originalMoneyValue || 'N/A');
                                finalHtml = finalHtml.replace(/\{\{\s*discountedPrice\s*\}\}/g, prices.moneyValue || 'N/A');
                                finalHtml = finalHtml.replace(/\{\{\s*key\s*\}\}/g, prices.key || packageKey); // Also replace key if needed

                                container.innerHTML = finalHtml;
                                container.style.visibility = 'visible'; // Show content
                            } else {
                                throw new Error(result.data?.message || 'Failed to fetch or find package prices.');
                            }
                        })
                        .catch(error => {
                            console.error('Welcome Package Banner <?php echo esc_js($unique_id); ?> Error:', error);
                            // Optionally display the template with placeholders or an error message
                            container.innerHTML = htmlTemplate
                                .replace(/\{\{\s*originalPrice\s*\}\}/g, 'Error')
                                .replace(/\{\{\s*discountedPrice\s*\}\}/g, 'Error')
                                .replace(/\{\{\s*key\s*\}\}/g, packageKey);
                            container.style.visibility = 'visible';
                            container.style.border = '1px dashed red'; // Indicate error visually
                        });
                    };

                    fetchAndRenderPrices(); // Fetch on initial load
                });
            </script>
            <?php
            return ob_get_clean();
        }
    }
}
