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
	                      'readonly'	=> true,
	                )
				),

				array(
					'title'		=> __( 'Enable logging?', 'printify-print-providers' ),
					'type'		=> 'checkbox',
					'id'		=> 'printify_print_providers_logging',
					'desc'		=> $description,
				),

				array(
					'type'  => 'sectionend',
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
				'post_title'	=> __( 'Global Printify product', 'printify-print-providers' ),
				'post_content'	=> sprintf(__( 'This product was automatically created by %s plugin. It is used to synchronize orders from Printify.', 'printify-print-providers' ), PRINTIFY_PRINT_PROVIDERS_PLUGIN_NAME),
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
		register_rest_route( 'v2020-03', '/orders.json', array(
			'methods' => 'POST',
			'callback' => array($this, 'create_order'),
			'permission_callback' => array( $this, 'check_api_key' ),
		));

		register_rest_route( 'v2020-03/orders', '/(?P<printify_id>[a-z0-9\-]+).json', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_order'),
			'permission_callback' => array( $this, 'check_api_key' ),
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
				
				global $woocommerce;
				$body = json_decode($data->get_body());

				//Checking if Order hasn't been previously already created
				if(!$this->check_if_order_exists($body->id)){
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
					$product = wc_get_product($product_id); //Retrieving Printify custom product ID

					//Adding line items to our new order coming in from Printify
					foreach ($body->items as $key => $item) {
						$product->set_name($item->sku . ' (ID: ' . $item->id . ')');
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
					}

					// Set addresses
					$order->set_address( $shipping_address, 'shipping' );
					$order->set_address( $billing_address, 'billing' );

					//Adding custom fields to the order
					if( $email_to ){
						$order->update_meta_data( 'Customer Email', $email_to );
					}
					if(isset($body->sample)){
						if( $body->sample ){
							$order->update_meta_data( 'Sample', 'Yes' );
						}
					}
					if(isset($body->reprint)){
						if( $body->reprint ){
							$order->update_meta_data( 'Reprint', 'Yes' );
						}
					}
					if(isset($body->xqc)){
						if( $body->xqc ){
							$order->update_meta_data( 'Extra Quality Care', 'Yes' );
						}
					}

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

					$response = array(
						'status'		=> 'success',
						'code'			=> 200,
						'id'			=> $body->id,
						'reference_id'	=> $new_order_id,
						'message'		=> 'Printify order successfully ceated. ID: ' . $body->id,
						'level'			=> 'info',
					);

				}else{ //If order has already been created
					$response = array(
						'status'	=> 'failed',
						'code'		=> 304,
			  			'message'	=> 'The request has already been received and order created. ID: ' . $body->id,
			  			'level'		=> 'warning'
					);
				}

			}else{ //If Printify API doesn't provide data
				$response = array(
					'status'	=> 'failed',
					'code'		=> 404,
		  			'message'	=> 'Printify request is missing body data',
		  			'level'		=> 'error'
				);
			}

		}else{ //If WooCommerce has been disabled
			$response = array(
				'status'	=> 'failed',
				'code'		=> 404,
	  			'message'	=> 'WooCommerce has been deactivated or is not installed',
	  			'level'		=> 'error'
			);
		}
		$this->log( $response['level'], $response['message'] );
		return new WP_REST_Response( $response, $response['code'] );
	}

	/**
	 * Function receives a request from Printify and returns product information
	 *
	 * @since    1.0
	 */
	function get_order($data) {
		if($this->woocommerce_is_activated()){
			global $wpdb, $woocommerce;
			
			if(isset($data['printify_id'])){ //If we have received an order ID from Printify

				$order_ids = $this->check_if_order_exists($data['printify_id']);
				if($order_ids){ //If order exists in our database

					//Get woocommerce order data
					$order = wc_get_order($order_ids->woocommerce_order_id);
					$order_data = $order->get_data();
					$order_meta_data = $order->get_meta_data();
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

					foreach ($order_meta_data as $key => $object) { //Handling meta data
						$data = $object->get_data();
						if($data['key'] === 'Sample' && $data['value'] === 'Yes'){
							$sample = true;
						}
						if($data['key'] === 'Reprint' && $data['value'] === 'Yes'){
							$reprint = true;
						}
						if($data['key'] === 'Extra Quality Care' && $data['value'] === 'Yes'){
							$xqc = true;
						}
						if($data['key'] === 'Shipping information'){
							$parts = explode(',', $data['value'], 2); //Splitting our string sepparated with a comma into parts
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
						if($data['key'] === 'Customer Email'){
							$email = $data['value'];
						}
						if($data['key'] === '_shipping_email'){
							$email_from = $data['value'];
						}
						if($data['key'] === '_shipping_phone'){
							$phone_from = $data['value'];
						}
					}

					foreach ($items as $key => $item) { //Handling product line items
						//write_log($item);
						$product_data = $item->get_data();
						$product_meta_data = $item->get_meta_data();

						$product_name = $product_data['name']; //Printify product name stores both SKU and ID values
						preg_match('/\(ID: ([^\"]*?)\)/', $product_name, $id_match); //Getting out ID
						preg_match('/^(.*?) \(/', $product_name, $sku_match); //Getting out SKU

						if(isset($id_match[1])){
							$id = $id_match[1];
						}else{
							$id = 'Unknown';
						}
						if(isset($sku_match[1])){
							$sku = $sku_match[1];
						}else{
							$sku = 'Unknown';
						}

						$preview_files = array();
						$print_files = array();
						//Must get preview files and print files
						foreach ($product_meta_data as $key => $object) { //Handling line item meta data
							$data = $object->get_data();

							foreach ($data['value'] as $key => $files) {
								if($key == 'print_files'){
									foreach ($files as $key => $file) {
										$print_files[$key] = $file;
									}
								}
								if($key == 'preview_files'){
									foreach ($files as $key => $file) {
										$preview_files[$key] = $file;
									}
								}
							}
						}

						$line_items[] = array(
							'id' 			=> $id,
							'sku'			=> $sku,
							'preview_files' => $preview_files,
							'print_files' 	=> $print_files,
							'quantity'		=> $product_data['quantity']
						);
					}

					//Return back order data
					$response = array(
						'status'		=> 'success',
						'code'			=> 200,
						'message'		=> 'Printify order successfully returned. ID: '. $order_ids->printify_order_id,
						'level'			=> 'info',
						'id'			=> $order_ids->printify_order_id,
						'reference_id'	=> $order_ids->woocommerce_order_id,
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
					);

				}else{
					$response = array(
						'status'	=> 'failed',
						'code'		=> 404,
			  			'message'	=> 'Order is not found. ID: '. $data['printify_id'],
			  			'level'		=> 'error'
					);
				}

			}else{
				$response = array(
					'status'	=> 'failed',
					'code'		=> 404,
		  			'message'	=> 'Request from Printify did not include order ID',
		  			'level'		=> 'error'
				);
			}

		}else{ //If WooCommerce has been disabled
			$response = array(
				'status'	=> 'failed',
				'code'		=> 404,
	  			'message'	=> 'WooCommerce has been deactivated or is not installed',
	  			'level'		=> 'error'
			);
		}
		$this->log( $response['level'], $response['message'] );
		return new WP_REST_Response( $response, $response['code'] );
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
                $printify_product = get_option('printify_print_providers_custom_product_id');

                if (!empty($post_type) && ('product' === $post_type || 'product_variation' === $post_type)  && ($product_id == $printify_product)){ //If we are looking at a product and it is Printify product

                	//Getting current data values
	                $item_status = get_post_meta( $item_id, 'printify_print_providers_item_status', true );
	                $item_tracking_nr = get_post_meta( $item_id, 'printify_print_providers_item_tracking_nr', true );
	                $item_carrier = get_post_meta( $item_id, 'printify_print_providers_item_carrier', true );

	                $allow_html = $this->wocs_get_allow_html_in_escaping();

	                echo wp_kses( //Building satus selectbox output
                		'<div class="printify-print-providers-status">
            				<label class="printify-print-providers-label" for="printify_print_providers_item_status_' . esc_attr__( $item_id ) . '">' . esc_html__( 'Status: ', 'printify-print-providers' ) . '</label>
            					<select class="wc-enhanced-select" id="printify_print_providers_item_status_' . esc_attr__( $item_id ) . '" name="printify_print_providers_item_status_' . esc_attr__( $item_id ) . '">',$allow_html);
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
		            	echo wp_kses('</select>',$allow_html);

			            if($item_status == 7){ //Display Tracking fields if status is set to Shipped
		                	echo wp_kses(
		                	'<label class="printify-print-providers-label" for="printify_print_providers_item_tracking_nr_' . esc_attr__( $item_id ) . '">' . esc_html__( 'Tracking Nr.: ', 'printify-print-providers' ) . '</label>
		                		<input id="printify_print_providers_item_tracking_nr_' . esc_attr__( $item_id ) . '" name="printify_print_providers_item_tracking_nr_' . esc_attr__( $item_id ) . '" type="text" value="'. esc_attr__($item_tracking_nr) .'" class="short">
		                		<label class="printify-print-providers-label" for="printify_print_providers_item_carrier_' . esc_attr__( $item_id ) . '">' . esc_html__( 'Carrier: ', 'printify-print-providers' ) . '</label>
		                		<input id="printify_print_providers_item_carrier_' . esc_attr__( $item_id ) . '" name="printify_print_providers_item_carrier_' . esc_attr__( $item_id ) . '" type="text" value="'. esc_attr__($item_carrier) .'" class="short">', $allow_html);
		                }

	                echo wp_kses('</div>',$allow_html);

	                //Retrieving Printify print and preview file links
	                $order = wc_get_order();
					$order_data = $order->get_data();
					$line_items = $order_data['line_items'];

					//Building url file output
					foreach ($line_items as $key => $line_item) {
						if($key == $item_id){
							$line_item_meta_data = $line_item->get_meta_data();

							foreach ($line_item_meta_data as $key => $data) {
								$data = $data->get_data();
								if($data['key'] == 'printify_files'){
									$printify_files = $data['value'];
									foreach ($printify_files as $key => $file) {
										if($key == 'print_files'){
											echo '<div class="printify-print-providers-print-files"><span class="printify-print-providers-label">Print files:</span>';
											foreach ($file as $key => $url){
												echo '<a class="button" href="' . $url . '" target="_blank" >' . $key . '</a>';
											}
											echo '</div>';
										}
										if($key == 'preview_files'){
											echo '<div class="printify-print-providers-preview-files"><span class="printify-print-providers-label">Preview files:</span>';
											foreach ($file as $key => $url){
												echo '<a href="' . $url . '" target="_blank"><img src="' . $url . '"/></a>';
											}
										}
									}
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
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}// Exit if it's an autosave
		$custom_action    = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
		$custom_post_type = filter_input( INPUT_POST, 'post_type', FILTER_SANITIZE_STRING );
		$order_item_id    = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
		$order_item_id    = ( isset( $order_item_id['order_item_id'] ) && ! empty( $order_item_id['order_item_id'] ) ) ? $order_item_id['order_item_id'] : array();

		if ('editpost' === $custom_action && 'shop_order' === $custom_post_type) {
			if ( isset( $order_item_id ) && is_array( $order_item_id )) {
				foreach ( $order_item_id as $item_id ){

					//Retrieving data both from post saving and one that is saved in the database
					$item_status = get_post_meta( $item_id, 'printify_print_providers_item_status', true );
					$item_statu_data = filter_input( INPUT_POST, 'printify_print_providers_item_status_' . $item_id, FILTER_SANITIZE_STRING );
					$item_tracking_nr = get_post_meta( $item_id, 'printify_print_providers_item_tracking_nr', true );
					$item_tracking_nr_data = filter_input( INPUT_POST, 'printify_print_providers_item_tracking_nr_' . $item_id, FILTER_SANITIZE_STRING );
					$item_carrier = get_post_meta( $item_id, 'printify_print_providers_item_carrier', true );
					$item_carrier_data = filter_input( INPUT_POST, 'printify_print_providers_item_carrier_' . $item_id,FILTER_SANITIZE_STRING );
					
					//In case the value have been changed, save them in the database
					if($item_status !== $item_statu_data){ 
						update_post_meta( $item_id, 'printify_print_providers_item_status', $item_statu_data );
					}
					if($item_tracking_nr !== $item_tracking_nr_data){
						update_post_meta( $item_id, 'printify_print_providers_item_tracking_nr', $item_tracking_nr_data);
					}
					if($item_carrier !== $item_carrier_data){
						update_post_meta( $item_id, 'printify_print_providers_item_carrier', $item_carrier_data);
					}
				}
			}
		}
	}


	public function wocs_get_allow_html_in_escaping(){
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
	 * Function checks if the order arriving from Printify exists in the database or not and rturns orders 
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
}
