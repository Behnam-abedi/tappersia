<?php
// tappersia/admin/views/view-license-settings.php
defined('ABSPATH') || exit;

// دریافت اطلاعات لایسنس
if (!class_exists('Yab_License_Manager')) {
    require_once YAB_PLUGIN_DIR . 'includes/license/class-yab-license-manager.php';
}
$license_manager = new Yab_License_Manager();
$license_key = $license_manager->get_api_key();
$license_status = $license_manager->get_license_status();

// ماسک کردن کلید برای نمایش
$masked_key = '****************' . esc_html(substr($license_key, -4));
$status_class = $license_status['status'] === 'valid' ? 'yab-status-valid' : 'yab-status-invalid';
$status_text = $license_status['status'] === 'valid' ? 'Active' : 'Invalid';

// دریافت ورژن فعلی پلاگین
$current_version = defined('YAB_VERSION') ? YAB_VERSION : '1.0.0';
?>
<div class="wrap yab-license-settings-wrap">
    <h1>Tappersia License & Updates</h1>

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
        <h2 class="yab-box-title">Plugin Update</h2>
        
        <div class="yab-update-row">
            <span class="yab-label">Current Version:</span>
            <span class="yab-version-badge"><?php echo esc_html($current_version); ?></span>
        </div>

        <div id="yab-auto-check-status" class="yab-update-status-area">
            <div class="yab-checking-state">
                <span class="spinner is-active" style="float: none; margin: 0 5px 0 0;"></span>
                Checking for updates...
            </div>
        </div>

        <div id="yab-update-messages" style="display:none; margin-top: 15px;"></div>
    </div>
</div>

<style>
/* استایل‌های صفحه لایسنس */
.yab-license-settings-wrap {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    padding: 20px;
    min-height: 80vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    box-sizing: border-box; 
}
.yab-license-box {
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 0 20px 20px 20px;
    max-width: 700px;
    width: 100%; 
    margin-top: 20px;
    border-radius: 8px;
    box-sizing: border-box; 
}
.yab-box-title {
    font-size: 18px;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
    margin-bottom: 20px;
    margin-top: 0;
}
.yab-status-indicator {
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 4px;
    color: #fff;
    text-transform: uppercase;
    font-size: 12px;
}
.yab-status-valid { background-color: #28a745; }
.yab-status-invalid { background-color: #dc3545; }
.yab-api-key-display {
    background: #f0f0f1;
    padding: 4px 8px;
    border-radius: 4px;
    font-family: monospace;
    color: #555;
}
.yab-deactivate-button {
    color: #dc3545 !important;
    border-color: #dc3545 !important;
}
.yab-deactivate-button:hover {
    background: #dc3545 !important;
    color: #fff !important;
    border-color: #dc3545 !important;
}

/* استایل بخش آپدیت */
.yab-update-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    font-size: 14px;
}
.yab-label { font-weight: 600; color: #333; }
.yab-version-badge {
    background: #e5e7eb;
    color: #374151;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}
.yab-update-status-area {
    padding: 15px;
    background: #f9f9f9;
    border-radius: 6px;
    border: 1px dashed #ccc;
    text-align: center;
}
.yab-checking-state {
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
}
.yab-update-available {
    color: #2271b1;
    font-weight: 500;
}
.yab-up-to-date {
    color: #28a745;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

/* نوتیفیکیشن‌ها */
.yab-notice {
    padding: 12px;
    border-radius: 4px;
    margin-top: 10px;
    border-left: 4px solid;
    text-align: left;
}
.yab-notice-success { background: #f0f9eb; border-color: #28a745; color: #155724; }
.yab-notice-error { background: #fef2f2; border-color: #dc3545; color: #991b1b; }
</style>

<script type="text/javascript">
jQuery(document).ready(function($) {
    var statusArea = $('#yab-auto-check-status');
    var messageBox = $('#yab-update-messages');
    var updateNonce = '<?php echo wp_create_nonce('yab_update_nonce'); ?>';

    // اجرای خودکار هنگام لود شدن صفحه
    performAutoCheck();

    function performAutoCheck() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'yab_ajax_check_for_updates',
                nonce: updateNonce
            },
            success: function(response) {
                if (response.success) {
                    if (response.data.update_available) {
                        // آپدیت موجود است
                        var newVersion = response.data.new_version;
                        statusArea.html(
                            '<div class="yab-update-available">' +
                                '<span class="dashicons dashicons-warning" style="color: #f0b849; vertical-align: middle;"></span> ' +
                                '<strong>New version available: v' + newVersion + '</strong>' +
                                '<br><br>' +
                                '<button type="button" class="button button-primary" id="yab-install-update-btn">Update Now</button>' +
                            '</div>'
                        );
                    } else {
                        // آپدیتی نیست
                        statusArea.html(
                            '<div class="yab-up-to-date">' +
                                '<span class="dashicons dashicons-yes-alt"></span> ' +
                                'You are using the latest version.' +
                            '</div>'
                        );
                    }
                } else {
                    // خطا در پاسخ سرور
                    statusArea.html('<span style="color:red;">Error checking for updates: ' + response.data.message + '</span>');
                }
            },
            error: function() {
                statusArea.html('<span style="color:red;">Connection error while checking for updates.</span>');
            }
        });
    }

    // هندلر دکمه نصب آپدیت (که به صورت داینامیک اضافه شده)
    $(document).on('click', '#yab-install-update-btn', function(e) {
        e.preventDefault();
        var $button = $(this);
        
        $button.prop('disabled', true).text('Installing...');
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
                    showMessage(response.data.message, 'success');
                    if (response.data.reload) {
                        setTimeout(function() { location.reload(); }, 2000);
                    }
                } else {
                    showMessage(response.data.message, 'error');
                    $button.prop('disabled', false).text('Retry Update');
                }
            },
            error: function() {
                showMessage('Installation failed due to a network error.', 'error');
                $button.prop('disabled', false).text('Retry Update');
            }
        });
    });

    function showMessage(message, type) {
        var className = type === 'success' ? 'yab-notice-success' : 'yab-notice-error';
        messageBox.html('<div class="yab-notice ' + className + '"><p>' + message + '</p></div>');
        messageBox.slideDown();
    }
});
</script>