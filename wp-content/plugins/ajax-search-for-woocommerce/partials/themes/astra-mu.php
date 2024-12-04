<?php
// Exit if accessed directly
if ( ! defined( 'DGWT_WCAS_FILE' ) ) {
	exit;
}

add_filter( 'dgwt/wcas/form/html', function ( $html ) {
	// We're removing the 'woocommerce' class on these pages because it makes it impossible to update the cart contents.
	if ( is_checkout() || is_cart() ) {
		return preg_replace( '/class="([0-9a-zA-Z-\s]+)woocommerce([0-9a-zA-Z-\s]+)"/m', "class=\"$1$2\"", $html );
	}

	return $html;
} );
