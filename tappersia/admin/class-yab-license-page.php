<?php
// tappersia/admin/class-yab-license-page.php
defined('ABSPATH') || exit;

class Yab_License_Page {

    private $license_manager;
    private $error_message = '';
    private $success_message = '';

    public function __construct(Yab_License_Manager $license_manager) {
        $this->license_manager = $license_manager;
    }

    /**
     * این متد اکنون روی هوک 'admin_init' اجرا می‌شود.
     * پردازش فرم، اعتبارسنجی و ریدایرکت را قبل از ارسال هدرها انجام می‌دهد.
     */
    public function process_activation_submission() {
        // 1. بررسی اینکه آیا فرم ما ارسال شده است؟
        if (!isset($_POST['yab_activate_license'])) {
            return; // فرم ما نیست، خارج شو
        }

        // 2. بررسی امنیتی Nonce
        if (!isset($_POST['yab_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['yab_nonce']), 'yab_activate_license_nonce')) {
            wp_die('Security check failed. Please try again.');
        }

        // 3. بررسی دسترسی کاربر
        if (!current_user_can('manage_options')) {
            wp_die('You do not have permission to do this.');
        }

        // 4. بررسی خالی نبودن کلید
        if (empty($_POST['yab_license_key'])) {
            // ریدایرکت به صفحه فعال‌سازی با پیام خطا
            wp_safe_redirect(admin_url('admin.php?page=tappersia-activate&message=error&code=empty_key'));
            exit;
        }

        // 5. اعتبارسنجی کلید
        $api_key = sanitize_text_field(trim($_POST['yab_license_key']));
        $result = $this->license_manager->activate_license($api_key);

        if ($result['success']) {
            // 6. موفقیت: ریدایرکت به صفحه اصلی پلاگین
            // خطای "headers already sent" دیگر رخ نخواهد داد
            wp_safe_redirect(admin_url('admin.php?page=tappersia'));
            exit;
        } else {
            // 7. شکست: ریدایرکت به صفحه فعال‌سازی با پیام خطا
            $error_message = urlencode($result['message']);
            wp_safe_redirect(admin_url('admin.php?page=tappersia-activate&message=error&code=validation_failed&error_msg=' . $error_message));
            exit;
        }
    }

    /**
     * این متد اکنون *فقط* HTML صفحه را رندر می‌کند.
     * تمام منطق POST به متد process_activation_submission منتقل شده است.
     */
    public function render_page() {
        
        // بررسی پیام‌های ریدایرکت شده
        if (isset($_GET['message'])) {
            if ($_GET['message'] === 'deactivated') {
                $this->success_message = 'License deactivated successfully.';
            }
            if ($_GET['message'] === 'invalid') {
                $this->error_message = 'Your license is invalid or has expired. Please reactivate.';
            }
            if ($_GET['message'] === 'error') {
                if (isset($_GET['error_msg'])) {
                    // پیام خطای API را از URL بخوان
                    $this->error_message = sanitize_text_field(urldecode($_GET['error_msg']));
                } elseif (isset($_GET['code']) && $_GET['code'] === 'empty_key') {
                    $this->error_message = 'Please enter an API key.';
                } else {
                    $this->error_message = 'An unknown activation error occurred.';
                }
            }
        }

        // Enqueue the specific styles for this page
        wp_enqueue_style(
            'yab-license-style', 
            YAB_PLUGIN_URL . 'assets/css/yab-license-style.css', 
            [], 
            YAB_VERSION
        );

        // Include the view file
        require_once YAB_PLUGIN_DIR . 'admin/views/view-license-activation.php';
    }
}