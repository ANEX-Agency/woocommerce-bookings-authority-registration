jQuery(document).ready(function ($) {

	$('body').on( 'woocommerce-product-type-change', function( event, type ) {
		
		if ( type !== 'booking' ) {
			console.log( 'NOT BOOKING' );
		}
		
		console.log( type );
		
	});

});