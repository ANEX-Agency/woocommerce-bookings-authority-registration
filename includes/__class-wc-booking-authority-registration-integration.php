<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Integration Demo Integration.
 *
 * @package  WC_Integration_Demo_Integration
 * @category Integration
 * @author   WooThemes
 */

if ( ! class_exists( 'WC_Bookings_Authority_Registration_Integration' ) ) {

	class WC_Bookings_Authority_Registration_Integration extends WC_Integration {
	
		/**
		 * Init and hook in the integration.
		 */
		public function __construct() {
			
			global $woocommerce;
	
			$this->id                 = 'bookings_authority_registration';
			$this->method_title       = __( 'Booking Authority Registration', 'woocommerce-bookings-authority-registration' );
			$this->method_description = __( '', 'woocommerce-bookings-authority-registration' );
	
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();
	
			// Define user set variables.
			$this->api_key	= $this->get_option( 'api_key' );
			$this->debug	= $this->get_option( 'debug' );
	
			// Actions.
			add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
	
			// Filters.
			add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitize_settings' ) );
	
		}
	
	
		/**
		 * Initialize integration settings form fields.
		 *
		 * @return void
		 */
		public function init_form_fields() {
			
			$this->form_fields = array(
			
				'mode' => array(
					'title'			=> __( 'Mode', 'woocommerce-bookings-authority-registration' ),
					'description'	=> __( 'By default all bookings are getting reported to the public authority. Set this option to "public" to notify the public authority only for bookings marked as public. With that option a new control will get added to the "add to cart" area of the product single page, where the customer can choose if that respective product gets used for public.', 'woocommerce-bookings-authority-registration' ),
					'desc_tip'		=>  true,
					'type'			=> 'select',
					'options'		=> array(
						'all'		=> __( 'All', 'woocommerce-bookings-authority-registration' ),
						'public'	=> __( 'Public', 'woocommerce-bookings-authority-registration' )
					),
					'default'		=> 'all'
				),
				
				'public_location' => array(
					'title'			=> __( 'Public: Location', 'woocommerce-bookings-authority-registration' ),
					'description'	=> __( 'Activate this to have the customer directly enter the planned location for using your product (within a public event). This will add a simple textinput and the entered value will get submitted within the booking registration notification. This is helpful if public authority also need to know where a specific item gets used in public.', 'woocommerce-bookings-authority-registration' ),
					'desc_tip'		=>  true,
					'type'			=> 'checkbox',
					'label'			=> __( 'Force Customers to enter the planned location', 'woocommerce-bookings-authority-registration' ),
					'default'		=> 'yes'
				),
			
				'public_note' => array(
					'title'			=> __( 'Public: Note', 'woocommerce-bookings-authority-registration' ),
					'description'	=> __( 'This will get added to the "add to cart" area on single product pages.', 'woocommerce-bookings-authority-registration' ),
					'desc_tip'		=>  true,
					'type'			=> 'textarea'
				),
			
//				'api_key' => array(
//					'title'             => __( 'API Key', 'woocommerce-integration-demo' ),
//					'type'              => 'text',
//					'description'       => __( 'Enter with your API Key. You can find this in "User Profile" drop-down (top right corner) > API Keys.', 'woocommerce-integration-demo' ),
//					'desc_tip'          => true,
//					'default'           => ''
//				),
//				'debug' => array(
//					'title'             => __( 'Debug Log', 'woocommerce-integration-demo' ),
//					'type'              => 'checkbox',
//					'label'             => __( 'Enable logging', 'woocommerce-integration-demo' ),
//					'default'           => 'no',
//					'description'       => __( 'Log events such as API requests', 'woocommerce-integration-demo' ),
//				),
//				'customize_button' => array(
//					'title'             => __( 'Customize!', 'woocommerce-integration-demo' ),
//					'type'              => 'button',
//					'custom_attributes' => array(
//						'onclick' => "location.href='http://www.woothemes.com'",
//					),
//					'description'       => __( 'Customize your settings by going to the integration site directly.', 'woocommerce-integration-demo' ),
//					'desc_tip'          => true,
//				)

			);
			
		}
	
	
		/**
		 * Generate Button HTML.
		 */
		public function generate_button_html( $key, $data ) {
			
			$field    = $this->plugin_id . $this->id . '_' . $key;
			$defaults = array(
				'class'             => 'button-secondary',
				'css'               => '',
				'custom_attributes' => array(),
				'desc_tip'          => false,
				'description'       => '',
				'title'             => '',
			);
	
			$data = wp_parse_args( $data, $defaults );
	
			ob_start();
			
			?>
            
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
					<?php echo $this->get_tooltip_html( $data ); ?>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
						<button class="<?php echo esc_attr( $data['class'] ); ?>" type="button" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php echo $this->get_custom_attribute_html( $data ); ?>><?php echo wp_kses_post( $data['title'] ); ?></button>
						<?php echo $this->get_description_html( $data ); ?>
					</fieldset>
				</td>
			</tr>
            
			<?php
			
			return ob_get_clean();
		}
	
	
		/**
		 * Santize our settings
		 * @see process_admin_options()
		 */
		public function sanitize_settings( $settings ) {
			
			// We're just going to make the api key all upper case characters since that's how our imaginary API works
			if ( isset( $settings ) &&
				 isset( $settings['api_key'] ) ) {
				$settings['api_key'] = strtoupper( $settings['api_key'] );
			}
			
			return $settings;
			
		}
	
	
		/**
		 * Validate the API key
		 * @see validate_settings_fields()
		 */
		public function validate_api_key_field( $key ) {
			
			// get the posted value
			$value = $_POST[ $this->plugin_id . $this->id . '_' . $key ];
	
			// check if the API key is longer than 20 characters. Our imaginary API doesn't create keys that large so something must be wrong. Throw an error which will prevent the user from saving.
			if ( isset( $value ) &&
				 20 < strlen( $value ) ) {
				$this->errors[] = $key;
			}
			
			return $value;
			
		}
	
	
		/**
		 * Display errors by overriding the display_errors() method
		 * @see display_errors()
		 */
		public function display_errors( ) {
	
			// loop through each error and display it
			foreach ( $this->errors as $key => $value ) {
				
				?>
                
				<div class="error">
					<p><?php _e( 'Looks like you made a mistake with the ' . $value . ' field. Make sure it isn&apos;t longer than 20 characters', 'woocommerce-integration-demo' ); ?></p>
				</div>
                
				<?php
				
			}
			
		}
	
	}

}
