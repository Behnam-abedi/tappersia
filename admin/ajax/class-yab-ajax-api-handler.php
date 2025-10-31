<?php
// tappersia/admin/ajax/class-yab-ajax-api-handler.php

if (!class_exists('Yab_Ajax_Api_Handler')) {

    // 1. بارگذاری فایل‌های Trait
    require_once YAB_PLUGIN_DIR . 'admin/ajax/traits/trait-yab-ajax-hotel-handler.php';
    require_once YAB_PLUGIN_DIR . 'admin/ajax/traits/trait-yab-ajax-tour-handler.php';
    require_once YAB_PLUGIN_DIR . 'admin/ajax/traits/trait-yab-ajax-flight-handler.php';
    require_once YAB_PLUGIN_DIR . 'admin/ajax/traits/trait-yab-ajax-welcome-package-handler.php';

    class Yab_Ajax_Api_Handler {

        // 2. استفاده از Traitها
        // تمام متدهای عمومی (public) و خصوصی (private) از این فایل‌ها
        // به این کلاس اضافه می‌شوند.
        use Yab_Ajax_Hotel_Handler,
            Yab_Ajax_Tour_Handler,
            Yab_Ajax_Flight_Handler,
            Yab_Ajax_Welcome_Package_Handler;

        // کلید API در کلاس اصلی باقی می‌ماند و در تمام Traitها
        // از طریق $this->api_key قابل دسترسی است.
        private $api_key = '0963b596-1f23-4188-b46c-d7d671028940';

        /**
         * متد ثبت هوک‌ها بدون تغییر باقی می‌ماند.
         * وردپرس متدهای مورد نیاز را از Traitها پیدا خواهد کرد.
         */
        public function register_hooks() {
            // --- هوک‌های هتل (از Trait هتل) ---
            add_action('wp_ajax_yab_fetch_hotels_from_api', [$this, 'fetch_hotels_from_api']);
            add_action('wp_ajax_yab_fetch_cities_from_api', [$this, 'fetch_cities_from_api']);
            add_action('wp_ajax_yab_fetch_hotel_details_from_api', [$this, 'fetch_hotel_details_from_api']);
            add_action('wp_ajax_yab_fetch_hotel_details_by_ids', [$this, 'fetch_hotel_details_by_ids']);
            add_action('wp_ajax_nopriv_yab_fetch_hotel_details_by_ids', [$this, 'fetch_hotel_details_by_ids']);

            // --- هوک‌های تور (از Trait تور) ---
            add_action('wp_ajax_yab_fetch_tours_from_api', [$this, 'fetch_tours_from_api']);
            add_action('wp_ajax_yab_fetch_tour_cities_from_api', [$this, 'fetch_tour_cities_from_api']);
            add_action('wp_ajax_yab_fetch_tour_details_from_api', [$this, 'fetch_tour_details_from_api']);
            add_action('wp_ajax_yab_fetch_tour_details_by_ids', [$this, 'fetch_tour_details_by_ids']);
            add_action('wp_ajax_nopriv_yab_fetch_tour_details_by_ids', [$this, 'fetch_tour_details_by_ids']);

            // --- هوک‌های بلیت پرواز (از Trait پرواز) ---
            add_action('wp_ajax_yab_fetch_airports_from_api', [$this, 'fetch_airports_from_api']);
            add_action('wp_ajax_yab_fetch_flight_search', [$this, 'fetch_flight_search']);
            add_action('wp_ajax_nopriv_yab_render_flight_ticket_ssr', [$this, 'render_flight_ticket_ssr']);
            add_action('wp_ajax_yab_render_flight_ticket_ssr', [$this, 'render_flight_ticket_ssr']);
            
            // --- هوک‌های پکیج خوش‌آمدگویی (از Trait پکیج) ---
            add_action('wp_ajax_yab_fetch_welcome_packages', [$this, 'fetch_welcome_packages']);
            add_action('wp_ajax_nopriv_yab_render_welcome_package_ssr', [$this, 'render_welcome_package_ssr']);
            add_action('wp_ajax_yab_render_welcome_package_ssr', [$this, 'render_welcome_package_ssr']);

            // --- هوک‌های عمومی / مشترک (در همین کلاس باقی می‌ماند) ---
            add_action('wp_ajax_nopriv_yab_fetch_api_banner_html', [$this, 'fetch_api_banner_html']);
            add_action('wp_ajax_yab_fetch_api_banner_html', [$this, 'fetch_api_banner_html']);
        }

        /**
         * این متد عمومی رندر بنر API (هتل/تور) است و
         * چون به هیچ گروه خاصی تعلق ندارد، در کلاس اصلی می‌ماند.
         */
        public function fetch_api_banner_html() {
            if (empty($_POST['banner_id']) || !is_numeric($_POST['banner_id'])) {
                wp_send_json_error(['message' => 'Invalid Banner ID.'], 400);
                return;
            }

            $banner_id = intval($_POST['banner_id']);
            $banner_post = get_post($banner_id);

            if (!$banner_post || $banner_post->post_type !== 'yab_banner' || $banner_post->post_status !== 'publish') {
                wp_send_json_error(['message' => 'Banner not found or not active.'], 404);
                return;
            }

            $data = get_post_meta($banner_id, '_yab_banner_data', true);
            $banner_type_meta = get_post_meta($banner_id, '_yab_banner_type', true);

            // Ensure it's an API banner before proceeding
            if ($banner_type_meta !== 'api-banner' || empty($data) || empty($data['api'])) {
                wp_send_json_error(['message' => 'Banner data is incomplete or invalid type.'], 500);
                return;
            }

            require_once YAB_PLUGIN_DIR . 'public/Renderers/class-api-banner-renderer.php';
            $renderer = new Yab_Api_Banner_Renderer($data, $banner_id);
            $html = $renderer->render_live_html();

            wp_send_json_success(['html' => $html]);
            wp_die();
        }

        // تمام متدهای دیگر به فایل‌های Trait منتقل شده‌اند
    }
}
?>