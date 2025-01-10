<?php
/**
 * Plugin Name: TSWP Manage Web Clients
 * Plugin URI: 
 * Description: A WordPress plugin to manage web development agency clients, their domains, applications, emails, and payments.
 * Version: 1.0.0
 * Author: Mauricio Cárdenas
 * Author URI: https://mauriciojc.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: tswp-manage-web-clients
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Plugin version
define('TSWP_VERSION', '1.0.0');
define('TSWP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TSWP_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_tswp_manage_web_clients() {
    require_once TSWP_PLUGIN_DIR . 'includes/class-tswp-activator.php';
    TSWP_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_tswp_manage_web_clients() {
    require_once TSWP_PLUGIN_DIR . 'includes/class-tswp-deactivator.php';
    TSWP_Deactivator::deactivate();
}

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'activate_tswp_manage_web_clients');
register_deactivation_hook(__FILE__, 'deactivate_tswp_manage_web_clients');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require TSWP_PLUGIN_DIR . 'includes/class-tswp-manager.php';

/**
 * Begins execution of the plugin.
 */
function run_tswp_manage_web_clients() {
    $plugin = new TSWP_Manager();
    $plugin->run();
}

// Run the plugin
run_tswp_manage_web_clients();