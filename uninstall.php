<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       www.streamline.lv
 * @since      1.0
 *
 * @package    Printify_Print_Providers
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

//Deletes database table and all of it's data on uninstall
global $wpdb;
$table_name = $wpdb->prefix . "printify_order_ids";
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

//Deleting custom Printify product
wp_delete_post( get_option('printify_print_providers_custom_product_id'), true );

//Removing Custom options created with the plugin
delete_option( 'printify_print_providers_api_key' );
delete_option( 'printify_print_providers_statuses' );
delete_option( 'printify_print_providers_logging' );
delete_option( 'printify_print_providers_version_number' );
delete_option( 'printify_print_providers_custom_product_id' );
delete_option( 'printify_print_providers_custom_product_image' );