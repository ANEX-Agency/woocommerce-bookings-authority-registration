<?php
/**
 * Minimum Price Template
 *
 * @author 		Kathy Darling
 * @package 	WC_Name_Your_Price/Templates
 * @version     2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<div class="product-addon" data-title="<?php echo $options['public_note']; ?>" data-tooltip>
						
    <h3 class="addon-name"><?php _e( 'Event Type', 'woocommerce-bookings-authority-registration' ) ?></h3>
    
    <div class="form-row form-row-wide">
    
        <div class="switcher switcher-radio switcher-block switcher-grid-2 switcher-kolarik">
            <input id="event-type-private" name="event-type" type="radio" value="private" checked />
            <label for="event-type-private"><?php _e( 'Private', 'woocommerce-bookings-authority-registration' ) ?></label>
            <input id="event-type-public" name="event-type" type="radio" value="public" />
            <label for="event-type-public"><?php _e( 'Public', 'woocommerce-bookings-authority-registration' ) ?></label>										
            <a class="switcher-active"></a>					
        </div>
    
    </div>
    
    <?php if( $options['event_location'] == 'yes' ) { ?>
    
        <div class="form-row form-row-wide form-row-toggle form-row-toggle-hide">
            <input id="event-location" class="regular-text" type="text" name="event-location" value="" placeholder="<?php _e( 'Enter Location', 'woocommerce-bookings-authority-registration' ) ?>">
        </div>
    
    <?php } ?>

</div>