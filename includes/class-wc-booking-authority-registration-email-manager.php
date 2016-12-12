<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handles email sending
 */
if ( ! class_exists( 'WC_Booking_Authority_Registration_Email_Manager' ) ) {

	class WC_Booking_Authority_Registration_Email_Manager {
	
		/**
		 * Constructor sets up actions
		 */
		public function __construct() {
			
			add_filter( 'woocommerce_email_classes', array( $this, 'init_emails' ) );
			
			add_filter( 'woocommerce_template_directory', array( $this, 'template_directory' ), 10, 2 );
			
		}
	
		/**
		 * Include our mail templates
		 *
		 * @param  array $emails
		 * @return array
		 */
		public function init_emails( $emails ) {
			
			if ( ! isset( $emails['WC_Email_Booking_Registration'] ) )
				$emails['WC_Email_Booking_Registration'] = include( 'emails/class-wc-email-booking-registration.php' );
	
			if ( ! isset( $emails['WC_Email_Booking_Cancellation'] ) )
				$emails['WC_Email_Booking_Cancellation'] = include( 'emails/class-wc-email-booking-cancellation.php' );
	
			return $emails;
		}
	
		/**
		 * Custom template directory.
		 *
		 * @param  string $directory
		 * @param  string $template
		 *
		 * @return string
		 */
		public function template_directory( $directory, $template ) {
			
			if ( false !== strpos( $template, '-booking' ) ) {
				return 'woocommerce-bookings-authority-registration';
			}
	
			return $directory;
			
		}
		
	}

}

new WC_Booking_Authority_Registration_Email_Manager();