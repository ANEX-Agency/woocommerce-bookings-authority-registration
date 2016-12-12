<?php

/**
 *
 * Plugin Name: 			WooCommerce Bookings - Authority Registration
 * Plugin URI: 				http://woothemes.com/products/woocommerce-bookings-authority-registration/
 *
 * Description: 			Adds additional notification which can be used for automatic authority registrations (and cancellations of registrations)
 * Version: 				1.0.0
 *
 * Author:					WooThemes
 * Author URI: 				http://woothemes.com
 *
 * Developer:				ANEX
 * Author URI: 				http://anex.at
 * Author Email: 			info@anex.at
 *
 * Text Domain: 			woocommerce-bookings-authority-registration
 * Domain Path: 			/lang/
 *
 * Bitbucket Plugin URI:	anex/woocommerce-bookings-authority-registration
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * WC_Bookings_Authority_Registration Base Class
 *
 * @since 1.0.0
 */

if( is_woocommerce_active() && ! class_exists( 'WC_Bookings_Authority_Registration' ) ) {
	
	class WC_Bookings_Authority_Registration {
		
		/**
		 * Constructor
		 */
		public function __construct() {

			$this->type = 'wc_booking';
			
			define( 'WC_BOOKING_AUTHORITY_REGISTRATION_VERSION', '1.0.0' );
			define( 'WC_BOOKING_AUTHORITY_REGISTRATION_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
			define( 'WC_BOOKING_AUTHORITY_REGISTRATION_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
			define( 'WC_BOOKING_AUTHORITY_REGISTRATION_MAIN_FILE', __FILE__ );
			

			/**
			 * Actions & Filters
			 */
			
			// internationalize the text strings used.
			add_action( 'init', array( $this, 'i18n' ), 2 );
			
			// init additional functions
			add_action( 'plugins_loaded', array( $this, 'init' ) );
			
			// load assets
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ), 5 );

			// Add item data to the cart
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 20, 2 );

			add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 20, 2 );

			add_filter( 'woocommerce_attribute_label', array( $this, 'get_meta_label' ), 10, 2);

			add_filter( 'woocommerce_order_item_display_meta_value', array( $this, 'format_value' ) );

				// Admin Columns
				add_filter( 'manage_edit-' . $this->type . '_columns', array( $this, 'admin_edit_columns' ), 15 );
				add_action( 'manage_' . $this->type . '_posts_custom_column', array( $this, 'admin_custom_columns' ), 2 );
			
		}
		
		public function init() {
			
			/**
			 * Include additional files
			 */
			
			// Include our integration class.
			@include_once __DIR__ . '/includes/class-wc-booking-authority-registration-email-manager.php';
			
			/**
			 * Filters & Actions
			 */

			// include custom booking controls
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'control' ), 5000 );

			// add custom product fields
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_meta' ) );
			
			// save custom product fields
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_meta' ) );

			// Add settings page
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings_page' ) );
			
		}
	
	
		
		/**
		 * Get the plugin path.
		 *
		 * @return string
		 * @since  2.0
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}


	
		/**
		 * Load textdomain for internationalization
		 */
		public function i18n() {
	
			$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-bookings-authority-registration' );
			$dir    = trailingslashit( WP_LANG_DIR );
	
			load_textdomain( 'woocommerce-bookings-authority-registration', $dir . 'woocommerce-bookings-authority-registration/woocommerce-bookings-authority-registration-' . $locale . '.mo' );
			load_plugin_textdomain( 'woocommerce-bookings-authority-registration', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			
		}
		

		
		/**
		 *
		 * Registers and enqueues plugin-specific assets.
		 *
		 * @since	0.1.0
		 * @access	public
		 *
		 */
		static function assets() {
			
			wp_enqueue_script( 'woocommerce-bookings-authority-registration', plugins_url( 'woocommerce-bookings-authority-registration/assets/js/script.js' ), array( 'jquery' ), WC_BOOKING_AUTHORITY_REGISTRATION_VERSION, true );
	
		}
		
		public function options() {
			
			$options = array();
			
			$options['mode']			= get_option( 'woocommerce_bookings_authority_registration_option_mode' );
			$options['public_note']		= get_option( 'woocommerce_bookings_authority_registration_option_public_note' );
			$options['event_location']	= get_option( 'woocommerce_bookings_authority_registration_option_public_location' );

			return $options;			
			
		}

		

		public function control() {
			
			global $post;
			
			// get options
			$options = $this->options();
			
			// return if not public
			if( $options['mode'] != 'public' )
				return;
			
			if( function_exists( 'get_product' ) ) {
				
				$product = get_product( $post->ID );
				
				if( $product->is_type( 'booking' ) ) {
					
					wc_get_template(
						'single-product/authority-registration.php',
						array( 'options' => $options ),
						FALSE,
						$this->plugin_path() . '/templates/' );
					
				}
				
			}
			
		}
		
		
		
		/**
		 * Add Metabox
		 */
		public function add_meta() {
	
			global $woocommerce, $post;
			
			echo '<div class="options_group show_if_booking">';
			
			// Decision Number
			woocommerce_wp_text_input( 
				array( 
					'id'          => '_bookings_authority_registration_decision_number', 
					'label'       => __( 'Decision Number', 'woocommerce-bookings-authority-registration' ), 
					'description' => __( 'Enter the number of the decision for this product here', 'woocommerce-bookings-authority-registration' ), 
					'desc_tip'    => 'true',
					'placeholder' => 'eg. M36/34930/2008/2'
				)
			);
			
			echo '</div>';
	
		}
		
		
		
		/**
		 * Save Metabox
		 */
		public function save_meta( $post_id ) {
	
			// Decision Number
			$decision_number = $_POST['_bookings_authority_registration_decision_number'];
			
			if( !empty( $decision_number ) )
				update_post_meta( $post_id, '_bookings_authority_registration_decision_number', esc_attr( $decision_number ) );
	
		}
		
		/**
		 * Add a new integration to WooCommerce.
		 */
		public function add_settings_page( $settings ) {
			
			$settings[] = @include_once __DIR__ . '/includes/class-wc-booking-authority-registration-settings.php';
			return $settings;
			
		}

		public function add_cart_item_data( $cart_item_meta, $product_id, $post_data = null, $test = false ) {
			
			if ( is_null( $post_data ) && isset( $_POST ) )
				$post_data = $_POST;

			if( isset( $post_data['event-type'] ) && $post_data['event-type'] == 'public' && isset( $post_data['event-location'] ) ) {
				
				$cart_item_meta['event_type']		= 'public';
				$cart_item_meta['event_location']	= sanitize_text_field( $post_data['event-location'] );
				
			} elseif( isset( $post_data['event-type'] ) && $post_data['event-type'] == 'private' ) {
				
				$cart_item_meta['event_type']		= 'private';
				
			} else {
				
				$cart_item_meta['event_type']		= '';
				
			}

			return $cart_item_meta;

		}

		public function order_item_meta( $item_id, $values ) {

			$key = 'event_type';

			if( isset( $values[$key] ) )
				wc_add_order_item_meta( $item_id, $key, $values[$key] );

			$key = 'event_location';

			if( isset( $values[$key] ) )
				wc_add_order_item_meta( $item_id, $key, $values[$key] );

		}

		public function get_meta_label($label, $name) {
			
			if( $name == 'event_type' )
				return __( 'Event Type', 'woocommerce-bookings-authority-registration' );

			if( $name == 'event_location' )
				return __( 'Event Location', 'woocommerce-bookings-authority-registration' );

			return $label;
		}

		public function format_value( $val ) {
			
			//var_dump( $val );
			
			if( $val == 'public' )
				return __( 'Public', 'woocommerce-bookings-authority-registration' );

			if( $val == 'private' )
				return __( 'Private', 'woocommerce-bookings-authority-registration' );

			return $val;
			
		}

		public function admin_edit_columns( $columns ) {
			
			return $this->array_insert_after( 'end_date', $columns, 'event_location', __( 'Location' ) );
			
		}

		public function admin_custom_columns( $column ) {
			
			if( $column != 'event_location' )
				return;

			global $post, $booking;

			if ( empty( $booking ) || $booking->id != $post->ID ) {
					$booking = get_wc_booking( $post->ID );
			}

			$item_id = $booking->custom_fields['_booking_order_item_id'][0];

			$items = $booking->order->get_items();

			$item = $items[$item_id];

			$public = isset($item['item_meta']['event_type']) && $item['item_meta']['event_type'][0] == 'public';

			if( $public )
				echo isset($item['item_meta']['event_location']) ? $item['item_meta']['event_location'][0] : '?';
			else
				echo __( 'N/A', 'woocommerce-bookings-authority-registration' );

		}
		
		/*
		 * Inserts a new key/value before the key in the array.
		 *
		 * @param $key
		 *   The key to insert before.
		 * @param $array
		 *   An array to insert in to.
		 * @param $new_key
		 *   The key to insert.
		 * @param $new_value
		 *   An value to insert.
		 *
		 * @return
		 *   The new array if the key exists, FALSE otherwise.
		 *
		 * @see array_insert_after()
		 */
		function array_insert_before($key, array &$array, $new_key, $new_value) {
		  if (array_key_exists($key, $array)) {
			$new = array();
			foreach ($array as $k => $value) {
			  if ($k === $key) {
				$new[$new_key] = $new_value;
			  }
			  $new[$k] = $value;
			}
			return $new;
		  }
		  return false;
		}

		/*
		 * Inserts a new key/value after the key in the array.
		 *
		 * @param $key
		 *   The key to insert after.
		 * @param $array
		 *   An array to insert in to.
		 * @param $new_key
		 *   The key to insert.
		 * @param $new_value
		 *   An value to insert.
		 *
		 * @return
		 *   The new array if the key exists, FALSE otherwise.
		 *
		 * @see array_insert_before()
		 */
		function array_insert_after($key, array &$array, $new_key, $new_value) {
		  if (array_key_exists($key, $array)) {
			$new = array();
			foreach ($array as $k => $value) {
			  $new[$k] = $value;
			  if ($k === $key) {
				$new[$new_key] = $new_value;
			  }
			}
			return $new;
		  }
		  return false;
		}
								
	}
	
}

// instantiate class
$GLOBALS['wc_bookings_authority_registration'] = new WC_Bookings_Authority_Registration();