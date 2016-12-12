<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WC_Bookings_Authority_Registration_Settings
 */
if ( ! class_exists( 'WC_Bookings_Authority_Registration_Settings' ) ) {

	class WC_Bookings_Authority_Registration_Settings extends WC_Settings_Page {
	
		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id    = 'bookings_authority_registration';
			$this->label = __( 'Bookings', 'woocommerce-bookings-authority-registration' );
	
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
		}
	
		/**
		 * Get settings array
		 *
		 * @return array
		 */
		public function get_settings() {
	
			return apply_filters( 'woocommerce_' . $this->id . '_settings', array(
	
				array(
					'id'		=> 'woocommerce_bookings_authority_registration_options',
					'title'		=> __( 'Bookings', 'woocommerce-bookings-authority-registration' ),
					'desc'		=> __( '', 'woocommerce-bookings-authority-registration' ),
					'type'		=> 'title'
				),
	
				array(
					'id'			=> 'woocommerce_bookings_authority_registration_option_mode',
					'title'			=> __( 'Mode', 'woocommerce-bookings-authority-registration' ),
					'description'	=> __( 'By default all bookings are getting reported to the public authority. Set this option to "public" to notify the public authority only for bookings marked as public. With that option a new control will get added to the "add to cart" area of the product single page, where the customer can choose if that respective product gets used for public.', 'woocommerce-bookings-authority-registration' ),
					'desc_tip'		=>  true,
					'type'			=> 'select',
					'options'		=> array(
						'all'		=> __( 'All', 'woocommerce-bookings-authority-registration' ),
						'public'	=> __( 'Public', 'woocommerce-bookings-authority-registration' )
					),
					'default'		=> 'all',
					'css' 			=> 'min-width:300px;',
				),
				
				array(
					'id'			=> 'woocommerce_bookings_authority_registration_option_public_location',
					'title'			=> __( 'Public: Location', 'woocommerce-bookings-authority-registration' ),
					'description'	=> __( 'Activate this to have the customer directly enter the planned location for using your product (within a public event). This will add a simple textinput and the entered value will get submitted within the booking registration notification. This is helpful if public authority also need to know where a specific item gets used in public.', 'woocommerce-bookings-authority-registration' ),
					'desc_tip'		=>  true,
					'type'			=> 'checkbox',
					'label'			=> __( 'Force Customers to enter the planned location', 'woocommerce-bookings-authority-registration' ),
					'default'		=> 'yes',
					'css' 			=> 'min-width:300px;',
				),
				
				array(
					'id'			=> 'woocommerce_bookings_authority_registration_option_public_note',
					'title'			=> __( 'Public: Note', 'woocommerce-bookings-authority-registration' ),
					'description'	=> __( 'This will get added to the "add to cart" area on single product pages.', 'woocommerce-bookings-authority-registration' ),
					'desc_tip'		=>  true,
					'type'			=> 'textarea',
					'css' 			=> 'min-width:300px; width: 100%; min-height: 160px;',
				),
				
				array(
					'type'			=> 'sectionend',
					'id'			=> 'woocommerce_bookings_authority_registration_options'
				)
	
			)); // End pages settings
			
		}
		
	}

}

return new WC_Bookings_Authority_Registration_Settings();