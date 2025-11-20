<?php
// tappersia/includes/license/class-yab-api-client.php
defined('ABSPATH') || exit;

class Yab_Api_Client {

    /**
     * ما از این اندپوینت برای تست کردن کلید API استفاده می‌کنیم.
     */
    private $validation_url = 'https://b2bapi.tapexplore.com/api/variable/airports';

    /**
     * یک تماس آزمایشی برای اعتبارسنجی کلید API مشتری ارسال می‌کند.
     * @param string $api_key (کلید API که مشتری وارد کرده)
     * @return array|WP_Error
     */
    public function send_validation_request($api_key) {
        
        $headers = [
            'api-key' => $api_key, // استفاده از کلید مشتری در هدر
            'Content-Type' => 'application/json'
        ];

        // این یک تماس GET است
        $response = wp_remote_get($this->validation_url, [
            'method' => 'GET',
            'timeout' => 15,
            'headers' => $headers,
            'sslverify' => true
        ]);

        return $this->handle_response($response); // این تابع اکنون اصلاح شده است
    }

    /**
     * پاسخ سرور b2bapi.tapexplore.com را بررسی می‌کند.
     * *** این تابع اصلاح شده است تا محتوای JSON را نیز بررسی کند ***
     * @param array|WP_Error $response
     * @return array|WP_Error
     */
    private function handle_response($response) {
        if (is_wp_error($response)) {
            // خطای اتصال (مثل خطای DNS یا cURL)
            return new WP_Error('api_connection_error', 'API Connection Error: ' . $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // --- START: منطق اعتبارسنجی اصلاح شده ---

        if ($response_code === 200) {
            // سرور پاسخ 200 OK داد. حالا باید *محتوای* پاسخ را بررسی کنیم.
            // بر اساس داکیومنت شما، پاسخ موفق باید "success": true داشته باشد.
            
            if (isset($data['success']) && $data['success'] === true) {
                // موفقیت کامل! کلید معتبر است.
                // پاسخی که Yab_License_Validator انتظار دارد را برمی‌گردانیم
                return ['success' => true, 'valid' => true, 'message' => 'API Key validation successful.'];
            } else {
                // پاسخ 200 OK بود، اما "success" نبود (مثلاً "success": false)
                // این به احتمال زیاد به معنای یک کلید نامعتبر است که سرور آن را تشخیص داده.
                $message = $data['message'] ?? 'API call returned OK, but the response was not successful (e.g., success: false).';
                return new WP_Error('api_validation_failed', 'Validation Failed: ' . $message);
            }
        }

        if ($response_code === 401 || $response_code === 403) {
            // خطا! کلید نامعتبر است (خطای HTTP).
            $message = $data['message'] ?? 'Invalid API Key. The server rejected the key (401/403).';
            return new WP_Error('api_auth_error', 'Validation Failed: ' . $message);
        }

        // سایر خطاهای سرور (مثل 500، 404 و غیره)
        $message = $data['message'] ?? 'An unknown error occurred.';
        return new WP_Error('api_server_error', 'API Server Error (' . $response_code . '): ' . $message);
        
        // --- END: منطق اعتبارسنجی اصلاح شده ---
    }
}