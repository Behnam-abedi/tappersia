<?php
// tappersia/admin/views/view-license-activation.php
defined('ABSPATH') || exit;

// This view is included from Yab_License_Page::render_page()
// $this->error_message and $this->success_message are available.

// *** اصلاح شده: تعریف متغیرها باید در بالای فایل باشد ***
$plugin_url = defined('YAB_PLUGIN_URL') ? YAB_PLUGIN_URL : plugins_url('tappersia/') . 'tappersia/'; 
$logo_url = $plugin_url . 'assets/image/logo.png'; 
?>
<div classid="yab-activation-wrapper" class="yab-activation-wrap">
    <div class="yab-activation-box">
        <div class="yab-logo-wrapper">            
            <img src="<?php echo esc_url($logo_url); ?>" alt="Tappersia Logo">
            <h1>Welcome to Tappersia</h1>
        </div>
        
        <p class="yab-prompt-text">Please enter your API Key to activate the plugin and unlock all features.</p>

        <?php if (!empty($this->error_message)) : ?>
            <div class="yab-notice yab-notice-error">
                <p><?php echo esc_html($this->error_message); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($this->success_message)) : ?>
            <div class="yab-notice yab-notice-success">
                <p><?php echo esc_html($this->success_message); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="yab-activation-form" novalidate="novalidate">
            <?php wp_nonce_field('yab_activate_license_nonce', 'yab_nonce'); ?>
            
            <div class="yab-form-field">
                <label for="yab_license_key">API Key</label>
                <input type="text" id="yab_license_key" name="yab_license_key" placeholder="Enter your API key" required>
            </div>
            
            <button type="submit" name="yab_activate_license" class="yab-activate-button">
                Activate License
            </button>
        </form>

    </div>
</div>