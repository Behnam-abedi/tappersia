<?php
// tappersia/public/Renderers/class-welcome-package-banner-renderer.php

if (!class_exists('Yab_Welcome_Package_Banner_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Welcome_Package_Banner_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            // Check if essential data exists
            if (empty($this->data['welcome_package']) || empty($this->banner_id)) {
                return '';
            }

            $banner_id = $this->banner_id;
            $placeholder_id = "yab-wp-banner-placeholder-" . $banner_id;

            ob_start();
            ?>
            <div id="<?php echo esc_attr($placeholder_id); ?>" class="yab-wp-banner-placeholder yab-skeleton-loader" style="width: 100%; min-height: 100px; background-color: #f0f0f0; border-radius: 8px; margin: 20px 0;">
                 <div style="padding: 20px; text-align: center; color: #ccc;">Loading banner...</div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const placeholder_<?php echo $banner_id; ?> = document.getElementById('<?php echo esc_js($placeholder_id); ?>');
                if (!placeholder_<?php echo $banner_id; ?>) return;

                // Simple fetch to the SSR endpoint
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        'action': 'yab_render_welcome_package_ssr', // The new SSR action
                        'banner_id': '<?php echo $banner_id; ?>',
                        // Add nonce if protection is needed even for GET-like SSR rendering
                        // 'nonce': '<?php // echo wp_create_nonce('yab_render_wp_banner'); ?>'
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                 })
                .then(result => {
                    if (result.success && result.data && result.data.html) {
                        placeholder_<?php echo $banner_id; ?>.innerHTML = result.data.html;
                        // Remove skeleton class after loading
                        placeholder_<?php echo $banner_id; ?>.classList.remove('yab-skeleton-loader');
                        placeholder_<?php echo $banner_id; ?>.style.minHeight = ''; // Remove min-height
                        placeholder_<?php echo $banner_id; ?>.style.backgroundColor = ''; // Remove background color
                        placeholder_<?php echo $banner_id; ?>.style.borderRadius = ''; // Remove border radius

                        // Find and execute any <script> tags within the loaded HTML
                        const scripts = placeholder_<?php echo $banner_id; ?>.querySelectorAll('script');
                        scripts.forEach(oldScript => {
                            const newScript = document.createElement('script');
                            // Copy attributes
                            Array.from(oldScript.attributes).forEach(attr => {
                                newScript.setAttribute(attr.name, attr.value);
                            });
                            // Copy content
                            newScript.textContent = oldScript.textContent;
                            // Replace old script with new one to trigger execution
                            oldScript.parentNode.replaceChild(newScript, oldScript);
                        });

                    } else {
                        console.error('Failed to load Welcome Package banner:', result.data ? result.data.message : 'Unknown error');
                        placeholder_<?php echo $banner_id; ?>.innerHTML = '';
                        placeholder_<?php echo $banner_id; ?>.classList.remove('yab-skeleton-loader');
                    }
                })
                .catch(error => {
                    console.error('Error fetching Welcome Package banner:', error);
                    placeholder_<?php echo $banner_id; ?>.innerHTML = '';
                    placeholder_<?php echo $banner_id; ?>.classList.remove('yab-skeleton-loader');
                });
            });
            </script>
             <style>
                /* Reuse existing skeleton loader styles if available or define basic ones */
                .yab-wp-banner-placeholder.yab-skeleton-loader { position: relative; overflow: hidden; }
                .yab-wp-banner-placeholder.yab-skeleton-loader::before {
                    content: ''; position: absolute; inset: 0; transform: translateX(-100%);
                    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
                    animation: yab-shimmer-wp-<?php echo $banner_id; ?> 1.5s infinite;
                }
                 @keyframes yab-shimmer-wp-<?php echo $banner_id; ?> { 100% { transform: translateX(100%); } }
            </style>
            <?php
            return ob_get_clean();
        }

        // We don't need get_background_style, get_image_style etc. for this renderer
        // as the HTML comes directly from the settings.
    }
}