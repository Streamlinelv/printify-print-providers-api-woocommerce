<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.streamline.lv
 * @since      1.0
 *
 * @package    Printify_Print_Providers
 * @subpackage Printify_Print_Providers/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Printify_Print_Providers
 * @subpackage Printify_Print_Providers/admin
 * @author     Streamline.lv <info@streamline.lv>
 */
class Printify_Print_Providers_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Printify_Print_Providers_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Printify_Print_Providers_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/printify-print-providers-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Printify_Print_Providers_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Printify_Print_Providers_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/printify-print-providers-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Check if WooCommerce is activated
	 *
	 * @since    1.0
	 * @return boolean
	 */
	public function woocommerce_is_activated() {
		if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
	}

	/**
	 * Add Settings link near plugin activation link
	 *
	 * @since    1.0
	 * @return array
	 */
	public function add_settings_link( $links ) {
		if($this->woocommerce_is_activated()){
			$action_links = array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=advanced&section=' . PRINTIFY_PRINT_PROVIDERS_SLUG ) . '" aria-label="' . esc_attr__( 'View settings', 'printify-print-providers' ) . '">' . esc_html__( 'Settings', 'printify-print-providers' ) . '</a>',
			);
			return array_merge( $action_links, $links );
		}else{
			return $links;
		}
	}

	/**
	 * Function checks the current plugin version with the one saved in database in order to handle database update when plugin is activated since WordPress doesn't run the database update function automatically on plugin update
	 *
	 * @since    1.0
	 */
	function update_database(){
		if (PRINTIFY_PRINT_PROVIDERS_VERSION == get_option('printify_print_providers_version_number')){ //If database version is equal to plugin version. Not updating database
			return;
		}else{ //Versions are different and we must update the database
			update_option('printify_print_providers_version_number', PRINTIFY_PRINT_PROVIDERS_VERSION);
			activate_printify_print_providers(); //Function that updates the database
			return;
		}
	}

	/**
	 * Add a new section to WooCommerce > Settings > Advanced
	 *
	 * @since    1.0
	 */
	function add_printify_section( $sections ) {
		$sections[PRINTIFY_PRINT_PROVIDERS_SLUG] = __( 'Printify API', 'printify-print-providers' );
		return $sections;
	}

	/**
	 * Add Settings to the Printify section
	 *
	 * @since    1.0
	 */
	function add_printify_settings( $settings, $current_section ) {
		// Make sure we're looking only at our section
		if ( PRINTIFY_PRINT_PROVIDERS_SLUG === $current_section ) {

			$logging_enabled = get_option('printify_print_providers_logging');
			if($logging_enabled === 'yes'){
				$description = sprintf(__('Please go to <b>WooCommerce</b> > <b>Status</b> > <b>Logs</b> and from the dropdown list pick the file that starts with "%s" to view the log file.', 'printify-print-providers'), PRINTIFY_PRINT_PROVIDERS );
			}else{
				$description = false;
			}

			$printify_settings = array(
				array(
					'title'     => __( 'Printify API', 'printify-print-providers' ),
					'type'      => 'title',
					'id'     	=> 'printify-print-providers-description',
					'desc'		=> __( 'The API key can be used to authorize API requests from Printify to your store. <br/>You will need to send this key to your Printify representative to setup API.', 'printify-print-providers' )
				),

				array(
					'title'		=> __( 'Key', 'printify-print-providers' ),
					'type'		=> 'text',
					'id'		=> 'printify_print_providers_api_key',
					'custom_attributes'	=> array(
	                      'readonly'	=> true
	                )
				),

				array(
					'title'		=> __( 'Printify products', 'printify-print-providers' ),
					'type'		=> 'text',
					'id'		=> 'printify_print_providers_product_ids',
					'desc_tip' => __( 'Add a comma sepparated list of your product IDs that are linked to Printify. E.g. 1001,1002,3879,4991', 'printify-print-providers' )
				),

				array(
					'title'		=> __( 'Enable logging?', 'printify-print-providers' ),
					'type'		=> 'checkbox',
					'id'		=> 'printify_print_providers_logging',
					'desc'		=> $description
				),

				array(
					'type'  => 'sectionend'
				),
			);
			
			return $printify_settings;
			
		}else{
			// otherwise give us back the other settings
			return $settings;
		}
	}

	/**
	 * Outputs debugging information to WooCommerce log
	 *
	 * @since 1.0
	 */
	function log($level, $message){
		if($this->woocommerce_is_activated()){
			if(get_option('printify_print_providers_logging') === 'yes'){
				$logger = wc_get_logger();
				$woocommerce_log_name = array( 'source' => PRINTIFY_PRINT_PROVIDERS );
				$logger->log( $level, $message, $woocommerce_log_name );
			}
		}
	}

	/**
	 * Function creates a new product for Printify purposes if it doesn't already exist
	 *
	 * @since    1.0
	 * @return   integer | null
	 */
	public function create_custom_printify_product(){

		$product_id = get_option('printify_print_providers_custom_product_id');

		if(!get_option('printify_print_providers_custom_product_id') || !get_post($product_id)){ //Creating the product if it does not exist or has been deleted
			$product_data = array(
				'post_title'	=> __( 'Custom Printify product', 'printify-print-providers' ),
				'post_content'	=> sprintf(__( 'This product was automatically created by %s plugin. It is used to synchronize orders from Printify. If you are selling multiple variations of this product, please create variations and set each variation its SKU. After that you should email your SKU values to Printify to make the necessary configuration on their side.', 'printify-print-providers' ), PRINTIFY_PRINT_PROVIDERS_PLUGIN_NAME),
				'post_author'   => 1,
				'post_type'		=> 'product'
			);

			//Add the post to database
			$product_id = wp_insert_post( $product_data );
			update_option( 'printify_print_providers_custom_product_id', $product_id );
		}

		$this->add_custom_printify_product_image($product_id);
		return $product_id;
	}

	/**
	 * Function uploads a new image into Media library (if it doesn't already exist) and sets it as Product image
	 *
	 * @since    1.0
	 */
	public function add_custom_printify_product_image($product_id){
		$attach_id = get_option('printify_print_providers_custom_product_image');
		if(!get_option('printify_print_providers_custom_product_image') || !wp_get_attachment_image($attach_id)){ //If we haven't previously uploaded custom Printify product image or it doesn't exists - go ahead and add the image to the media library
			$image_url = plugins_url( 'assets/printify-custom-product-image.jpg', __FILE__ );
			$upload_dir = wp_upload_dir();
		    $image_data = file_get_contents($image_url);
		    $filename = basename($image_url);
			if(wp_mkdir_p($upload_dir['path'])){
				$file = $upload_dir['path'] . '/' . $filename;
			}else{
				$file = $upload_dir['basedir'] . '/' . $filename;
			}
		    file_put_contents($file, $image_data);

		    $wp_filetype = wp_check_filetype($filename, null );
		    $attachment = array(
		        'post_mime_type' => $wp_filetype['type'],
		        'post_title' => sanitize_file_name($filename),
		        'post_content' => '',
		        'post_status' => 'inherit'
		    );
		    $attach_id = wp_insert_attachment( $attachment, $file, $product_id );
			update_option( 'printify_print_providers_custom_product_image', $attach_id );
		    require_once(ABSPATH . 'wp-admin/includes/image.php');
		    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		    wp_update_attachment_metadata( $attach_id, $attach_data );
		    set_post_thumbnail( $product_id, $attach_id );

		}else{
			set_post_thumbnail( $product_id, $attach_id );
		}
	}

	/**
	 * Function adds additional Pricing fields required by Printify Stock API to simple Printify products (set in API settings)
	 *
	 * @since    1.0
	 */
	function add_simple_product_pricing_fields(){
		global $post;
		$printify_product_ids = $this->get_printify_product_ids();

		if(in_array($post->ID, $printify_product_ids)){ //If we are looking at Printify product
			$blank_field = array(
				'label' 		=> __( 'Blank price (USD cents)', 'printify-print-providers' ),
				'class' 		=> 'short',
				'id' 			=> 'printify_print_providers_blank_price',
				'type' 			=> 'text',
				'desc_tip' 		=> true,
				'description' 	=> __('Price for a blank product without print in USD cents', 'printify-print-providers' )
			);

			$processing_field = array(
				'label' 		=> __( 'Processing fee (USD cents)', 'printify-print-providers' ),
				'class' 		=> 'short',
				'id' 			=> 'printify_print_providers_processing_fee',
				'type' 			=> 'text',
				'desc_tip' 		=> true,
				'description' 	=> __('Processing fee for the item in USD cents. If there is no additional processing fee, it should be set to 0', 'printify-print-providers' )
			);

			$printing_field = array(
				'label' 		=> __( 'Printing price (USD cents)', 'printify-print-providers' ),
				'class' 		=> 'short',
				'id' 			=> 'printify_print_providers_printing_price',
				'type' 			=> 'text',
				'desc_tip' 		=> true,
				'description' 	=> __('Price object for printing the item. Price can be divided into multiple offers depending on the areas being printed. Prices should be expressed in USD cents', 'printify-print-providers' )
			);

			woocommerce_wp_text_input( $blank_field );
			woocommerce_wp_text_input( $processing_field );
			woocommerce_wp_text_input( $printing_field );
		}
	}

	/**
	 * Function saves additional Pricing fields required by Printify Stock API for simple products (set in API settings)
	 *
	 * @since    1.0
	 */
	function save_simple_product_pricing_fields( $post_id ) {
		$blank_price 	= null;
		$processing_fee = null;
		$printing_price = null;

		if(isset( $_POST[ 'printify_print_providers_blank_price'] )){
			$blank_price = $_POST[ 'printify_print_providers_blank_price'];
		}
		if(isset( $_POST[ 'printify_print_providers_processing_fee'] )){
			$processing_fee = $_POST[ 'printify_print_providers_processing_fee'];
		}
		if(isset( $_POST[ 'printify_print_providers_printing_price'] )){
			$printing_price = $_POST[ 'printify_print_providers_printing_price'];
		}

		$blank_price = isset( $blank_price ) ? sanitize_text_field( $blank_price ) : '';
		$processing_fee = isset( $processing_fee ) ? sanitize_text_field( $processing_fee ) : '';
		$printing_price = isset( $printing_price ) ? sanitize_text_field( $printing_price ) : '';

		$product = wc_get_product( $post_id );
		$product->update_meta_data( 'printify_print_providers_blank_price', $blank_price );
		$product->update_meta_data( 'printify_print_providers_processing_fee', $processing_fee );
		$product->update_meta_data( 'printify_print_providers_printing_price', $printing_price );

		$product->save();
	}

	/**
	 * Function adds additional Pricing fields required by Printify Stock API to variable products (set in API settings)
	 *
	 * @since    1.0
	 */
	function add_variable_product_pricing_fields( $loop, $variation_data, $variation ) {
		$variation_id = $variation->ID;
		$post_parent = $variation->post_parent;
		$printify_product_ids = $this->get_printify_product_ids();

		if(in_array($variation_id, $printify_product_ids) || in_array($post_parent, $printify_product_ids)){ //If we are looking at Printify products
			$blank_field = array(
				'label' 		=> __( 'Blank price (USD cents)', 'printify-print-providers' ),
				'class' 		=> 'short',
				'wrapper_class' => 'form-row form-row-first',
				'id' 			=> 'printify_print_providers_blank_price[' . $loop . ']',
				'type' 			=> 'text',
				'desc_tip' 		=> true,
				'description' 	=> __('Price for a blank product without print in USD cents', 'printify-print-providers' ),
				'value' 		=> get_post_meta( $variation->ID, 'printify_print_providers_blank_price', true )
			);

			$processing_field = array(
				'label' 		=> __( 'Processing fee (USD cents)', 'printify-print-providers' ),
				'class' 		=> 'short',
				'wrapper_class' => 'form-row form-row-last',
				'id' 			=> 'printify_print_providers_processing_fee[' . $loop . ']',
				'type' 			=> 'text',
				'desc_tip' 		=> true,
				'description' 	=> __('Processing fee for the item in USD cents. If there is no additional processing fee, it should be set to 0', 'printify-print-providers' ),
				'value' 		=> get_post_meta( $variation->ID, 'printify_print_providers_processing_fee', true )
			);

			$printing_field = array(
				'label' 		=> __( 'Printing price (USD cents)', 'printify-print-providers' ),
				'class' 		=> 'short',
				'wrapper_class' => 'form-row form-row-first',
				'id' 			=> 'printify_print_providers_printing_price[' . $loop . ']',
				'type' 			=> 'text',
				'desc_tip' 		=> true,
				'description' 	=> __('Price object for printing the item. Price can be divided into multiple offers depending on the areas being printed. Prices should be expressed in USD cents', 'printify-print-providers' ),
				'value' 		=> get_post_meta( $variation->ID, 'printify_print_providers_printing_price', true )
			);

			woocommerce_wp_text_input( $blank_field );
			woocommerce_wp_text_input( $processing_field );
			woocommerce_wp_text_input( $printing_field );
		}
	}

	/**
	 * Function saves additional Pricing fields required by Printify Stock API for variable products (set in API settings)
	 *
	 * @since    1.0
	 */
	function save_variable_product_pricing_field_data( $variation_id, $i ) {
		$blank_price 	= null;
		$processing_fee = null;
		$printing_price = null;

		if(isset( $_POST[ 'printify_print_providers_blank_price'][$i] )){
			$blank_price = $_POST[ 'printify_print_providers_blank_price'][$i];
		}
		if(isset( $_POST[ 'printify_print_providers_processing_fee'][$i] )){
			$processing_fee = $_POST[ 'printify_print_providers_processing_fee'][$i];
		}
		if(isset( $_POST[ 'printify_print_providers_printing_price'][$i] )){
			$printing_price = $_POST[ 'printify_print_providers_printing_price'][$i];
		}

		$blank_price = isset( $blank_price ) ? sanitize_text_field( $blank_price ) : '';
		$processing_fee = isset( $processing_fee ) ? sanitize_text_field( $processing_fee ) : '';
		$printing_price = isset( $printing_price ) ? sanitize_text_field( $printing_price ) : '';

		$product = wc_get_product( $variation_id );
		$product->update_meta_data( 'printify_print_providers_blank_price', $blank_price );
		$product->update_meta_data( 'printify_print_providers_processing_fee', $processing_fee );
		$product->update_meta_data( 'printify_print_providers_printing_price', $printing_price );

		$product->save();
	}

	/**
	 * Function that verifies if the received request with X-API-KEY is valid or not
	 *
	 * @since    1.0
	 */
	public function check_api_key(WP_REST_Request $request){
        $key = $request->get_header('x_api_key');
        if($key == $api_key = get_option('printify_print_providers_api_key')){
            return true;
        }
        return false;
    }

	/**
	 * Register new WordPress API routes to handle communication with Printify
	 *
	 * @since    1.0
	 */
	public function create_custom_api_routes(){
		$printify_api_base = 'v2019-06';
		register_rest_route( $printify_api_base, '/orders.json', array(
			'methods' => 'POST',
			'callback' => array($this, 'create_order'),
			'permission_callback' => array( $this, 'check_api_key' )
		));

		register_rest_route( $printify_api_base, '/orders/(?P<printify_id>[a-z0-9\-]+).json', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_order'),
			'permission_callback' => array( $this, 'check_api_key' )
		));

		register_rest_route( $printify_api_base, '/order/(?P<printify_id>[a-z0-9\-]+)/events.json', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_order_events'),
			'permission_callback' => array( $this, 'check_api_key' )
		));

		register_rest_route( $printify_api_base, '/order/(?P<printify_id>[a-z0-9\-]+).json', array(
			'methods' => 'PUT',
			'callback' => array($this, 'update_order'),
			'permission_callback' => array( $this, 'check_api_key' )
		));

		register_rest_route( $printify_api_base, '/order/(?P<printify_id>[a-z0-9\-]+)/cancel.json', array(
			'methods' => 'POST',
			'callback' => array($this, 'cancel_order'),
			'permission_callback' => array( $this, 'check_api_key' )
		));

		register_rest_route( $printify_api_base, '/stock.json', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_stock'),
			'permission_callback' => array( $this, 'check_api_key' )
		));

		register_rest_route( $printify_api_base, '/stock/(?P<printify_id>[a-z0-9\-]+).json', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_stock'),
			'permission_callback' => array( $this, 'check_api_key' )
		));

		register_rest_route( $printify_api_base, '/pricing.json', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_pricing'),
			'permission_callback' => array( $this, 'check_api_key' )
		));

		register_rest_route( $printify_api_base, '/pricing/(?P<printify_id>[a-z0-9\-]+).json', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_pricing'),
			'permission_callback' => array( $this, 'check_api_key' )
		));
	}

	/**
	 * Function that receives the data from Printify, handles the creation of a new order and provides a response
	 *
	 * @since    1.0
	 */
	function create_order($data) {
		if($this->woocommerce_is_activated()){
			if(!empty($data->get_body())){ //If we have received data

				$body = json_decode($data->get_body());
				//Checking if Order hasn't been previously already created
				if(!$this->check_if_order_exists($body->id)){
					$errors = array();

					//Checking if mandatory fields have been provided from Printify. If any errors occur, the Order is not created
					if(!isset($body->address_to)){
						$errors[] = array(
							'field' 	=> 'address_to',
							'message' 	=> 'Address to was not provided',
							'code' 		=> 'address_to'
						);
					}

					if(!isset($body->address_from)){
						$errors[] = array(
							'field' 	=> 'address_from',
							'message' 	=> 'Address from was not provided',
							'code' 		=> 'address_from'
						);
					}

					if(empty($body->address_to->address1)){
						$errors[] = array(
							'address_to.address1' 	=> 'Was not provided',
							'code' 					=> 'address_to'
						);
					}

					if(empty($body->address_from->address1)){
						$errors[] = array(
							'address_from.address1' => 'Was not provided',
							'code' 					=> 'address_from'
						);
					}

					if(empty($body->address_to->city)){
						$errors[] = array(
							'address_to.city' 	=> 'Was not provided',
							'code' 				=> 'address_to'
						);
					}

					if(empty($body->address_from->city)){
						$errors[] = array(
							'address_from.city' => 'Was not provided',
							'code' 				=> 'address_from'
						);
					}

					if(empty($body->address_to->zip)){
						$errors[] = array(
							'address_to.zip' 	=> 'Was not provided',
							'code' 				=> 'address_to'
						);
					}

					if(empty($body->address_from->zip)){
						$errors[] = array(
							'address_from.zip' 	=> 'Was not provided',
							'code' 				=> 'address_from'
						);
					}

					if(empty($body->address_to->country)){
						$errors[] = array(
							'address_to.country' => 'Was not provided',
							'code' 				 => 'address_to'
						);
					}

					if(empty($body->address_from->country)){
						$errors[] = array(
							'address_from.country' 	=> 'Was not provided',
							'code' 				 	=> 'address_from'
						);
					}

					if(empty($body->address_to->first_name)){
						$errors[] = array(
							'address_to.first_name' => 'Was not provided',
							'code' 					=> 'address_to'
						);
					}

					if(empty($body->address_to->last_name)){
						$errors[] = array(
							'address_to.last_name' => 'Was not provided',
							'code' 					=> 'address_to'
						);
					}

					if(empty($body->address_from->company)){
						$errors[] = array(
							'address_from.company' 	=> 'Was not provided',
							'code' 				 	=> 'address_from'
						);
					}

					if(!isset($body->shipping)){
						$errors[] = array(
							'field' 	=> 'shipping',
							'message' 	=> 'Shipping information was not provided',
							'code' 		=> 'shipping'
						);
					}

					if(empty($body->shipping->carrier)){
						$errors[] = array(
							'field' 	=> 'carrier',
							'message' 	=> 'Shipping carrier was not provided',
							'code' 		=> 'shipping'
						);
					}

					if(empty($body->shipping->priority)){
						$errors[] = array(
							'field' 	=> 'priority',
							'message' 	=> 'Shipping priority was not provided',
							'code' 		=> 'shipping'
						);
					}

					//Checking if we have values coming from the Printify
					(isset($body->address_to->address1)) ? $address1_to = $body->address_to->address1 : $address1_to = '';
					(isset($body->address_to->address2)) ? $address2_to = $body->address_to->address2 : $address2_to = '';
					(isset($body->address_to->city)) ? $city_to = $body->address_to->city : $city_to = '';
					(isset($body->address_to->zip)) ? $zip_to = $body->address_to->zip : $zip_to = '';
					(isset($body->address_to->country)) ? $country_to = $body->address_to->country : $country_to = '';
					(isset($body->address_to->region)) ? $region_to = $body->address_to->region : $region_to = '';
					(isset($body->address_to->first_name)) ? $first_name_to = $body->address_to->first_name : $first_name_to = '';
					(isset($body->address_to->last_name)) ? $last_name_to = $body->address_to->last_name : $last_name_to = '';
					(isset($body->address_to->email)) ? $email_to = $body->address_to->email : $email_to = '';
					(isset($body->address_to->phone)) ? $phone_to = $body->address_to->phone : $phone_to = '';

					(isset($body->address_from->address1)) ? $address1_from = $body->address_from->address1 : $address1_from = '';
					(isset($body->address_from->address2)) ? $address2_from = $body->address_from->address2 : $address2_from = '';
					(isset($body->address_from->city)) ? $city_from = $body->address_from->city : $city_from = '';
					(isset($body->address_from->zip)) ? $zip_from = $body->address_from->zip : $zip_from = '';
					(isset($body->address_from->country)) ? $country_from = $body->address_from->country : $country_from = '';
					(isset($body->address_from->region)) ? $region_from = $body->address_from->region : $region_from = '';
					(isset($body->address_from->company)) ? $company_from = $body->address_from->company : $company_from = '';
					(isset($body->address_from->email)) ? $email_from = $body->address_from->email : $email_from = '';
					(isset($body->address_from->phone)) ? $phone_from = $body->address_from->phone : $phone_from = '';

					$billing_address = array(
						'address_1'  => $address1_to,
						'address_2'  => $address2_to,
						'city'       => $city_to,
						'postcode'   => $zip_to,
						'country'    => $country_to,
						'state'      => $region_to,
						'first_name' => $first_name_to,
						'last_name'  => $last_name_to,
						'phone'      => $phone_to
					);

					$shipping_address = array(
						'address_1'  => $address1_from,
						'address_2'  => $address2_from,
						'city'       => $city_from,
						'postcode'   => $zip_from,
						'country'    => $country_from,
						'state'      => $region_from,
						'company' 	 => $company_from,
						'email' 	 => $email_from,
						'phone'      => $phone_from
					);

					// Now we create the order
					$order = wc_create_order();
					$product_id = $this->create_custom_printify_product(); //Creating a new product if it doesn't already exist
					$affected_items = array();

					//Adding line items to our new order coming in from Printify
					if(isset($body->items)){
						foreach ($body->items as $key => $item) {
							$product_id = wc_get_product_id_by_sku($item->sku); //Retrieving product variation by SKU
							$product = wc_get_product($product_id);

							if($product){
								$product->set_name($product->get_name() . ' (ID: ' . $item->id . ')');
								$line_item_id = $order->add_product( $product, $item->quantity ); //Adding product to the order
								
								//Adding custom meta data to each product
								$print_files = array();
								$preview_files = array();
								foreach ($item->print_files as $key => $print_file) {
									$print_files[$key] = $print_file; //Create an array of print files
								}
								foreach ($item->preview_files as $key => $preview_file) {
									$preview_files[$key] = $preview_file; //Create an array of preview files
								}
								wc_add_order_item_meta($line_item_id, 'printify_files', array(
									'print_files' => $print_files,
									'preview_files' => $preview_files
								));

								//Updating line item status to Created
								update_post_meta( $line_item_id, '_printify_print_providers_item_status', 0 );
								$affected_items[$line_item_id] = $item->id;

							}else{ //In case we are unable to identify item's SKU received from Printify
								$errors[] = array(
									'field' 	=> 'item',
									'message' 	=> 'Received product SKU: '. $item->sku .' does not exist',
									'code' 		=> 'item' // Optional attribute
								);
							}
						}

					}else{
						$errors[] = array(
							'field' 	=> 'items',
							'message' 	=> 'Items were not provided',
							'code' 		=> 'items' // Optional attribute
						);
					}

					if(!$errors){ //If no errors have occured
						// Set addresses
						$order->set_address( $shipping_address, 'shipping' );
						$order->set_address( $billing_address, 'billing' );

						//Adding custom fields to the order
						if( $email_to ){
							$order->update_meta_data( 'Customer Email', $email_to );
						}
						if(isset($body->sample)){
							$order->update_meta_data( 'Sample', 'Yes' );
						}
						if(isset($body->reprint)){
							$order->update_meta_data( 'Reprint', 'Yes' );
						}
						if(isset($body->xqc)){
							$order->update_meta_data( 'Extra Quality Care', 'Yes' );
						}
						$order->update_meta_data( '_printify_order', 1 ); //Used to determine if the order came from Printify

						if(isset($body->shipping)){
							$carier = '';
							$priority = '';
							if(isset($body->shipping->carrier)){
								$carier = $body->shipping->carrier;
							}
							if(isset($body->shipping->priority)){
								$priority = $body->shipping->priority;
							}
							$order->update_meta_data( 'Shipping information', $carier . ',' . $priority );
						}

						$order->calculate_totals();
						$order->update_status( 'processing', __('Received a new order from Printify', 'printify-print-providers'), TRUE );
						$new_order_id = $order->get_id(); //Newly created WooCommerce order's ID
						$this->link_order_id($body->id, $new_order_id); //Inserting in the database a new row linking both Order ids

						//Creating array to be passed to events function
						$event_array = array(
							$item->id => array(
								'action' => 'created',
								'affected_items' => $affected_items
							)
						);
						$this->add_printify_note($event_array, $new_order_id); //Adding note used by Printify Tracking API

						$response = array(
							'code'			=> 200,
							'message'		=> 'Printify order successfully ceated. Order: ' . $new_order_id,
							'level'			=> 'info',
							'printify'		=> array(
								'status'		=> 'success',
								'id'			=> $body->id,
								'reference_id'	=> (string)$new_order_id
							)
						);

					}else{ //If some of the products were not created
						$response = array(
							'code'		=> 422,
							'message'	=> 'Unable to add all or some of the products. Order: ' . $body->id .'. Errors: ' . json_encode($errors),
				  			'level'		=> 'warning',
							'printify'	=> array(
								'status'	=> 'failed',
					  			'errors'	=> $errors
							)
						);
					}

				}else{ //If order has already been created
					$response = array(
						'code'		=> 304,
						'message'	=> 'The request has already been received and order created. ID: ' . $body->id,
			  			'level'		=> 'warning',
			  			'printify'	=> array(
							'status'	=> 'failed',
				  			'message'	=> 'The request has already been received and order created. ID: ' . $body->id
				  		)
					);
				}

			}else{ //If Printify API doesn't provide data
				$response = array(
					'code'		=> 404,
					'message'	=> 'Printify request is missing body data',
		  			'level'		=> 'error',
					'printify'	=> array(
						'status'	=> 'failed',
			  			'message'	=> 'Printify request is missing body data'
			  		)
				);
			}

		}else{ //If WooCommerce has been disabled
			$response = array(
				'code'		=> 404,
				'message'	=> 'WooCommerce has been deactivated or is not installed',
	  			'level'		=> 'error',
				'printify'	=> array(
					'status'	=> 'failed',
		  			'message'	=> 'WooCommerce has been deactivated or is not installed'
		  		)
			);
		}
		$this->log( $response['level'], $response['message'] );
		return new WP_REST_Response( $response['printify'], $response['code'] );
	}

	/**
	 * Function that receives the data from Printify, handles update of existing order
	 * As soon as any of the products status is set to Anything else but Created, the order is considered as taken into Production and only shipping related information can be updated
	 * As soon as any of the items are marked as Packaged or Shipped - shipping related information is no longer updatable.
	 * Only Order ID can be updated no matter the order status
	 *
	 * @since    1.0
	 */
	function update_order($data) {
		if($this->woocommerce_is_activated()){
			if(!empty($data->get_body())){ //If we have received data
				global $wpdb;
				$body = json_decode($data->get_body());

				if(isset($data['printify_id'])){ //If we have received an order ID from Printify
					$order_id = $this->check_if_order_exists($data['printify_id']);
					if($order_id){ //If order exists in our database
						$order = wc_get_order($order_id->woocommerce_order_id);
						if($order){ //If WooCommerce order exists
							$started_processing = array('picked','printed','packaged','reprint','x-updates','on hold','shipped','canceled'); //Array listing all statuses of an order that has been taken into production
							$packaged_or_shipped = array('packaged','shipped'); //Array listing all statuses of an order that has been prepared for shipping
							$statuses = $this->get_product_statuses($order_id->woocommerce_order_id);
							$can_update = true;
							$errors = array();

							//Checking what kind of updates Printify is proposing
							if(isset($body->id)){ //If required to update order ID
								$can_update = true;
							}

							if(isset($body->sample)){ //If required to update Sample field
								if(count(array_intersect($started_processing, $statuses))){
									$can_update = false;
									$errors[] = array(
										'field'		=> 'sample',
										'message'	=> 'Order has been taken into production. Order: ' . $order_id->woocommerce_order_id,
										'code'		=> 'expired'
									);
								}
							}

							if(isset($body->reprint)){ //If required to update Reprint field
								if(count(array_intersect($started_processing, $statuses))){
									$can_update = false;
									$errors[] = array(
										'field'		=> 'reprint',
										'message'	=> 'Order has been taken into production. Order: ' . $order_id->woocommerce_order_id,
										'code'		=> 'expired'
									);
								}
							}

							if(isset($body->xqc)){ //If required to update XQC field
								if(count(array_intersect($started_processing, $statuses))){
									$can_update = false;
									$errors[] = array(
										'field'		=> 'xqc',
										'message'	=> 'Order has been taken into production. Order: ' . $order_id->woocommerce_order_id,
										'code'		=> 'expired'
									);
								}
							}

							if(isset($body->address_to)){ //If required to update Address_to fields
								if(count(array_intersect($packaged_or_shipped, $statuses))){
									$can_update = false;
									$errors[] = array(
										'field'		=> 'address_to',
										'message'	=> 'The order has already been partially or fully packaged and shipped. Order: ' . $order_id->woocommerce_order_id,
										'code'		=> 'expired'
									);
								}
							}

							if(isset($body->address_from)){ //If required to update Address_to fields
								if(count(array_intersect($packaged_or_shipped, $statuses))){
									$can_update = false;
									$errors[] = array(
										'field'		=> 'address_from',
										'message'	=> 'The order has already been partially or fully packaged and shipped. Order: ' . $order_id->woocommerce_order_id,
										'code'		=> 'expired'
									);
								}
							}

							if(isset($body->shipping)){ //If required to update Shipping field
								if(count(array_intersect($packaged_or_shipped, $statuses))){
									$can_update = false;
									$errors[] = array(
										'field'		=> 'shipping',
										'message'	=> 'The order has already been partially or fully packaged and shipped. Order: ' . $order_id->woocommerce_order_id,
										'code'		=> 'expired'
									);
								}
							}

							if(isset($body->items)){ //If required to update Items
								if(count(array_intersect($started_processing, $statuses))){
									$can_update = false;
									$errors[] = array(
										'field'		=> 'items',
										'message'	=> 'Order has been taken into production. Order: ' . $order_id->woocommerce_order_id,
										'code'		=> 'expired'
									);
								}
							}

							//Checking if we are able to fully update the entire order or not
							if($can_update){ //In case we are allowed to update the order, try to update it
								$table_name = $wpdb->prefix . PRINTIFY_PRINT_PROVIDERS_TABLE_NAME;
								$updated_fields = array();

								if(isset($body->id)){ //If required to update order ID
									//Check if database already hasn't got a Printify Order with the provided ID since we do not accept duplicate IDs
									$prinitfy_id_exists = $this->check_if_order_exists($body->id);
									if($prinitfy_id_exists){ //We find that the order ID already exists
										$errors[] = array(
											'field'		=> 'id',
											'message'	=> 'Unable to update order ID. Duplicate order ID entry. Order: ' . $data['printify_id'],
											'code'		=> 'other'
										);

									}else{
										$updated = $wpdb->update( $table_name, ['printify_order_id' => $body->id], ['printify_order_id' => $data['printify_id']]); //Updating order ID
										
										if ( false === $updated ) { //In case update experienced errors
											$errors[] = array(
												'field'		=> 'id',
												'message'	=> 'Unable to update order ID. Order: ' . $data['printify_id'],
												'code'		=> 'other'
											);
										}else{
											$updated_fields[] = 'ID'; //Adding updated fields to the array
										}
									}
								}

								if(isset($body->sample)){ //If required to update Sample field
									$current_sample_value = $order->get_meta( 'Sample' );
									//Checking if field differs from the new one
									if($current_sample_value == 'Yes'){
										$current_sample_value = true;
									}else{
										$current_sample_value = false;
									}

									if($body->sample !== $current_sample_value){ //If Reprint differs, update the value
										if($body->sample == true){
											$order->update_meta_data( 'Sample', 'Yes' );
										}else{
											$order->update_meta_data( 'Sample', 'No' );
										}
										$updated_fields[] = 'Sample'; //Adding updated fields to the array
									}
								}

								if(isset($body->reprint)){ //If required to update Reprint field
									$current_reprint_value = $order->get_meta( 'Reprint' );
									//Checking if field differs from the new one
									if($current_reprint_value == 'Yes'){
										$current_reprint_value = true;
									}else{
										$current_reprint_value = false;
									}

									if($body->reprint !== $current_reprint_value){ //If Reprint differs, update the value
										if($body->reprint == true){
											$order->update_meta_data( 'Reprint', 'Yes' );
										}else{
											$order->update_meta_data( 'Reprint', 'No' );
										}
										$updated_fields[] = 'Reprint'; //Adding updated fields to the array
									}
								}

								if(isset($body->xqc)){ //If required to update XQC field
									$current_xqc_value = $order->get_meta( 'Extra Quality Care' );
									//Checking if field differs from the new one
									if($current_xqc_value == 'Yes'){
										$current_xqc_value = true;
									}else{
										$current_xqc_value = false;
									}

									if($body->xqc !== $current_xqc_value){ //If Reprint differs, update the value
										if($body->xqc == true){
											$order->update_meta_data( 'Extra Quality Care', 'Yes' );
										}else{
											$order->update_meta_data( 'Extra Quality Care', 'No' );
										}
										$updated_fields[] = 'Extra Quality Care'; //Adding updated fields to the array
									}
								}

								if(isset($body->address_to)){ //If required to update Address_to fields
									(isset($body->address_to->address1)) ? $address1_to = $body->address_to->address1 : $address1_to = '';
									(isset($body->address_to->address2)) ? $address2_to = $body->address_to->address2 : $address2_to = '';
									(isset($body->address_to->city)) ? $city_to = $body->address_to->city : $city_to = '';
									(isset($body->address_to->zip)) ? $zip_to = $body->address_to->zip : $zip_to = '';
									(isset($body->address_to->country)) ? $country_to = $body->address_to->country : $country_to = '';
									(isset($body->address_to->region)) ? $region_to = $body->address_to->region : $region_to = '';
									(isset($body->address_to->first_name)) ? $first_name_to = $body->address_to->first_name : $first_name_to = '';
									(isset($body->address_to->last_name)) ? $last_name_to = $body->address_to->last_name : $last_name_to = '';
									(isset($body->address_to->email)) ? $email_to = $body->address_to->email : $email_to = '';
									(isset($body->address_to->phone)) ? $phone_to = $body->address_to->phone : $phone_to = '';

									$billing_address = array(
										'address_1'  => $address1_to,
										'address_2'  => $address2_to,
										'city'       => $city_to,
										'postcode'   => $zip_to,
										'country'    => $country_to,
										'state'      => $region_to,
										'first_name' => $first_name_to,
										'last_name'  => $last_name_to,
										'phone'      => $phone_to
									);

									$updated_fields[] = 'Billing address'; //Adding updated fields to the array
								}

								if(isset($body->address_from)){ //If required to update Address_from fields
									(isset($body->address_from->address1)) ? $address1_from = $body->address_from->address1 : $address1_from = '';
									(isset($body->address_from->address2)) ? $address2_from = $body->address_from->address2 : $address2_from = '';
									(isset($body->address_from->city)) ? $city_from = $body->address_from->city : $city_from = '';
									(isset($body->address_from->zip)) ? $zip_from = $body->address_from->zip : $zip_from = '';
									(isset($body->address_from->country)) ? $country_from = $body->address_from->country : $country_from = '';
									(isset($body->address_from->region)) ? $region_from = $body->address_from->region : $region_from = '';
									(isset($body->address_from->company)) ? $company_from = $body->address_from->company : $company_from = '';
									(isset($body->address_from->email)) ? $email_from = $body->address_from->email : $email_from = '';
									(isset($body->address_from->phone)) ? $phone_from = $body->address_from->phone : $phone_from = '';

									$shipping_address = array(
										'address_1'  => $address1_from,
										'address_2'  => $address2_from,
										'city'       => $city_from,
										'postcode'   => $zip_from,
										'country'    => $country_from,
										'state'      => $region_from,
										'company' 	 => $company_from,
										'email' 	 => $email_from,
										'phone'      => $phone_from
									);

									$updated_fields[] = 'Shipping address'; //Adding updated fields to the array
								}

								if(isset($body->shipping)){ //If required to update Shipping field
									$carier = '';
									$priority = '';
									if(isset($body->shipping->carrier)){
										$carier = $body->shipping->carrier;
									}
									if(isset($body->shipping->priority)){
										$priority = $body->shipping->priority;
									}
									$order->update_meta_data( 'Shipping information', $carier . ',' . $priority );

									$updated_fields[] = 'Shipping information'; //Adding updated fields to the array
								}

								if(isset($body->items)){ //If required to update Items SKU, Print / Preview files or quantity
									$product_id = get_option('printify_print_providers_custom_product_id');
									$product = wc_get_product($product_id); //Retrieving Printify custom product ID
									$affected_items = array();
									$line_items = $order->get_items();
									$printify_line_item_ids = array();
									$existing_line_item_ids = array();

									//Building two arrays. For checking if all product ID values coming from Printify exist in our Order
									foreach ($body->items as $key => $item){
										$printify_line_item_ids[] = $item->id;
									}
									foreach ($line_items as $line_id => $line_item){
										$existing_line_item_ids[] = $this->get_line_item_id($line_item);
									}

									if(count(array_intersect($printify_line_item_ids, $existing_line_item_ids)) == count($printify_line_item_ids)){
										//Checking line items coming from Printify that must be updated
										foreach ($body->items as $key => $item) {
											//Must check if update request includes all product ID's already existing in the order
											//If at leat one of the product is missing, we cancel the update
											foreach ($line_items as $line_id => $line_item) {
												$line_item_id = $this->get_line_item_id($line_item); //Retrieving product's ID

												if( $line_item_id == $item->id){ //If we find a matching ID
													//In case the order would like to update SKU, we must first check if a product with given SKU exists in our store. If it does, we are removing current line item and adding a new one instead
													if(isset($item->sku)){ //Updating SKU
														//In case the order wants to update SKU value, must first check if a product with given SKU exists in WooCommerce
														if(wc_get_product_id_by_sku($item->sku)){

															//Getting current line item SKU value
															if(wc_get_product($line_item->get_variation_id())){ //If we have variations
																$current_product = wc_get_product($line_item->get_variation_id());
																$current_sku = $current_product->get_sku();
															}else{
																$current_product = wc_get_product($line_item->get_product_id());
																$current_sku = $current_product->get_sku();
															}

															if( $current_sku !== $item->sku){ //Checking if the new SKU value is different from the one we already had
																$printify_product_id = wc_get_product_id_by_sku($item->sku);
																$product_object = wc_get_product( $printify_product_id );
																$product_price = $product_object->get_price();

																wc_delete_order_item($line_id); //Removing existing line item

																$product_object->set_name($product_object->get_name() . ' (ID: ' . $line_item_id . ')');
																$line_item_id = $order->add_product( $product_object, $item->quantity ); //Adding new line item with the SKU from Printify

																//Adding custom meta data to each product
																$print_files = array();
																$preview_files = array();
																foreach ($item->print_files as $file_key => $print_file) {
																	$print_files[$file_key] = $print_file; //Create an array of print files
																}
																foreach ($item->preview_files as $file_key => $preview_file) {
																	$preview_files[$file_key] = $preview_file; //Create an array of preview files
																}
																wc_add_order_item_meta($line_item_id, 'printify_files', array(
																	'print_files' => $print_files,
																	'preview_files' => $preview_files
																));

																//Updating line item status to Created
																update_post_meta( $line_item_id, '_printify_print_providers_item_status', 0 );
																$updated_fields[] = 'SKU'; //Adding updated fields to the array

															}

														}else{ //If unable to update line item SKU
															$errors[] = array(
																'field'		=> 'sku',
																'message'	=> 'Product SKU: '. $item->sku .' does not exist. Order: '. $order_id->woocommerce_order_id .'. Line item: ' . $line_item_id,
																'code'		=> 'item'
															);
														}
													}

													if(isset($item->preview_files) && isset($item->print_files)){ //Updating Print and Preview files
														$preview_files = array();
														$print_files = array();

														foreach ($item->preview_files as $key => $preview_file) {
															$preview_files[$key] = $preview_file; //Create an array of preview files
														}
														foreach ($item->print_files as $key => $print_file) {
															$print_files[$key] = $print_file; //Create an array of print files
														}
														wc_update_order_item_meta($line_id, 'printify_files', array(
															'print_files' => $print_files,
															'preview_files' => $preview_files
														));
														$updated_fields[] = 'Print and Preview files'; //Adding updated fields to the array

													}else{//In case we do not receive Print and Preview files in the request
														$errors[] = array(
															'field'		=> 'item',
															'message'	=> 'Update request for the item did not include both Preview and Print files. Order: '. $order_id->woocommerce_order_id .'. Line item: ' . $line_item_id,
															'code'		=> 'item'
														);
													}

													if(isset($item->quantity)){ //Updating Quantity
														//Checking if existing item quantity differs from the new one
														if($item->quantity !== $line_item->get_quantity()){
															if($item->quantity > 0){
																$line_item->set_quantity( $item->quantity );
																//Updating line item totals and subtotals
																$line_item->set_subtotal( $product->get_price() * $item->quantity );
																$line_item->set_total( $product->get_price() * $item->quantity );
																$updated_fields[] = 'Item quantity'; //Adding updated fields to the array

															}else{
																$errors[] = array(
																	'field'		=> 'item.quantity',
																	'message'	=> 'Item quantity value must be greater than 0. Order: '. $order_id->woocommerce_order_id .'. Line item: ' . $line_item_id,
																	'code'		=> 'item'
																);
															}
														}
													}
												}
											}
										}

									}else{
										$errors[] = array(
											'field'		=> 'sku',
											'message'	=> 'Some of the IDs sent in the request do not match the ones existing in the current order. Order: '. $order_id->woocommerce_order_id,
											'code'		=> 'item'
										);
									}
								}

								if(!$errors){ //If no errors occured
									if(isset($billing_address)){ //Updating billing address
										$order->set_address( $billing_address, 'billing' );
									}
									if(isset($shipping_address)){ //Updating shipping address
										$order->set_address( $shipping_address, 'shipping' );
									}
									$order->save_meta_data(); //Saving all requested meta data changes
									$order->save(); //Saving all item changes
									$order->calculate_totals();

									//Add order update note which will not be visible to Printify so that the shop owner has some basic knowledge when the order has been updated
									$updated_field_string = '';
									foreach (array_unique($updated_fields) as $key => $field) {
										if(empty($updated_field_string)){
											$updated_field_string = $field;
										}else{
											$updated_field_string .= ', ' . $field;
										}
									}
									$this->add_event( $order_id->woocommerce_order_id, 'Printify updated order. Updated fields: '. $updated_field_string, false );

									$response = array(
										'code'			=> 200,
										'message'		=> 'Printify order successfully updated. Order: ' . $order_id->woocommerce_order_id,
										'level'			=> 'info',
										'printify'		=> array(
											'status'		=> 'success'
										)
									);

								}else{ //If we encounter some errors, not updating any of the fields and returning errors
									$response = array(
										'code'		=> 422,
										'message'	=> 'Unable to update Printify order. Errors: ' . json_encode($errors),
								  		'level'		=> 'error',
										'printify'	=> array(
											'status'	=> 'failed',
								  			'errors'	=> $errors
								  		)
									);
								}

							}else{ //If at least one of the top-level elements is not allowed to be updated
								$response = array(
									'code'		=> 422,
									'message'	=> 'Unable to update Printify order. Errors: ' . json_encode($errors),
							  		'level'		=> 'error',
									'printify'	=> array(
										'status'	=> 'failed',
							  			'errors'	=> $errors
							  		)
								);
							}

						}else{//If order has been deleted
							$response = array(
								'code'		=> 404,
								'message'	=> 'Order has been deleted or was never there. ID: '. $data['printify_id'],
						  		'level'		=> 'error',
								'printify'	=> array(
									'status'	=> 'failed',
						  			'message'	=> 'Order has been deleted or was never there. ID: '. $data['printify_id']
						  		)
							);
						}

					}else{
						$response = array(
							'code'		=> 404,
							'message'	=> 'Order is not found. ID: '. $data['printify_id'],
							'level'		=> 'error',
							'printify'	=> array(
								'status'	=> 'failed',
					  			'message'	=> 'Order is not found. ID: '. $data['printify_id']
					  		)
						);
					}

				}else{
					$response = array(
						'code'		=> 404,
			  			'message'	=> 'Request from Printify did not include order ID',
			  			'level'		=> 'error',
						'printify'	=> array(
							'status'	=> 'failed',
				  			'message'	=> 'Request from Printify did not include order ID'
				  		)
					);
				}

			}else{ //If Printify API doesn't provide data
				$response = array(
					'code'		=> 404,
					'message'	=> 'Printify request is missing body data',
		  			'level'		=> 'error',
					'printify'	=> array(
						'status'	=> 'failed',
			  			'message'	=> 'Printify request is missing body data'
			  		)
				);
			}

		}else{ //If WooCommerce has been disabled
			$response = array(
				'code'		=> 404,
				'message'	=> 'WooCommerce has been deactivated or is not installed',
	  			'level'		=> 'error',
				'printify'	=> array(
					'status'	=> 'failed',
		  			'message'	=> 'WooCommerce has been deactivated or is not installed'
		  		)
			);
		}
		$this->log( $response['level'], $response['message'] );
		return new WP_REST_Response( $response['printify'], $response['code'] );
	}

	/**
	 * Function receives a request from Printify and returns product information
	 *
	 * @since    1.0
	 */
	function get_order($data) {
		if($this->woocommerce_is_activated()){			
			if(isset($data['printify_id'])){ //If we have received an order ID from Printify

				$order_id = $this->check_if_order_exists($data['printify_id']);
				if($order_id){ //If order exists in our database

					//Get woocommerce order data
					$order = wc_get_order($order_id->woocommerce_order_id);
					$order_data = $order->get_data();
					$items = $order->get_items();

					$sample = false;
					$reprint = false;
					$xqc = false;
					$carrier = '';
					$priority = '';
					$email = '';
					$email_from = '';
					$phone_from = '';
					$line_items = array();

					//Handling Order meta data
					if($order->get_meta('Sample') === 'Yes'){
						$sample = true;
					}
					if($order->get_meta('Reprint') === 'Yes'){
						$reprint = true;
					}
					if($order->get_meta('Extra Quality Care') === 'Yes'){
						$xqc = true;
					}
					if($order->get_meta('Shipping information')){
						$parts = explode(',', $order->get_meta('Shipping information'), 2); //Splitting our string sepparated with a comma into parts
						if(isset($parts[0])){
							$carrier = $parts[0];
						}else{
							$carrier = '';
						}
						if(isset($parts[1])){
							$priority = $parts[1];
						}else{
							$priority = '';
						}
					}
					if($order->get_meta('Customer Email')){
						$email = $order->get_meta('Customer Email');
					}
					if($order->get_meta('_shipping_email')){
						$email_from = $order->get_meta('_shipping_email');
					}
					if($order->get_meta('_shipping_phone')){
						$phone_from = $order->get_meta('_shipping_phone');
					}

					//Handling product line items
					foreach ($items as $key => $item) {
						$item_id = $this->get_line_item_id($item); //Retrieving product's ID
						$product_data = $item->get_data();
						$preview_files = array();
						$print_files = array();
						$sku = 'Unknown';

						if($item->get_variation_id()){ //Taking care of products with variations
							$product = wc_get_product( $item->get_variation_id() );
						}else{
							$product = wc_get_product( $item->get_product_id() );
						}

						if($product){
							if($product->get_sku()){
								$sku = $product->get_sku();
							}
						}

						//Must get preview files and print files
						$printify_files = $item->get_meta('printify_files');
						foreach ($printify_files['print_files'] as $key => $file) {
							$print_files[$key] = $file;
						}
						foreach ($printify_files['preview_files'] as $key => $file) {
							$preview_files[$key] = $file;
						}

						$line_items[] = array(
							'id' 			=> $item_id,
							'sku'			=> $sku,
							'preview_files' => $preview_files,
							'print_files' 	=> $print_files,
							'quantity'		=> $product_data['quantity']
						);
					}

					//Return back order data
					$response = array(
						'code'		=> 200,
						'message'	=> 'Printify order successfully returned. Order: '. $order_id->woocommerce_order_id,
						'level'		=> 'info',
						'printify'	=> array(
							'id'			=> $order_id->printify_order_id,
							'reference_id'	=> (string)$order_id->woocommerce_order_id,
							'sample'		=> $sample,
							'reprint'		=> $reprint,
							'xqc'			=> $xqc,
							'address_to'	=> array(
								'address1'		=> $order_data['billing']['address_1'],
								'address2'		=> $order_data['billing']['address_2'],
								'city'			=> $order_data['billing']['city'],
								'zip'   		=> $order_data['billing']['postcode'],
								'country'   	=> $order_data['billing']['country'],
								'region'      	=> $order_data['billing']['state'],
								'first_name' 	=> $order_data['billing']['first_name'],
								'last_name'  	=> $order_data['billing']['last_name'],
								'email'  		=> $email,
								'phone'      	=> $order_data['billing']['phone']
							),
							'address_from'		=> array(
								'address1'  	=> $order_data['shipping']['address_1'],
								'address2'  	=> $order_data['shipping']['address_2'],
								'city'       	=> $order_data['shipping']['city'],
								'zip'   		=> $order_data['shipping']['postcode'],
								'country'    	=> $order_data['shipping']['country'],
								'region'      	=> $order_data['shipping']['state'],
								'company' 	 	=> $order_data['shipping']['company'],
								'email'  		=> $email_from,
								'phone'      	=> $phone_from
							),
							'shipping'		=> array(
								'carrier'  		=> $carrier,
								'priority'      => $priority
							),
							'items'			=> $line_items
						)
					);

				}else{
					$response = array(
						'code'		=> 404,
						'message'	=> 'Order is not found. ID: '. $data['printify_id'],
				  		'level'		=> 'error',
						'printify'	=> array(
							'status'	=> 'failed',
							'code'		=> 404,
				  			'message'	=> 'Order is not found. ID: '. $data['printify_id']
				  		)
					);
				}

			}else{
				$response = array(
					'code'		=> 404,
					'message'	=> 'Request from Printify did not include order ID',
			  		'level'		=> 'error',
					'printify'	=> array(
						'status'	=> 'failed',
			  			'message'	=> 'Request from Printify did not include order ID'
			  		)
				);
			}

		}else{ //If WooCommerce has been disabled
			$response = array(
				'code'		=> 404,
				'message'	=> 'WooCommerce has been deactivated or is not installed',
	  			'level'		=> 'error',
				'printify'	=> array(
					'status'	=> 'failed',
		  			'message'	=> 'WooCommerce has been deactivated or is not installed'
		  		)
			);
		}
		$this->log( $response['level'], $response['message'] );
		return new WP_REST_Response( $response['printify'], $response['code'] );
	}

	/**
	 * Function receives a request from Printify and returns order events
	 *
	 * @since    1.0
	 */
	function get_order_events($data) {
		if($this->woocommerce_is_activated()){			
			if(isset($data['printify_id'])){ //If we have received an order ID from Printify

				$order_id = $this->check_if_order_exists($data['printify_id']);
				if($order_id){ //If order exists in our database
					
					//Retrieving order notes
					$notes = wc_get_order_notes([
					   'order_id' => $order_id->woocommerce_order_id,
					   'type' => 'internal',
					   'order' => 'ASC',
					]);

					if(!empty($notes)){ //If we have notes
						$events = array();
						foreach ($notes as $key => $note){
							//Checking if value can be decoded since this will mean that we have an array that is saved by us
							if(json_decode($note->content)){

								//Converting WooCommerce time to UTC time
							    $time = new DateTime($note->date_created);
							    $time->setTimeZone(new DateTimeZone('UTC'));
							    $time = $time->format('c');

							    $items = json_decode($note->content);
							    if(is_array($items)){
							    	foreach ($items as $key => $item) {

										$item = (array)$item;
										$item['time'] = $time;

										if(isset($item['notes'])){
											//unset($item['notes']);
											$note_data = $item['notes'];
											$item['notes'] = $note_data;
										}

										$affected_item_array = array();

										if(isset($item['affected_items'])){
											$affected_items = $item['affected_items'];
											//Preparing affected items for Printify
											foreach ($affected_items as $key => $affected_item) {
												$affected_item_array[] = $affected_item;
											}
										}

										unset($item['affected_items']); //Removing previous Object of affected items so we could place instead an array ot the same items
										$item['affected_items'] = $affected_item_array;
										$events[] = $item;
									}
							    }
							}
						}

						//Calculating global order status
						$statuses = $this->get_product_statuses($order_id->woocommerce_order_id);
						$order_status = 'created';

						if(in_array( 'canceled', $statuses )){ //If at least one of the products has status canceled
							$order_status = 'canceled';
						}
						if(in_array( 'shipped', $statuses )){ //If at least one of the products has status shipped
							$order_status = 'shipped';
						}
						if(in_array( 'packaged', $statuses )){ //If at least one of the products has status packaged
							$order_status = 'packaged';
						}
						if(in_array( 'on hold', $statuses )){ //If at least one of the products has status on hold
							$order_status = 'on hold';
						}
						if(in_array( 'reprint', $statuses )){ //If at least one of the products has status reprint
							$order_status = 'reprint';
						}
						if(in_array( 'printed', $statuses )){ //If at least one of the products has status printed
							$order_status = 'printed';
						}
						if(in_array( 'picked', $statuses )){ //If at least one of the products has status picked
							$order_status = 'picked';
						}
						if(in_array( 'created', $statuses )){ //If at least one of the products has status created
							$order_status = 'created';
						}

						//Return back order event data
						$response = array(
							'code'		=> 200,
							'message'	=> 'Printify order events successfully returned. Order: '. $order_id->woocommerce_order_id,
							'level'		=> 'info',
							'printify'	=> array(
								'status'	=> $order_status,
								'events'	=> $events
							)
						);

					}else{ //If no events found
						$response = array(
							'code'		=> 404,
							'message'	=> 'No events found. ID: '. $data['printify_id'],
					  		'level'		=> 'error',
							'printify'	=> array(
								'status'	=> 'failed',
								'code'		=> 404,
					  			'message'	=> 'No events found. ID: '. $data['printify_id']
					  		)
						);
					}

				}else{
					$response = array(
						'code'		=> 404,
						'message'	=> 'Order is not found. ID: '. $data['printify_id'],
				  		'level'		=> 'error',
						'printify'	=> array(
							'status'	=> 'failed',
							'code'		=> 404,
				  			'message'	=> 'Order is not found. ID: '. $data['printify_id']
				  		)
					);
				}

			}else{
				$response = array(
					'code'		=> 404,
					'message'	=> 'Request from Printify did not include order ID',
			  		'level'		=> 'error',
					'printify'	=> array(
						'status'	=> 'failed',
			  			'message'	=> 'Request from Printify did not include order ID'
			  		)
				);
			}

		}else{ //If WooCommerce has been disabled
			$response = array(
				'code'		=> 404,
				'message'	=> 'WooCommerce has been deactivated or is not installed',
	  			'level'		=> 'error',
				'printify'	=> array(
					'status'	=> 'failed',
		  			'message'	=> 'WooCommerce has been deactivated or is not installed'
		  		)
			);
		}
		$this->log( $response['level'], $response['message'] );
		return new WP_REST_Response( $response['printify'], $response['code'] );
	}

	/**
	 * Function handles cancelation requests from Printify. Either for the whole order or specific line items in the order
	 *
	 * @since    1.0
	 */
	function cancel_order($data){
		if($this->woocommerce_is_activated()){
			if(isset($data['printify_id'])){ //If we have received an order ID from Printify
				$order_id = $this->check_if_order_exists($data['printify_id']);
				if($order_id){ //If order exists in our database
					$order = wc_get_order($order_id->woocommerce_order_id);
						if($order){ //If WooCommerce order exists
							$started_processing = array('picked','printed','packaged','reprint','x-updates','on hold','shipped'); //Array listing all statuses of an order that has been taken into production
							$packaged_or_shipped = array('packaged','shipped'); //Array listing all statuses of an order that has been prepared for shipping
							$canceled = array('canceled'); //Array listing all statuses of an order that has been canceled

							//If the request has no data, must cancel entire order
							$items_to_cancel = false;
							if(!empty($data->get_body())){
								$body = json_decode($data->get_body());
								if(isset($body->items)){
									$items_to_cancel = $body->items;
								}
							}

							//Must check if each product in the order hasn't been taken into production
							$items = $order->get_items();
							$item_array = array();
							$cancelation_issues = false;
							$canceled_items = array();

							//Creating array of all line item IDs so that we can test if the IDs provided by Printify exist in our current order at all
							$item_id_array = array();
							foreach ($items as $line_item_id => $item) {
								$item_id_array[] = $this->get_line_item_id($item);
							}

							foreach ($items_to_cancel as $key => $item) {
								if(!in_array($item, $item_id_array)){
									$cancelation_issues = true;
									$item_array[] = array(
										'id' 		=> $item,
										'status' 	=> 'failed',
										'message' 	=> 'Item ID does not exist in the order'
									);
								}
							}

							foreach ($items as $line_item_id => $item) { //Handling product line items
								$product_data = $item->get_data();
								$item_status = get_post_meta( $product_data['id'],'_printify_print_providers_item_status', true );
								$status = $this->get_status($item_status);
								$line_item_printify_id = $this->get_line_item_id($item); //Retrieving product's ID

								if($items_to_cancel){ //Canceling only specific items
									if(in_array($line_item_printify_id, $items_to_cancel)){
										if(in_array($status, $canceled)){ //The item already has status Canceled
											$cancelation_issues = true;
											$item_array[] = array(
												'id' => $line_item_printify_id,
												'status' => 'failed',
												'message' => 'Item has already been canceled'
											);	
										}

										elseif(in_array( $status, $packaged_or_shipped )){
											$cancelation_issues = true;
											$item_array[] = array(
												'id' => $line_item_printify_id,
												'status' => 'failed',
												'message' => 'Item has been packed or shipped and can not be canceled'
											);

										}else{
											if(in_array( $status, $started_processing )){
												$cancelation_issues = true;
												$item_array[] = array(
													'id' => $line_item_printify_id,
													'status' => 'failed',
													'message' => 'Unable to cancel. Item already in production'
												);

											}else{
												$item_array[] = array(
													'id' => $line_item_printify_id,
													'status' => 'success'
												);
												$canceled_items[] = $line_item_printify_id;
												//Updating line item status to Canceled
												update_post_meta( $line_item_id, '_printify_print_providers_item_status', 8 );
											}
										}
									}

								}else{ //Canceling all items
									if(in_array($status, $canceled)){ //The item already has status Canceled
										$cancelation_issues = true;
										$item_array[] = array(
											'id' => $line_item_printify_id,
											'status' => 'failed',
											'message' => 'Item has already been canceled'
										);
									}

									elseif(in_array( $status, $packaged_or_shipped )){
										$cancelation_issues = true;
										$item_array[] = array(
											'id' => $line_item_printify_id,
											'status' => 'failed',
											'message' => 'Item has been packed or shipped and can not be canceled'
										);

									}else{
										if(in_array( $status, $started_processing )){
											$cancelation_issues = true;
											$item_array[] = array(
												'id' => $line_item_printify_id,
												'status' => 'failed',
												'message' => 'Unable to cancel. Item already in production'
											);

										}else{
											$item_array[] = array(
												'id' => $line_item_printify_id,
												'status' => 'success'
											);
											$canceled_items[] = $line_item_printify_id;
											//Updating line item status to Canceled
												update_post_meta( $line_item_id, '_printify_print_providers_item_status', 8 );
										}
									}
								}
							}

							if($cancelation_issues){ //If at least one of the item was impossible to be canceled
								$response = array(
									'code'		=> 422,
									'message'	=> 'Printify order was not canceled or partially canceled. Order: ' . $order_id->woocommerce_order_id . '. Items: '. json_encode($item_array),
									'level'		=> 'info',
									'printify'	=> array(
										'status'	=> 'failed',
										'items'		=> $item_array
									)
								);

							}else{ //If no issues detected
								$response = array(
									'code'		=> 200,
									'message'	=> 'Printify order successfully canceled. Order: '. $order_id->woocommerce_order_id,
									'level'		=> 'info',
									'printify'	=> array(
										'status'	=> 'success',
										'items'		=> $item_array
									)
								);
								if(!$items_to_cancel){ //If we had to cancel all items in the order - also marking WooCommerce order itself as Canceled
									$order->update_status( 'cancelled', __('Printify has canceled the order', 'printify-print-providers'), TRUE );
									
								}
							}

							//Adding note about the cancelation
							$note = '';
							if(isset($body->note)){ //In case the request from Printify provides a reason for cancelation
								$note = '. Reason: ' . $body->note;
							}
							$canceled_field_string = '';
							foreach ($canceled_items as $key => $field) {
								if(empty($canceled_field_string)){
									$canceled_field_string = $field;
								}else{
									$canceled_field_string .= ', ' . $field;
								}
							}
							
							if(!empty($canceled_field_string)){
								$this->add_event( $order_id->woocommerce_order_id, 'Printify canceled items: ' . $canceled_field_string . $note, false );

								$event_array = array(
									array(
										'action' => 'canceled',
										'affected_items' => $canceled_items
									)
								);
								$this->add_printify_note($event_array, $order_id->woocommerce_order_id); //Adding note used by Printify Tracking API
							}

						}else{//If WooCommerce order doesn't exist
							$response = array(
								'code'		=> 404,
								'message'	=> 'Order has been deleted or was never there. ID: '. $data['printify_id'],
						  		'level'		=> 'error',
								'printify'	=> array(
									'status'	=> 'failed',
						  			'message'	=> 'Order has been deleted or was never there. ID: '. $data['printify_id']
						  		)
							);
						}

				}else{
					$response = array(
						'code'		=> 404,
						'message'	=> 'Order is not found. ID: '. $data['printify_id'],
				  		'level'		=> 'error',
						'printify'	=> array(
							'status'	=> 'failed',
				  			'message'	=> 'Order is not found. ID: '. $data['printify_id']
				  		)
					);
				}

			}else{
				$response = array(
					'code'		=> 404,
					'message'	=> 'Request from Printify did not include order ID',
			  		'level'		=> 'error',
					'printify'	=> array(
						'status'	=> 'failed',
			  			'message'	=> 'Request from Printify did not include order ID'
			  		)
				);
			}

		}else{ //If WooCommerce has been disabled
			$response = array(
				'code'		=> 404,
				'message'	=> 'WooCommerce has been deactivated or is not installed',
	  			'level'		=> 'error',
				'printify'	=> array(
					'status'	=> 'failed',
		  			'message'	=> 'WooCommerce has been deactivated or is not installed'
		  		)
			);
		}
		$this->log( $response['level'], $response['message'] );
		return new WP_REST_Response( $response['printify'], $response['code'] );
	}

	/**
	 * Function returns product stock levels
	 * If request includes a specific SKU value, the response returns only specific SKU item stock.
	 *
	 * @since    1.0
	 */
	function get_stock($data){
		if($this->woocommerce_is_activated()){
			$product_found = false;
			if(isset($data['printify_id'])){ //If we have received an order ID from Printify
				$sku = $data['printify_id'];
				$product_id = wc_get_product_id_by_sku($sku); //Retrieving product ID by SKU
				if($product_id){ //If we have found Product
					$product_found = true;
				}else{
					$response = array(
						'code'		=> 404,
			  			'message'	=> 'SKU is not found. SKU: '. $data['printify_id'],
			  			'level'		=> 'error',
						'printify'	=> array(
							'status'	=> 'failed',
							'code'		=> 404,
				  			'message'	=> 'SKU is not found. SKU: '. $data['printify_id']
				  		)
					);
					$this->log( $response['level'], $response['message'] );
					return new WP_REST_Response( $response['printify'], $response['code'] );
				}
			}
			if($product_found){ //If a specific SKU has been passed and it has been retrieved
				$printify_product_ids[] = $product_id;

			}else{ //Returning all SKU stock levels since a specific SKU was not passed in the request
				$printify_product_ids = $this->get_printify_product_ids();
			}

			$sku_array = array();

			foreach ($printify_product_ids as $key => $product_id) {
				$product = wc_get_product($product_id);
				if($product){
					$main_product_sku = $product->get_sku();
					if($main_product_sku){ //If the product has SKU defined, adding it to the response 
						if($product_found){
							$sku_array = $this->add_product_stock($product);
						}else{
							$sku_array[$main_product_sku] = $this->add_product_stock($product);
						}
					}
					$product_variations = $product->get_children();
					foreach ($product_variations as $key => $variation_id) {
						$variation_product = wc_get_product($variation_id);
						$variable_product_sku = $variation_product->get_sku();
						if($variable_product_sku){ //If the variable product has SKU defined, adding it to the response
							$sku_array[$variable_product_sku] = $this->add_product_stock($variation_product);
						}
					}
				}
			}

			//In case the request included limit and offset values (e.g. stock.json?limit=20&offset=0), we limit and prepare the response accordingly
			if($data->get_params()){
				$attributes = $data->get_params();
				$limit = null;
				$offset = null;
				if(isset($attributes['limit'])){
					$limit = $attributes['limit'];
				}
				if(isset($attributes['offset'])){
					$offset = $attributes['offset'];
				}
				$sku_array = array_slice($sku_array, $offset, $limit); //Preparing the response
			}
			return new WP_REST_Response( $sku_array, 200 );
		}
	}

	/**
	 * Function returns product pricing
	 * If request includes a specific SKU value, the response returns only specific SKU item pricing.
	 *
	 * @since    1.0
	 */
	function get_pricing($data){
		if($this->woocommerce_is_activated()){
			$product_found = false;
			if(isset($data['printify_id'])){ //If we have received an order ID from Printify
				$sku = $data['printify_id'];
				$product_id = wc_get_product_id_by_sku($sku); //Retrieving product ID by SKU
				if($product_id){ //If we have found Product
					$product_found = true;
				}else{
					$response = array(
						'code'		=> 404,
			  			'message'	=> 'SKU is not found. SKU: '. $data['printify_id'],
			  			'level'		=> 'error',
						'printify'	=> array(
							'status'	=> 'failed',
							'code'		=> 404,
				  			'message'	=> 'SKU is not found. SKU: '. $data['printify_id']
				  		)
					);
					$this->log( $response['level'], $response['message'] );
					return new WP_REST_Response( $response['printify'], $response['code'] );
				}
			}
			if($product_found){ //If a specific SKU has been passed and it has been retrieved
				$printify_product_ids[] = $product_id;

			}else{ //Returning all SKU stock levels since a specific SKU was not passed in the request
				$printify_product_ids = $this->get_printify_product_ids();
			}

			$sku_array = array();

			foreach ($printify_product_ids as $key => $product_id) {
				$product = wc_get_product($product_id);
				if($product){
					$main_product_sku = $product->get_sku();
					if($main_product_sku){ //If the product has SKU defined, adding it to the response
						if($product_found){
							$sku_array = $this->add_product_pricing($product);
						}else{
							$sku_array[$main_product_sku] = $this->add_product_pricing($product);
						}
					}
					$product_variations = $product->get_children();
					foreach ($product_variations as $key => $variation_id) {
						$variation_product = wc_get_product($variation_id);
						$variable_product_sku = $variation_product->get_sku();
						if($variable_product_sku){ //If the variable product has SKU defined, adding it to the response
							$sku_array[$variable_product_sku] = $this->add_product_pricing($variation_product);
						}
					}
				}
			}

			//In case the request included limit and offset values (e.g. stock.json?limit=20&offset=0), we limit and prepare the response accordingly
			if($data->get_params()){
				$attributes = $data->get_params();
				$limit = null;
				$offset = null;
				if(isset($attributes['limit'])){
					$limit = $attributes['limit'];
				}
				if(isset($attributes['offset'])){
					$offset = $attributes['offset'];
				}
				$sku_array = array_slice($sku_array, $offset, $limit); //Preparing the response
			}
			return new WP_REST_Response( $sku_array, 200 );
		}
	}

	/**
	 * Function prepares product stock for returning back to Printify
	 *
	 * @since    1.0
	 * @return   array
	 */
	function add_product_stock($product){
		$status = $product->get_stock_status();
		if($status == 'instock'){
			$status = 'in-stock';
		}elseif($status == 'outofstock'){
			$status = 'out-of-stock';
		}else{
			$status = 'unknown';
		}

		$stock_quantity = $product->get_stock_quantity();
		if(empty($stock_quantity)){ //If Stock quantity is not defined or turned off, return unlimited quantity
			$stock = 99999;
		}else{
			$stock = $stock_quantity;
		}
		return $sku_array = array(
			'status' => $status,
			'stock' => $stock
		);
	}

	/**
	 * Function prepares product pricing for returning back to Printify
	 *
	 * @since    1.0
	 * @return   array
	 */
	function add_product_pricing($product){
		return $sku_array = array(
			'blank' 		=> $product->get_meta('printify_print_providers_blank_price'),
			'processing' 	=> $product->get_meta('printify_print_providers_processing_fee'),
			'printing'	 	=> array(
				'areas'			=> array('all'),
				'price'			=> $product->get_meta('printify_print_providers_printing_price')
			)
		);
	}

	/**
	 * Function adds a note to the order that later can be passed to Printify as event
	 *
	 * $order_id = Order ID
	 * $status	 = Line item status change (e.g. Shipped, Canceled, Created etc.)
	 * $affected_items = Line items that are affected with current changes
	 * $encode = should we encode the note. Encoding is necessary for all Printify events except those we want to exclude from sending back to Printify via events Tracking API
	 *
	 * @since    1.0
	 * @return   Integer | False
	 */
	function add_event($order_id, $information = array(), $encode ){
		$order = wc_get_order($order_id);
		if($order){
			if($encode == true){ //Encoding data to easily return it back to Printify Events API
				$information = json_encode($information);
			}
			return $order->add_order_note( $information, false, false );
		}else{
			return false;
		}
	}

	/**
	 * Function adds custom fields to each line item under orders from Printify
	 *
	 * @since    1.0
	 */
	public function add_custom_ordered_product_fields( $item_id, $item, $product ) {
		$available_printify_statuses = get_option( 'printify_print_providers_statuses' );
        if(isset($available_printify_statuses)) {
            if (!empty($product) && isset($product)){
				$post_type = $product->post_type;
				$product_id = $product->get_id();
                $printify_product = get_post_meta( $item_id, '_printify_print_providers_item_status', true );

               	if( $printify_product === '' ){
               		$printify_product = false;
               	}else{
               		$printify_product = true;
               	}

                if (!empty($post_type) && ('product' === $post_type || 'product_variation' === $post_type)  &&  $printify_product ) { //If we are looking at a product and it is Printify product

                	//Getting current data values
	                $item_status = get_post_meta( $item_id, '_printify_print_providers_item_status', true );
	                $item_tracking_nr = get_post_meta( $item_id, '_printify_print_providers_item_tracking_nr', true );
	                $item_carrier = get_post_meta( $item_id, '_printify_print_providers_item_carrier', true );
	                $item_notes = get_post_meta( $item_id, '_printify_print_providers_item_notes', true );

	                $allow_html = $this->allowed_html_elements();

	                echo wp_kses( //Building satus selectbox output
                		'<div class="printify-print-providers-status">
            				<p><label class="printify-print-providers-label" for="printify_print_providers_item_status_' . esc_attr__( $item_id ) . '">' . esc_html__( 'Status: ', 'printify-print-providers' ) . '</label>
            					<select class="wc-enhanced-select" id="printify_print_providers_item_status_' . esc_attr__( $item_id ) . '" name="_printify_print_providers_item_status_' . esc_attr__( $item_id ) . '">',$allow_html);
						foreach ($available_printify_statuses as $index => $status){
			                $item_status     = (int)$item_status;
			                $selected_option = ($item_status === $index) ? wp_kses('<option value="' .esc_attr($index) . '" '. selected($index, $index).'>' . esc_attr__( $status ) . '</option>',$allow_html) : wp_kses('<option value="' . esc_attr__( $index ) . '">' . esc_attr__( $status ) . '</option>',$allow_html);
			                $option_args     = array(
				                'option' => array(
					                'value'    => true,
					                'selected' => true,
				                )
			                );
			                echo wp_kses( $selected_option, $option_args );
		                }
		            	echo wp_kses('</select></p>',$allow_html);

	                	echo wp_kses(
	                	'<p><label class="printify-print-providers-label" for="printify_print_providers_item_tracking_nr_' . esc_attr__( $item_id ) . '">' . esc_html__( 'Tracking Nr.: ', 'printify-print-providers' ) . '</label>
	                		<input id="printify_print_providers_item_tracking_nr_' . esc_attr__( $item_id ) . '" name="_printify_print_providers_item_tracking_nr_' . esc_attr__( $item_id ) . '" type="text" value="'. esc_attr__($item_tracking_nr) .'" class="short"></p>
	                		<p><label class="printify-print-providers-label" for="printify_print_providers_item_carrier_' . esc_attr__( $item_id ) . '">' . esc_html__( 'Carrier: ', 'printify-print-providers' ) . '</label>
	                		<input id="printify_print_providers_item_carrier_' . esc_attr__( $item_id ) . '" name="_printify_print_providers_item_carrier_' . esc_attr__( $item_id ) . '" type="text" value="'. esc_attr__($item_carrier) .'" class="short"></p>', $allow_html);
		                

	                echo wp_kses('</div>',$allow_html);

	                 echo wp_kses('<div class="printify-print-providers-notes"><label class="printify-print-providers-label" for="printify_print_providers_item_notes_' . esc_attr__( $item_id ) . '">' . esc_html__( 'Notes: ', 'printify-print-providers' ) . '</label>
                		<input id="printify_print_providers_item_notes_' . esc_attr__( $item_id ) . '" name="_printify_print_providers_item_notes_' . esc_attr__( $item_id ) . '" type="text" value="'. esc_attr__($item_notes) .'" class="short"></div>', $allow_html);

	                //Retrieving Printify print and preview file links
	                $order = wc_get_order();
					$order_data = $order->get_data();
					$line_items = $order_data['line_items'];

					//Building url file output
					foreach ($line_items as $key => $line_item) {
						if($key == $item_id){

							$printify_files = $line_item->get_meta('printify_files');
							if($printify_files){
								if(!empty($printify_files['print_files']) && isset($printify_files['print_files'])){
									$print_files = $printify_files['print_files'];
									echo '<div class="printify-print-providers-print-files"><span class="printify-print-providers-label">Print files:</span>';
									foreach( $print_files as $key => $url ){
										echo '<a class="button" href="' . $url . '" target="_blank" >' . $key . '</a>';
									}
									echo '</div>';
								}

								if(!empty($printify_files['preview_files']) && isset($printify_files['preview_files'])){
									$preview_files = $printify_files['preview_files'];
									echo '<div class="printify-print-providers-preview-files"><span class="printify-print-providers-label">Preview files:</span>';
									foreach( $preview_files as $key => $url ){
										echo '<a href="' . $url . '" target="_blank"><img src="' . $url . '"/></a>';
									}
									echo '</div>';
								}
							}
						}
					}
            	}
            }
        }
	}

	/**
	 * Function saves custom field data of each line item under orders from Printify
	 *
	 * @since    1.0
	 */
	public function save_custom_ordered_product_fields($post_id, $post, $update) {
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}// Exit if it's an autosave
		$custom_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
		$custom_post_type = filter_input( INPUT_POST, 'post_type', FILTER_SANITIZE_STRING );
		$post_data = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
		$order_item_id = (isset( $post_data['order_item_id']) && !empty( $post_data['order_item_id'] ) ) ? $post_data['order_item_id'] : array();
		$order_id = (isset( $post_data['post_ID']) && !empty( $post_data['post_ID'] ) ) ? $post_data['post_ID'] : false;

		if('editpost' === $custom_action && 'shop_order' === $custom_post_type){
			if(isset($order_item_id) && is_array($order_item_id)){
				
				$status = '';
				$tracking_nr = '';
				$carrier = '';
				$event_array = array();
				$affected_items = array();

				foreach ($order_item_id as $item_id){ //Going through each line item of the order					
					$data_changed = false;
					$changes = array();
					$order = wc_get_order($order_id);
					$order_data = $order->get_data();
					$line_items = $order_data['line_items'];

					//Going through line items to get ordered product name
					foreach ($line_items as $key => $line_item){
						if($item_id == $key){
							$line_item_printify_id = $this->get_line_item_id($line_item); //Retrieving product's ID
						}
					}

					//Retrieving data both from post saving and one that is saved in the database
					$item_status = get_post_meta( $item_id, '_printify_print_providers_item_status', true );
					$item_status_data = filter_input( INPUT_POST, '_printify_print_providers_item_status_' . $item_id, FILTER_SANITIZE_STRING );
					$item_tracking_nr = get_post_meta( $item_id, '_printify_print_providers_item_tracking_nr', true );
					$item_tracking_nr_data = filter_input( INPUT_POST, '_printify_print_providers_item_tracking_nr_' . $item_id, FILTER_SANITIZE_STRING );
					$item_carrier = get_post_meta( $item_id, '_printify_print_providers_item_carrier', true );
					$item_carrier_data = filter_input( INPUT_POST, '_printify_print_providers_item_carrier_' . $item_id,FILTER_SANITIZE_STRING );
					$item_notes = get_post_meta( $item_id, '_printify_print_providers_item_notes', true );
					$item_notes_data = filter_input( INPUT_POST, '_printify_print_providers_item_notes_' . $item_id,FILTER_SANITIZE_STRING );
					
					//In case the value have been changed, save them in the database
					if($item_status !== $item_status_data){ 
						update_post_meta($item_id, '_printify_print_providers_item_status', $item_status_data);
						$status = $this->get_status($item_status_data);
						$data_changed = true;
					}else{
						$status = $this->get_status($item_status);
					}

					if($item_tracking_nr !== $item_tracking_nr_data){
						update_post_meta($item_id, '_printify_print_providers_item_tracking_nr', $item_tracking_nr_data);
						$tracking_nr = $item_tracking_nr_data;
						$data_changed = true;
					}else{
						$tracking_nr = $item_tracking_nr;
					}

					if($item_carrier !== $item_carrier_data){
						update_post_meta($item_id, '_printify_print_providers_item_carrier', $item_carrier_data);
						$carrier = $item_carrier_data;
						$data_changed = true;
					}else{
						$carrier = $item_carrier;
					}

					if($item_notes !== $item_notes_data){
						update_post_meta($item_id, '_printify_print_providers_item_notes', $item_notes_data);
						$notes = $item_notes_data;
						$data_changed = true;
					}else{
						$notes = $item_notes;
					}

					//Building change array only in case if changes have happened with the line item
					if($data_changed){
						$changes['action'] = $status;
						if(isset($tracking_nr)){
							$changes['tracking_number'] = $tracking_nr;
						}
						if(isset($carrier)){
							$changes['carrier'] = $carrier;
						}
						if(isset($notes)){
							$changes['notes'] = $notes;
						}

						//Here must check if $event_array does not already hold and data that is equal with current line item changes already is in the array and build the event array later used for Note addition
						if(in_array($changes, $event_array)){ //If event is dusplicate with another one
							$matching_id = array_search($changes, $event_array);

							//If ID already exists in the array, we add the line item ID to this key
							if(array_key_exists($matching_id, $affected_items)){ //Exists
								array_push($affected_items[$matching_id], $line_item_printify_id);
							}
							else{//Doesn't exist
								$affected_items[$matching_id] = array(
									$matching_id => $line_item_printify_id
								);
							}

						}else{//If doesn't exist
							$event_array[$item_id] = $changes;

							//Adding current item to the array affected items
							$affected_items[$item_id] = array(
								$item_id => $line_item_printify_id
							);
						}
					}
				}

				//Adding affected items to the event array
				foreach ($event_array as $event_key => $event){
					foreach ($affected_items as $item_key => $item){
						if($item_key == $event_key){
							$event_array[$event_key] += ['affected_items' => $item];
						}
					}
				}

				//Adding notes in case line items have been changed (status, tracking nr, shipping company or notes). Used by Printify Tracking API
				$this->add_printify_note($event_array, $order_id);

			}
		}
	}

	/**
	 * Function defines what HTML elements and their attributes are allowed to be outputed
	 *
	 * @since    1.0
	 */
	public function allowed_html_elements(){
		$allow_html_args = array(
			'input'      => array(
				'type'  => array(
					'checkbox' => true,
					'text'     => true,
					'submit'   => true,
					'button'   => true,
					'file'  => true,
				),
				'class' => true,
				'name' => true,
				'value' => true,
				'id'    => true,
				'style'    => true,
				'selected' => true,
				'checked' => true,
			),
			'select'     => array(
				'id' => true,
				'data-placeholder' => true,
				'name' => true,
				'multiple' => true,
				'class' => true,
				'style' => true
			),
			'a'          => array( 'href' => array(), 'title' => array(), 'target' => array() ),
			'b'          => array( 'class' => true ),
			'i'          => array( 'class' => true ),
			'p'          => array( 'class' => true ),
			'blockquote' => array( 'class' => true ),
			'h2'         => array( 'class' => true ),
			'h3'         => array( 'class' => true ),
			'ul'         => array( 'class' => true ),
			'ol'         => array( 'class' => true ),
			'li'         => array( 'class' => true ),
			'option'     => array( 'value' => true, 'selected' => true ),
			'table'      => array( 'class' => true ),
			'td'         => array( 'class' => true ),
			'th'         => array( 'class' => true, 'scope' => true ),
			'tr'         => array( 'class' => true ),
			'tbody'      => array( 'class' => true ),
			'label'      => array( 'class' => true, 'for' => true ),
			'strong'     => true,
			'div'      => array(
				'id'    => true,
				'class'    => true,
				'title'    => true,
				'style'    => true,

			),
			'textarea'   => array(
				'id'    => true,
				'class' => true,
				'name'  => true,
				'style' => true
			),
		);
		return $allow_html_args;
	}

	/**
	 * Function returns the ID value of a given order line item (ordered product) 
	 *
	 * @since    1.0
	 * @return   array
	 */
	public function get_line_item_id($line_item_id) {
		$product_data = $line_item_id->get_data();
		$product_name = $product_data['name']; //Printify product name stores Printify ID value
		preg_match('/\(ID: ([^\"]*?)\)/', $product_name, $id_match); //Getting out ID

		if(isset($id_match[1])){
			$id = $id_match[1];
		}else{
			$id = 'Unknown';
		}

		return $id;
	}

	/**
	 * Function adds note to Printify order that later can be used for events
	 *
	 * $events = array of line item events, $order_id = Order ID
	 *
	 * @since    1.0
	 * @return   integer
	 */
	public function add_printify_note($events, $order_id) {
		if(empty($events)){ //Exit in case we do not have any changes, exit
			return;
		}

		$information = array();
		foreach ($events as $key => $event) {
			$action = '';
			$affected_items = '';
			$tracking_number = '';
			$carrier = '';
			$notes = '';

			if(isset($event['action'])){
				$action = $event['action'];
			}
			if(isset($event['affected_items'])){
				$affected_items = $event['affected_items'];
			}
			if(isset($event['tracking_number'])){
				$tracking_number = $event['tracking_number'];
			}
			if(isset($event['carrier'])){
				$carrier = $event['carrier'];
			}
			if(isset($event['notes'])){
				$notes = $event['notes'];
			}

			$information[] = array_filter(array(
				'action' 			=> $action,
				'affected_items'	=> $affected_items,
				'eta'				=> '',
				'updates'			=> '',
				'tracking_url'		=> '',
				'tracking_number'	=> $tracking_number,
				'carrier'			=> $carrier,
				'notes'				=> $notes
			));
		}

		return $this->add_event($order_id, $information, true );
	}

	/**
	 * Function checks if the order arriving from Printify exists in the database or not and returns orders 
	 *
	 * @since    1.0
	 * @return   order_id | false
	 */
	public function check_if_order_exists($printify_id) {
		global $wpdb;
		$table_name = $wpdb->prefix . PRINTIFY_PRINT_PROVIDERS_TABLE_NAME;
		$row = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM ". $table_name ."
			WHERE printify_order_id = %s", $printify_id)
		);
		if($row){
			return $row;
		}else{
			return false;
		}
	}

	/**
	 * Function creates a row and assigns Printidy order ID with WooCommerce order ID.
	 *
	 * @since    1.0
	 * @return   boolean
	 */
	public function link_order_id($printify_id, $woocommerce_id) {
		global $wpdb;
		$table_name = $wpdb->prefix . PRINTIFY_PRINT_PROVIDERS_TABLE_NAME;
		$row = $wpdb->insert($table_name,
			array(
				'printify_order_id' => $printify_id,
				'woocommerce_order_id' => $woocommerce_id
			)
		);
		if($row){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Function returns order status
	 *
	 * $id = Status ID
	 *
	 * @since    1.0
	 * @return   string | false
	 */
	public function get_status($id) {
		$statuses = get_option('printify_print_providers_statuses');
		foreach ($statuses as $key => $status) {
			if($key == $id){
				$result = $status;
			}
		}
		if($result){
			return $result;
		}else{
			return false;
		}
	}

	/**
	 * Function returns an array of all unique statuses of the orders' products
	 *
	 * @since    1.0
	 * @return   array
	 */
	public function get_product_statuses($order_id) {
		global $wpdb;
		$statuses = array();
		$order = wc_get_order($order_id);
		if($order){
			$items = $order->get_items();
			foreach ($items as $key => $item) { //Handling product line items
				$product_data = $item->get_data();
				$item_status = get_post_meta( $product_data['id'],'_printify_print_providers_item_status', true );
				$statuses[] = $this->get_status($item_status);
			}
			$statuses = array_unique($statuses);
		}

		return $statuses;
	}

	/**
	 * Function removes record from our Printify database table in case if the order is manually deleted from WooCommerce
	 *
	 * @since    1.0
	 */
	public function delete_printify_order( $post_id ) {
		global $wpdb;
		$post = get_post($post_id);
		if( $post->post_type == 'shop_order' ){
			$table_name = $wpdb->prefix . PRINTIFY_PRINT_PROVIDERS_TABLE_NAME;
			$result = $wpdb->delete( $table_name, ['woocommerce_order_id' => $post_id] );
		}
	}

	/**
	 * Function adds an extra column to the WooCommerce orders for Printify
	 *
	 * @since    1.0
	 */
	function add_printify_column( $columns_array ) {
		return array_slice( $columns_array, 0, 4, true ) + array( 'printify' => 'Printify' ) + array_slice( $columns_array, 4, NULL, true );
	}

	/**
	 * Function outputs Printify tag in the Orders table in case the order came from Printify
	 *
	 * @since    1.0
	 */
	function insert_data_in_printify_column( $column_name ) {
		if( $column_name  == 'printify' ){
			$image_url = plugins_url( 'assets/printify-custom-product-image.jpg', __FILE__ );
			$order = wc_get_order(get_the_ID());
			$printify_order = $order->get_meta('_printify_order'); //Check if order came from Printify
			if($printify_order){
				echo "<mark class='order-status printify-print-providers-order-status'><img src='". $image_url ."' /></mark>";
			}
		}
	}

	/**
	 * Function returns all IDs that have been saved in the plugins' settings
	 *
	 * @since    1.0
	 * @return   array
	 */
	function get_printify_product_ids() {
		$product_ids = get_option('printify_print_providers_product_ids');
		$product_ids = explode(',', $product_ids);
		return array_map('trim', $product_ids); //Removing whitespaces from the array
	}
}
