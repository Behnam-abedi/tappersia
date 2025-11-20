<?php
// /includes/updater/class-yab-silent-upgrader-skin.php
defined('ABSPATH') || exit;

// این کلاس برای اجرای آپدیت در پس‌زمینه (AJAX) ضروری است
// بدون اینکه خروجی HTML ناخواسته ایجاد کند.
if ( ! class_exists( 'WP_Upgrader_Skin' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
}

class Yab_Silent_Upgrader_Skin extends WP_Upgrader_Skin {
    public $errors = [];

    // این متدها را خالی می‌گذاریم تا هیچ خروجی HTML چاپ نکنند
    public function header() {}
    public function footer() {}
    public function feedback( $string, ...$args ) {}
    public function before( $title = '' ) {}
    public function after( $title = '' ) {}

    /**
     * خطاها را به جای چاپ کردن، ذخیره می‌کند
     */
    public function error( $errors ) {
        if ( is_wp_error( $errors ) ) {
            $this->errors = array_merge( $this->errors, $errors->get_error_messages() );
        } elseif ( is_string( $errors ) ) {
            $this->errors[] = $errors;
        }
    }

    /**
     * یک متد کمکی برای دریافت خطاها
     */
    public function get_errors() {
        return $this->errors;
    }
}