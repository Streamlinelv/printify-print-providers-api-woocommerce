<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0
 * @package    Printify_Print_Providers
 * @subpackage Printify_Print_Providers/includes
 * @author     Streamline.lv <info@streamline.lv>
 */
class Printify_Print_Providers_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0
	 */
	public static function activate() {
		
		/**
		* Creating table
		*/
		global $wpdb, $table_name;
		
		$table_name = $wpdb->prefix . PRINTIFY_PRINT_PROVIDERS_TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			printify_order_id VARCHAR(35) NOT NULL,
			woocommerce_order_id BIGINT(25) NOT NULL,
			PRIMARY KEY  (printify_order_id)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql );
		
		/**
		* Resets table Auto increment index to 1
		*/
		$sql = "ALTER TABLE $table_name AUTO_INCREMENT = 1";
		dbDelta( $sql );

		//Generating API key if it hasn't been already created
		add_option('printify_print_providers_api_key', implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6)));
		update_option('printify_print_providers_statuses', array('Created','Picked','Printed','Packaged','Reprint','X-Updates','On hold','Shipped','Canceled'));

		$plugin_admin = new Printify_Print_Providers_Admin(PRINTIFY_PRINT_PROVIDERS_SLUG, PRINTIFY_PRINT_PROVIDERS_VERSION);
		$plugin_admin->create_custom_printify_product();
	}
}