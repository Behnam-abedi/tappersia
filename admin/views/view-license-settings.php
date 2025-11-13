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
</div>

<style>
    /* Minimal styles for the license settings page */

    .yab-license-settings-wrap {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        padding: 20px;
        height:100vh;
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
    }
    .yab-license-box {
        background: #fff;
        border: 1px solid #c3c4c7;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
        padding: 1px 12px 12px 12px;
        max-width: 700px;
        margin-top: 20px;
        border-radius:10px
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
</style>