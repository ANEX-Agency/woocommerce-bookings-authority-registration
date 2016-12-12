<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Booking Registration Cancellation Email
 *
 * An email sent to a custom email (authority) when a booking has been cancelled.
 *
 * @class       WC_Email_Booking_Cancellation
 * @extends     WC_Email
 */
if ( ! class_exists( 'WC_Email_Booking_Cancellation' ) ) {
	
	class WC_Email_Booking_Cancellation extends WC_Email {
		
		/**
		 * Constructor
		 */
		function __construct() {
	
			$this->id                   = 'booking_cancellation';
			$this->title                = __( 'Booking Cancellation', 'woocommerce-bookings-authority-registration' );
			$this->description          = __( 'Booking Cancellation emails are sent to the authority when a pending, confirmed or already paid booking gets cancelled.', 'woocommerce-bookings-authority-registration' );
	
			$this->heading              = __( 'Booking Cancellation', 'woocommerce-bookings-authority-registration' );
			$this->subject              = __( 'Cancel Registration for "{product_title}"', 'woocommerce-bookings-authority-registration' );
			$this->message              = __( 'We like to inform you about the cancelation of {product_title}', 'woocommerce-bookings-authority-registration' );
			
			$this->message     			= $this->get_option( 'message', $this->message );

			$this->template_html    	= 'emails/authority-booking-cancellation.php';
			$this->template_plain   	= 'emails/plain/authority-booking-cancellation.php';
	
			// Triggers for this email
			add_action( 'woocommerce_booking_pending-confirmation_to_cancelled', array( $this, 'trigger' ) );
			add_action( 'woocommerce_booking_confirmed_to_cancelled', array( $this, 'trigger' ) );
			add_action( 'woocommerce_booking_paid_to_cancelled', array( $this, 'trigger' ) );
	
			// Call parent constructor
			parent::__construct();
	
			// Other settings
			$this->template_base = WC_BOOKING_AUTHORITY_REGISTRATION_TEMPLATE_PATH;
			$this->recipient     = $this->get_option( 'recipient', get_option( 'admin_email' ) );
		}
	
		/**
		 * trigger function.
		 */
		function trigger( $booking_id ) {
						
			if ( $booking_id ) {
				
				$this->object = get_wc_booking( $booking_id );
				$order = $this->object->get_order();

				$item_id = $this->object->custom_fields['_booking_order_item_id'][0];

				$items = $order->get_items();

				$item = $items[$item_id];

				$public   = isset( $item['item_meta']['event_type'] ) ? $item['item_meta']['event_type'][0] : '';
				$location = isset( $item['item_meta']['event_location'] ) ? $item['item_meta']['event_location'][0] : '';
				
				if( ! $public || $public == 'private' )
					return;
	
				$key = array_search( '{product_title}', $this->find );
				
				if ( false !== $key ) {
					unset( $this->find[ $key ] );
					unset( $this->replace[ $key ] );
				}
	
				$this->find[]    = '{product_title}';
				$this->replace[] = $this->object->get_product()->get_title();
				
				$this->find[]    = '{event_location}';
				$this->replace[] = $location;
		
				if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
					return;
				}
				
				if( $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() ) ) {
				
					// add note
					$order->add_order_note( __( 'Booking Registration Cancellation has been successfully sent', 'woocommerce-bookings-authority-registration' ), false );
				
				} else {
					
					// add note
					$order->add_order_note( __( 'Booking Registration Cancellation couldn\'t be sent', 'woocommerce-bookings-authority-registration' ), false );
					
				}
	
			}
			
		}
	
		/**
		 * get_content_html function.
		 *
		 * @access public
		 * @return string
		 */
		function get_content_html() {
			
			ob_start();
			
			wc_get_template( $this->template_html, array(
				'booking'       => $this->object,
				'email_heading' => $this->get_heading(),
				'email_message' => $this->get_message()
			), 'woocommerce-bookings-authority-registration/', $this->template_base );
			
			$content = ob_get_clean();
			
			return preg_replace( $this->find, $this->replace, $content );
			
		}
	
		/**
		 * get_content_plain function.
		 *
		 * @access public
		 * @return string
		 */
		function get_content_plain() {
			
			ob_start();
			
			wc_get_template( $this->template_plain, array(
				'booking'       => $this->object,
				'email_heading' => $this->get_heading(),
				'email_message' => $this->get_message()
			), 'woocommerce-bookings-authority-registration/', $this->template_base );
			
			$content = ob_get_clean();
			
			return preg_replace( $this->find, $this->replace, $content );
			
		}
	
		/**
		 * get_message function.
		 *
		 * @return string
		 */
		public function get_message() {
			return apply_filters( 'woocommerce_email_message_' . $this->id, $this->format_string( $this->message ), $this->object );
		}
	
		/**
		 * Initialise Settings Form Fields
		 *
		 * @access public
		 * @return void
		 */
		function init_form_fields() {
			
			$this->form_fields = array(
				'enabled' => array(
					'title'         => __( 'Enable/Disable', 'woocommerce' ),
					'type'          => 'checkbox',
					'label'         => __( 'Enable this email notification', 'woocommerce' ),
					'default'       => 'yes'
				),
				'recipient' => array(
					'title'         => __( 'Recipient(s)', 'woocommerce' ),
					'type'          => 'text',
					'description'   => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'woocommerce' ), esc_attr( get_option('admin_email') ) ),
					'desc_tip'      => true,
					'placeholder'   => '',
					'default'       => ''
				),
				'subject' => array(
					'title'         => __( 'Subject', 'woocommerce' ),
					'type'          => 'text',
					'description'   => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
					'desc_tip'      => true,
					'placeholder'   => '',
					'default'       => ''
				),
				'heading' => array(
					'title'         => __( 'Email Heading', 'woocommerce' ),
					'type'          => 'text',
					'description'   => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
					'desc_tip'      => true,
					'placeholder'   => '',
					'default'       => ''
				),
				'message' => array(
					'title'         => __( 'Email Message', 'woocommerce-bookings-authority-registration' ),
					'type'          => 'textarea',
					'description'   => sprintf( __( 'This controls the main body contained within the email notification. Leave blank to use the default body: <code>%s</code>.', 'woocommerce-bookings-authority-registration' ), $this->message ),
					'desc_tip'      => true,
					'placeholder'   => '',
					'default'       => ''
				),
				'email_type' => array(
					'title'         => __( 'Email type', 'woocommerce' ),
					'type'          => 'select',
					'description'   => __( 'Choose which format of email to send.', 'woocommerce' ),
					'default'       => 'html',
					'class'         => 'email_type wc-enhanced-select',
					'options'       => $this->get_email_type_options(),
					'desc_tip'      => true
				)
				
			);
			
		}
		
	}

}

return new WC_Email_Booking_Cancellation();
