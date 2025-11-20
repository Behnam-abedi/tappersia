<?php
// tappersia/admin/views/view-license-settings.php
defined('ABSPATH') || exit;

// We need an instance of the manager to get data
if (!class_exists('Yab_License_Manager')) {
    require_once YAB_PLUGIN_DIR . 'includes/license/class-yab-license-manager.php';
}
$license_manager = new Yab_License_Manager();
$license_key = $license_manager->get_api_key();
$license_status = $license_manager->get_license_status();

// Mask the API key for display
$masked_key = '****************' . esc_html(substr($license_key, -4));
$status_class = $license_status['status'] === 'valid' ? 'yab-status-valid' : 'yab-status-invalid';
$status_text = $license_status['status'] === 'valid' ? 'Active' : 'Invalid';

// پیام آپدیت قبلی حذف شد، چون ای‌جکس شد
?>
<div class="wrap yab-license-settings-wrap">
    <h1>Tappersia License Settings</h1>

    <div class="yab-license-box">
        <h2 class="yab-box-title">License Status</h2>

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">License Status</th>
                    <td>
                        <span class="yab-status-indicator <?php echo esc_attr($status_class); ?>">
                            <?php echo esc_html(ucfirst($status_text)); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">API Key</th>
                    <td>
                        <code class="yab-api-key-display"><?php echo esc_html($masked_key); ?></code>
                    </td>
                </tr>
                <?php if (!empty($license_status['expires_at'])) : ?>
                <tr>
                    <th scope="row">Expires On</th>
                    <td>
                        <?php echo esc_html(date('F j, Y', strtotime($license_status['expires_at']))); ?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th scope="row">Manage License</th>
                    <td>
                        <p class="description">
                            To update your API key, you must first deactivate the current license.
                        </p>
                        <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top: 15px;">
                            <input type="hidden" name="action" value="yab_deactivate_license">
                            <?php wp_nonce_field('yab_deactivate_license_nonce', 'yab_nonce'); ?>
                            <button type="submit" class="button button-secondary yab-deactivate-button">
                                Deactivate License
                            </button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="yab-license-box">
        <h2 class="yab-box-title">Plugin Updates</h2>
        
        <div id="yab-updater-ui">
            <p class="description">
                Check for new versions of Tappersia from GitHub.
            </p>
            <button type="button" class="button button-primary" id="yab-check-update-btn" style="margin-top: 15px;">
                Check for Updates
            </button>
            <span class="spinner" style="float: none; vertical-align: middle; margin-left: 5px;"></span>
        </div>

        <div id="yab-update-messages" style="display:none; margin-top: 15px;"></div>
        
    </div>
    </div>

<style>
/* Minimal styles for the license settings page */
.yab-license-settings-wrap {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    padding: 20px;
    height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-sizing: border-box; 
}
.yab-license-box {
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 1px 12px 12px 12px;
    max-width: 700px;
    width: 100%; 
    margin-top: 20px;
    border-radius: 10px;
    box-sizing: border-box; 
}
.yab-box-title {
    font-size: 18px;
    padding: 8px 0;
    border-bottom: 1px solid #c3c4c7;
    margin-bottom: 20px;
}
.yab-status-indicator {
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 4px;
    color: #fff;
    text-transform: uppercase;
    font-size: 13px;
}
.yab-status-valid {
    background-color: #28a745;
}
.yab-status-invalid {
    background-color: #dc3545;
}
.yab-api-key-display {
    background: #f0f0f1;
    padding: 4px 8px;
    border-radius: 4px;
    font-family: monospace;
}
.yab-deactivate-button {
    color: #dc3545 !important;
    border-color: #dc3545 !important;
}
.yab-deactivate-button:hover {
    background: #dc3545 !important;
    color: #fff !important;
}

/* --- START: NEW/MODIFIED STYLES --- */
.yab-notice {
    padding: 15px;
    border-radius: 6px;
    margin-top: 20px;
    border-left: 4px solid;
    max-width: 700px;
    width: 100%;
    box-sizing: border-box;
}
.yab-notice-success {
    background: #fff;
    border-color: #4fe07f;
    color: #1a1a1a;
}
.yab-notice-error {
    background: #fff;
    border-color: #dc3545; /* قرمز برای خطا */
    color: #1a1a1a;
}
.yab-notice-info {
    background: #fff;
    border-color: #007cba; /* آبی برای اطلاعات */
    color: #1a1a1a;
}
.yab-notice p {
    margin: 0;
    padding: 0;
    font-weight: 500;
}
#yab-updater-ui .spinner {
    display: none; /* در ابتدا مخفی */
}
#yab-updater-ui.loading .spinner {
    display: inline-block; /* نمایش در زمان لودینگ */
}
/* --- END: NEW/MODIFIED STYLES --- */
</style>

<script type="text/javascript">
jQuery(document).ready(function($) {

    var uiContainer = $('#yab-updater-ui');
    var messageBox = $('#yab-update-messages');

    // Nonce برای امنیت ای‌جکس
    var updateNonce = '<?php echo wp_create_nonce('yab_update_nonce'); ?>';

    // 1. هندلر برای دکمه "Check for Updates"
    $(document).on('click', '#yab-check-update-btn', function(e) {
        e.preventDefault();
        var $button = $(this);
        
        uiContainer.addClass('loading');
        $button.prop('disabled', true);
        messageBox.hide().empty();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'yab_ajax_check_for_updates',
                nonce: updateNonce
            },
            success: function(response) {
                if (response.success) {
                    // موفقیت‌آمیز بود
                    showMessage(response.data.message, 'info');

                    if (response.data.update_available) {
                        // آپدیت موجود است، دکمه را عوض کن
                        $button.replaceWith(
                            '<button type="button" class="button button-primary" id="yab-install-update-btn" style="margin-top: 15px;">' +
                            'Install Update (v' + response.data.new_version + ')' +
                            '</button>'
                        );
                    } else {
                        // آپدیتی نیست، دکمه را فعال کن
                        $button.prop('disabled', false);
                    }
                } else {
                    // خطای سرور
                    showMessage(response.data.message, 'error');
                    $button.prop('disabled', false);
                }
            },
            error: function() {
                showMessage('An unknown error occurred.', 'error');
                $button.prop('disabled', false);
            },
            complete: function() {
                uiContainer.removeClass('loading');
            }
        });
    });

    // 2. هندلر برای دکمه "Install Update"
    // (از 'document' استفاده می‌کنیم چون این دکمه بعداً به صفحه اضافه می‌شود)
    $(document).on('click', '#yab-install-update-btn', function(e) {
        e.preventDefault();
        var $button = $(this);
        
        uiContainer.addClass('loading');
        $button.prop('disabled', true).text('Installing... Do not refresh.');
        messageBox.hide().empty();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'yab_ajax_install_update',
                nonce: updateNonce
            },
            success: function(response) {
                if (response.success) {
                    // نصب موفقیت‌آمیز بود
                    showMessage(response.data.message, 'success');
                    if (response.data.reload) {
                        // اگر سرور گفت، ریلود کن
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // 2 ثانیه صبر کن تا کاربر پیام را ببیند
                    }
                } else {
                    // خطای نصب
                    showMessage(response.data.message, 'error');
                    $button.prop('disabled', false).text('Install Failed. Try Again?');
                }
            },
            error: function() {
                showMessage('An unknown error occurred during installation.', 'error');
                $button.prop('disabled', false).text('Install Failed. Try Again?');
            },
            complete: function() {
                // لودینگ را حذف نکن تا کاربر رفرش کند
                // uiContainer.removeClass('loading'); 
            }
        });
    });

    // 3. تابع کمکی برای نمایش پیام
    function showMessage(message, type) {
        var className = 'yab-notice-info'; // default
        if (type === 'success') {
            className = 'yab-notice-success';
        } else if (type === 'error') {
            className = 'yab-notice-error';
        }
        
        messageBox.html('<div class="yab-notice ' + className + '"><p>' + message + '</p></div>');
        messageBox.slideDown();
    }

});
</script>