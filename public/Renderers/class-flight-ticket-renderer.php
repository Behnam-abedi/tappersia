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

            // +++ START: Load designs for skeleton sizing +++
            $desktop_design = $this->data['flight_ticket']['design'] ?? [];
            $mobile_design = $this->data['flight_ticket']['design_mobile'] ?? $desktop_design;

            $desktop_height = esc_attr($desktop_design['minHeight'] ?? 150) . 'px';
            $desktop_radius = esc_attr($desktop_design['borderRadius'] ?? 16) . 'px';

            $mobile_height = esc_attr($mobile_design['minHeight'] ?? 70) . 'px';
            $mobile_radius = esc_attr($mobile_design['borderRadius'] ?? 8) . 'px';
            // +++ END: Load designs for skeleton sizing +++

            ob_start();
            ?>
            
            <div id="<?php echo esc_attr($placeholder_id); ?>" class="yab-flight-ticket-placeholder yab-skeleton-loader" style="width: 100%; position: relative;">
                
                <div class="yab-ft-skeleton-desktop" style=" flex-direction:row; align-items:center; justify-content:space-between; width: 100%; height: <?php echo $desktop_height; ?>; background-color: #f0f0f0; border-radius: 16px; margin: 0; padding: <?php echo esc_attr($desktop_design['padding'] ?? 12); ?>px; box-sizing: border-box;">
                    <div style="flex-grow: 1; height: 90%; background-color: #e0e0e0; border-radius:10px; margin-right: 20px;" class="yab-skeleton-inner"></div>
                    <div style="width:352px; height:129px; background-color: #e0e0e0; border-radius:10px; flex-shrink: 0;" class="yab-skeleton-inner"></div>
                </div>

                <div class="yab-ft-skeleton-mobile" style="flex-direction:row; align-items:center; justify-content:space-between;padding:0 5px; width: 100%; height: <?php echo $mobile_height; ?>; background-color: #f0f0f0; border-radius:8px; margin: 0;  box-sizing: border-box;">
                    <div style="width:40%; height: 80%; background-color: #e0e0e0; border-radius:8px; margin-right: 10px;" class="yab-skeleton-inner"></div>
                    
                    <div style="width:30%; height:60px; background-color: #e0e0e0; border-radius:8px; flex-shrink: 0;" class="yab-skeleton-inner"></div>
                    </div>

            </div>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const placeholder_<?php echo $banner_id; ?> = document.getElementById('<?php echo esc_js($placeholder_id); ?>');
                if (!placeholder_<?php echo $banner_id; ?>) return;

                // FIX: Calculate tomorrow's date using the client's local time
                const today = new Date();
                const tomorrow = new Date(today);
                tomorrow.setDate(today.getDate() + 1);

                const yyyy = tomorrow.getFullYear();
                const mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
                const dd = String(tomorrow.getDate()).padStart(2, '0');
                const tomorrowDateString = `${yyyy}-${mm}-${dd}`;
                // END FIX
                
                // +++ START: Add is_mobile check +++
                const isMobileClient = window.innerWidth <= 768;
                // +++ END: Add is_mobile check +++

                // درخواست به اکشن AJAX عمومی که قیمت به‌روز را محاسبه می‌کند
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        'action': 'yab_render_flight_ticket_ssr', // اکشن AJAX عمومی جدید
                        'banner_id': '<?php echo $banner_id; ?>',
                        'local_departure_date': tomorrowDateString, // FIX: Pass client's tomorrow date
                        'is_mobile': isMobileClient ? '1' : '0' // +++ ADDED: Send client type +++
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
                /* Skeleton Responsive Styles */
                .yab-flight-ticket-placeholder .yab-ft-skeleton-mobile { display: none; }
                .yab-flight-ticket-placeholder .yab-ft-skeleton-desktop { display: flex; }
                .yab-flight-ticket-placeholder{border-radius:16px}
                @media (max-width: 768px) {
                    .yab-flight-ticket-placeholder .yab-ft-skeleton-desktop { display: none; }
                    .yab-flight-ticket-placeholder .yab-ft-skeleton-mobile { display: flex; }
                    .yab-flight-ticket-placeholder{border-radius:8px}
                }
                /* Skeleton Animation */
                .yab-flight-ticket-placeholder.yab-skeleton-loader {
                    position: relative; 
                    overflow: hidden; 
                    background-color: #f0f0f0;
                }
                .yab-flight-ticket-placeholder.yab-skeleton-loader .yab-skeleton-inner {
                     position: relative;
                     overflow: hidden;
                     background-color: #e0e0e0;
                }
                .yab-flight-ticket-placeholder.yab-skeleton-loader .yab-skeleton-inner::before {
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