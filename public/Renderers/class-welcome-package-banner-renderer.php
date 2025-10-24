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
            // Get the raw HTML template saved by the user
            $html_template_raw = $this->data['welcome_package']['htmlContent'];
            $unique_id_wrapper = 'yab-wpb-wrapper-' . $banner_id . '-' . wp_rand(1000, 9999);
            $unique_id_iframe = 'yab-wpb-iframe-' . $banner_id . '-' . wp_rand(1000, 9999);
            $ajax_url = admin_url('admin-ajax.php');

            // Basic skeleton for the iframe content itself
            $iframe_skeleton_html = '<div style="width: 100%; height: 100px; background-color: #f0f0f0; border-radius: 8px; display:flex; align-items:center; justify-content:center; color:#ccc;">Loading...</div>';

            // Prepare the HTML for srcdoc, including the script to fetch prices *inside* the iframe
            // IMPORTANT: Use specific, unique IDs/Classes inside the template if needed for targeting by the script.
            // Example: Add spans with IDs like <span id="original-price-placeholder"></span>
            $iframe_content_html = <<<HTML
<!DOCTYPE html>
<html style="margin:0; padding:0; height:100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Package Banner</title>
    <style>
        body { margin: 0; padding: 0; font-family: sans-serif; height: 100%; display: flex; flex-direction: column; }
        .banner-content-wrapper { flex-grow: 1; /* Allows content to fill iframe */ }
        /* Add basic styles or link to external CSS if absolutely necessary and allowed by sandbox */
    </style>
</head>
<body id="banner-body-{$banner_id}">
    <div class="banner-content-wrapper">
        {$iframe_skeleton_html} <!-- Initial skeleton inside iframe -->
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const packageKey = '{$package_key}'; // Directly embed key
            const htmlTemplate = {$this->json_encode_options($html_template_raw)}; // Embed raw template
            const ajaxUrl = '{$ajax_url}';
            const contentWrapper = document.querySelector('.banner-content-wrapper');
            const bodyElement = document.body;

            if (!packageKey || !htmlTemplate || !contentWrapper) {
                console.error('Iframe Error: Missing packageKey, htmlTemplate, or contentWrapper.');
                contentWrapper.innerHTML = '<!-- Error loading banner content -->';
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
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(result => {
                    if (result.success && result.data) {
                        const prices = result.data;
                        let finalHtml = htmlTemplate;
                        finalHtml = finalHtml.replace(/\{\{\s*originalPrice\s*\}\}/g, prices.originalMoneyValue || 'N/A');
                        finalHtml = finalHtml.replace(/\{\{\s*discountedPrice\s*\}\}/g, prices.moneyValue || 'N/A');
                        finalHtml = finalHtml.replace(/\{\{\s*key\s*\}\}/g, prices.key || packageKey);

                        contentWrapper.innerHTML = finalHtml;

                        // Attempt to adjust iframe height based on content
                        // Note: This might be unreliable depending on content complexity and CSS.
                        // Consider setting a fixed or aspect-ratio height via CSS if possible.
                         setTimeout(() => { // Allow content to render first
                             const newHeight = bodyElement.scrollHeight;
                            if (window.parent && window.parent !== window) {
                                window.parent.postMessage({
                                    type: 'resize-iframe',
                                    iframeId: '{$unique_id_iframe}',
                                    height: newHeight
                                }, '*'); // Adjust target origin in production
                             }
                         }, 100);


                    } else {
                        throw new Error(result.data?.message || 'Failed to process package prices.');
                    }
                })
                .catch(error => {
                    console.error('Iframe Fetch Error:', error);
                    contentWrapper.innerHTML = htmlTemplate
                                .replace(/\{\{\s*originalPrice\s*\}\}/g, 'Error')
                                .replace(/\{\{\s*discountedPrice\s*\}\}/g, 'Error')
                                .replace(/\{\{\s*key\s*\}\}/g, packageKey);
                     contentWrapper.style.border = '1px dashed red';

                     // Send resize message even on error to potentially adjust height
                     setTimeout(() => {
                         const errorHeight = bodyElement.scrollHeight;
                         if (window.parent && window.parent !== window) {
                            window.parent.postMessage({
                                type: 'resize-iframe',
                                iframeId: '{$unique_id_iframe}',
                                height: errorHeight
                            }, '*');
                         }
                     }, 100);
                });
            };

            fetchAndRenderPrices();
        });
    <\/script>
</body>
</html>
HTML;

            ob_start();
            ?>
            <div id="<?php echo esc_attr($unique_id_wrapper); ?>" class="yab-welcome-package-banner-wrapper">
                <iframe
                    id="<?php echo esc_attr($unique_id_iframe); ?>"
                    srcdoc="<?php echo esc_attr($iframe_content_html); ?>"
                    style="width: 100%; border: none; display: block; min-height: 100px; height: 100px; /* Initial height */ transition: height 0.3s ease;"
                    sandbox="allow-scripts allow-same-origin allow-forms"
                    scrolling="no"
                    title="Welcome Package Banner Content"
                ></iframe>
            </div>
             <script>
                // Listen for messages from the iframe to resize it
                window.addEventListener('message', function(event) {
                    // IMPORTANT: Add origin check in production for security
                    // if (event.origin !== 'https://your-expected-origin.com') return;

                    if (event.data && event.data.type === 'resize-iframe') {
                        const iframe = document.getElementById(event.data.iframeId);
                        if (iframe && event.data.height) {
                            // Add some padding or use Math.max for minimum height
                            const newHeight = Math.max(100, event.data.height + 10); // Min height 100px, add 10px buffer
                            iframe.style.height = newHeight + 'px';
                        }
                    }
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

