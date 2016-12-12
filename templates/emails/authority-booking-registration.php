<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Authority booking registration email
 */

?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<p><?php echo $email_message; ?></p>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<tbody>
		<tr>
			<th scope="row" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Object', 'woocommerce-bookings-authority-registration' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo $booking->get_product()->get_title(); ?></td>
		</tr>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php _e( 'Decision Number', 'woocommerce-bookings-authority-registration' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo get_post_meta( $booking->get_product()->get_id(), '_bookings_authority_registration_decision_number', true ); ?></td>
		</tr>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php _e( 'Start Date', 'woocommerce-bookings-authority-registration' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo $booking->get_start_date(); ?></td>
		</tr>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php _e( 'End Date', 'woocommerce-bookings-authority-registration' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo $booking->get_end_date(); ?></td>
		</tr>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php _e( 'Location', 'woocommerce-bookings-authority-registration' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;">event_location</td>
		</tr>
	</tbody>
</table>

<?php do_action( 'woocommerce_email_footer' ); ?>
