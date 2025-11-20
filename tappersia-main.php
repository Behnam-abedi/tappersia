<?php
/**
 * Plugin Name:       Tappersia
 * Plugin URI:        https://www.tappersia.com
 * Description:       A modern banner management plugin with a custom UI using Vue.js and Tailwind CSS.
 * Version:           1.0.3
 * Author:            Behnam Abedi
 * Author URI:        abd.behnam@gmail.com
 * GitHub Plugin URI: Behnam-abedi/tappersia
 * GitHub Branch:     main
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tappersia
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Define plugin constants
define( 'YAB_VERSION', '1.0.3' );
define( 'YAB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'YAB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_yab() {
    require_once YAB_PLUGIN_DIR . 'includes/class-activator.php';
    Yab_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_yab() {
    require_once YAB_PLUGIN_DIR . 'includes/class-deactivator.php';
    Yab_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_yab' );
register_deactivation_hook( __FILE__, 'deactivate_yab' );

/**
 * The core plugin class.
 */
require_once YAB_PLUGIN_DIR . 'includes/class-main.php';

/**
 * Begins execution of the plugin.
 */
function run_yab() {
    // Pass the main plugin file path to the constructor
    $plugin = new Yab_Main( __FILE__ );
    $plugin->run();
}

run_yab();