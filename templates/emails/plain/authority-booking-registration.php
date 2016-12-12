<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Authority booking registration email
 */

echo "= " . $email_heading . " =\n\n";
echo $email_message . "\n\n";

echo "__________________________________________________\n\n";

echo __( 'Details:', 'woocommerce-bookings-authority-registration' ) . "\n\n";

echo sprintf( __( 'Object: %s', 'woocommerce-bookings-authority-registration' ), $booking->get_product()->get_title() ) . "\n";
echo sprintf( __( 'Decision Number: %s', 'woocommerce-bookings-authority-registration' ), get_post_meta( $booking->get_product()->get_id(), '_bookings_authority_registration_decision_number', true ) ) . "\n";
echo sprintf( __( 'Start Date: %s', 'woocommerce-bookings-authority-registration' ), $booking->get_start_date() ) . "\n";
echo sprintf( __( 'End Date: %s', 'woocommerce-bookings-authority-registration' ), $booking->get_end_date() ) . "\n";
echo sprintf( __( 'Location: %s', 'woocommerce-bookings-authority-registration' ), 'event_location' ) . "\n";

echo "__________________________________________________\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
