<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.streamline.lv
 * @since             1.0
 * @package           Printify_Print_Providers
 *
 * @wordpress-plugin
 * Plugin Name:       Printify for Print Providers
 * Plugin URI:        https://wordpress.org/plugins/printify-print-providers
 * Description:       Integrate your Printify orders with WooCommerce using API.
 * Version:           1.0
 * Author:            Streamline.lv
 * Author URI:        www.streamline.lv
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       printify-print-providers
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if (!defined('PRINTIFY_PRINT_PROVIDERS_VERSION')) define( 'PRINTIFY_PRINT_PROVIDERS_VERSION', '1.0');
if (!defined('PRINTIFY_PRINT_PROVIDERS_PLUGIN_NAME')) define( 'PRINTIFY_PRINT_PROVIDERS_PLUGIN_NAME', 'Printify for Print Providers');
if (!defined('PRINTIFY_PRINT_PROVIDERS')) define( 'PRINTIFY_PRINT_PROVIDERS', 'printify-print-providers');
if (!defined('PRINTIFY_PRINT_PROVIDERS_SLUG')) define( 'PRINTIFY_PRINT_PROVIDERS_SLUG', 'printify_print_providers');
if (!defined('PRINTIFY_PRINT_PROVIDERS_TABLE_NAME')) define( 'PRINTIFY_PRINT_PROVIDERS_TABLE_NAME', 'printify_order_ids');
if (!defined('PRINTIFY_PRINT_PROVIDERS_BASENAME')) define( 'PRINTIFY_PRINT_PROVIDERS_BASENAME', plugin_basename( __FILE__ ));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-printify-print-providers-activator.php
 */
function activate_printify_print_providers() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-printify-print-providers-activator.php';
	Printify_Print_Providers_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-printify-print-providers-deactivator.php
 */
function deactivate_printify_print_providers() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-printify-print-providers-deactivator.php';
	Printify_Print_Providers_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_printify_print_providers' );
register_deactivation_hook( __FILE__, 'deactivate_printify_print_providers' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-printify-print-providers.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0
 */
function run_printify_print_providers() {

	$plugin = new Printify_Print_Providers();
	$plugin->run();

}
run_printify_print_providers();
