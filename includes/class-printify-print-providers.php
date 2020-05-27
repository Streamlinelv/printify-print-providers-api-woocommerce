<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       www.streamline.lv
 * @since      1.0
 *
 * @package    Printify_Print_Providers
 * @subpackage Printify_Print_Providers/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0
 * @package    Printify_Print_Providers
 * @subpackage Printify_Print_Providers/includes
 * @author     Streamline.lv <info@streamline.lv>
 */
class Printify_Print_Providers {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      Printify_Print_Providers_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0
	 */
	public function __construct() {
		if ( defined( 'PRINTIFY_PRINT_PROVIDERS_VERSION' ) ) {
			$this->version = PRINTIFY_PRINT_PROVIDERS_VERSION;
		} else {
			$this->version = '1.0';
		}
		$this->plugin_name = 'printify-print-providers';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Printify_Print_Providers_Loader. Orchestrates the hooks of the plugin.
	 * - Printify_Print_Providers_i18n. Defines internationalization functionality.
	 * - Printify_Print_Providers_Admin. Defines all hooks for the admin area.
	 * - Printify_Print_Providers_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-printify-print-providers-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-printify-print-providers-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-printify-print-providers-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-printify-print-providers-public.php';

		$this->loader = new Printify_Print_Providers_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Printify_Print_Providers_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Printify_Print_Providers_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Printify_Print_Providers_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin, 'create_custom_api_routes' );
		$this->loader->add_filter( 'woocommerce_get_sections_advanced', $plugin_admin, 'add_printify_section' );
		$this->loader->add_filter( 'woocommerce_get_settings_advanced', $plugin_admin, 'add_printify_settings', 10, 2 );
		$this->loader->add_filter( 'plugin_action_links_' . PRINTIFY_PRINT_PROVIDERS_BASENAME, $plugin_admin, 'add_settings_link' );
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'update_database' ); //Updating database if necessary
		$this->loader->add_action( 'woocommerce_after_order_itemmeta', $plugin_admin, 'add_custom_ordered_product_fields', 3, 20 ); 
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_custom_ordered_product_fields', 3, 20 ); 
		$this->loader->add_action( 'before_delete_post', $plugin_admin, 'delete_printify_order' );
		$this->loader->add_filter( 'manage_edit-shop_order_columns', $plugin_admin, 'add_printify_column', 20 );
		$this->loader->add_action( 'manage_posts_custom_column', $plugin_admin, 'insert_data_in_printify_column' );
		$this->loader->add_action( 'woocommerce_product_options_pricing', $plugin_admin, 'add_simple_product_pricing_fields');
		$this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'save_simple_product_pricing_fields');
		$this->loader->add_action( 'woocommerce_variation_options_pricing', $plugin_admin, 'add_variable_product_pricing_fields', 10, 3);
		$this->loader->add_action( 'woocommerce_save_product_variation', $plugin_admin, 'save_variable_product_pricing_field_data', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Printify_Print_Providers_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0
	 * @return    Printify_Print_Providers_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
