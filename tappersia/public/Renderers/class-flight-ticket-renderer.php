<?php
// tappersia/public/Renderers/class-flight-ticket-renderer.php

if (!class_exists('Yab_Flight_Ticket_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    /**
     * رندر کننده بنر بلیت پرواز
     *
     * این کلاس یک placeholder رندر می‌کند و سپس با AJAX درخواست HTML نهایی
     * را می‌دهد تا قیمت‌ها و لینک‌ها همیشه به‌روز باشند.
     */
    class Yab_Flight_Ticket_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            // اطمینان از وجود دیتای اولیه و آیدی بنر
            if (empty($this->data['flight_ticket']) || empty($this->banner_id)) {
                return '';
            }

            $banner_id = $this->banner_id;
            $placeholder_id = "yab-flight-ticket-placeholder-" . $banner_id;

            ob_start();
            ?>
            
            <div id="<?php echo esc_attr($placeholder_id); ?>" class="yab-flight-ticket-placeholder yab-skeleton-loader" style="width: 100%; min-height: 150px; background-color: #f0f0f0; border-radius: 16px; margin: 20px 0; max-width: 700px; position: relative; overflow: hidden;">
                 <div style="padding: 20px; text-align: center; color: #ccc; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">Loading flight deals...</div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const placeholder_<?php echo $banner_id; ?> = document.getElementById('<?php echo esc_js($placeholder_id); ?>');
                if (!placeholder_<?php echo $banner_id; ?>) return;

                // درخواست به اکشن AJAX عمومی که قیمت به‌روز را محاسبه می‌کند
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        'action': 'yab_render_flight_ticket_ssr', // اکشن AJAX عمومی جدید
                        'banner_id': '<?php echo $banner_id; ?>'
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
                        // جایگزینی محتوای placeholder با HTML نهایی
                        placeholder_<?php echo $banner_id; ?>.innerHTML = result.data.html;
                        
                        // حذف کلاس‌ها و استایل‌های اسکلتون
                        placeholder_<?php echo $banner_id; ?>.classList.remove('yab-skeleton-loader');
                        placeholder_<?php echo $banner_id; ?>.style.minHeight = '';
                        placeholder_<?php echo $banner_id; ?>.style.backgroundColor = '';
                        placeholder_<?php echo $banner_id; ?>.style.borderRadius = '';
                        placeholder_<?php echo $banner_id; ?>.style.margin = '';
                        placeholder_<?php echo $banner_id; ?>.style.maxWidth = '';
                        placeholder_<?php echo $banner_id; ?>.style.position = '';
                        placeholder_<?php echo $banner_id; ?>.style.overflow = '';

                        // حذف اجرای مجدد تگ‌های اسکریپت مرتبط با clip-path که اکنون حذف شده است
                        // (چون SSR ما دیگر اسکریپت clip-path را بر نمی‌گرداند، این بخش نیازی به کدهای پیچیده ندارد)

                    } else {
                        console.error('Tappersia: Failed to load Flight Ticket banner:', result.data ? result.data.message : 'Unknown error');
                        placeholder_<?php echo $banner_id; ?>.innerHTML = '';
                        placeholder_<?php echo $banner_id; ?>.classList.remove('yab-skeleton-loader');
                    }
                })
                .catch(error => {
                    console.error('Tappersia: Error fetching Flight Ticket banner:', error);
                    placeholder_<?php echo $banner_id; ?>.innerHTML = '';
                    placeholder_<?php echo $banner_id; ?>.classList.remove('yab-skeleton-loader');
                });
            });
            </script>
            
            <style>
                .yab-flight-ticket-placeholder.yab-skeleton-loader::before {
                    content: ''; 
                    position: absolute; 
                    inset: 0; 
                    transform: translateX(-100%);
                    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
                    animation: yab-shimmer-ft-<?php echo $banner_id; ?> 1.5s infinite;
                }
                 @keyframes yab-shimmer-ft-<?php echo $banner_id; ?> { 
                    100% { transform: translateX(100%); } 
                 }
            </style>
            <?php
            return ob_get_clean();
        }
    }
}