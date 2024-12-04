<?php

use DgoraWcas\Helpers;

// Exit if accessed directly.
if ( ! defined( 'DGWT_WCAS_FILE' ) ) {
	exit;
}

global $dgwtWcasBricksStyles;

$dgwtWcasBricksStyles = '';

/**
 * Support for Bricks custom pagination parameter.
 */
add_action( 'pre_get_posts', function ($query) {
	if ( ! Helpers::isSearchQuery( $query ) ) {
		return;
	}

	if ( ! empty( $_GET['product-page'] ) && intval($_GET['product-page']) > 0 ) {
		$query->set('paged', intval($_GET['product-page']));
	}
} , 900000 );

/**
 * This filter should return true or false depending on whether the Element is to be displayed,
 * but we use it to override the search Element. This is not entirely the correct way to use a filter,
 * but we have no other way to override the Element's rendering function.
 */
add_filter( 'bricks/element/render', function ( $render_element, $element ) {
	global $dgwtWcasBricksStyles;

	if ( ! $render_element ) {
		return $render_element;
	}
	if ( ! isset( $element->block ) || $element->block !== 'core/search' ) {
		return $render_element;
	}

	if ( isset( $element->settings['searchType'] ) && $element->settings['searchType'] === 'overlay' ) {
		echo do_shortcode( '[fibosearch layout="icon"]' );

		if ( ! empty( $element->settings['iconTypography']['color']['hex'] ) ) {
			ob_start();
			?>
			<style>
				.dgwt-wcas-ico-magnifier, .dgwt-wcas-ico-magnifier-handler {
					fill: <?php echo esc_attr( $element->settings['iconTypography']['color']['hex'] ) ?>;
				}
			</style>
			<?php
			$dgwtWcasBricksStyles .= ob_get_clean();
		}
	} else {
		echo do_shortcode( '[fibosearch]' );
	}

	return false;
}, 10, 2 );

add_action( 'wp_footer', function () {
	global $dgwtWcasBricksStyles;

	if ( ! empty( $dgwtWcasBricksStyles ) ) {
		echo $dgwtWcasBricksStyles;
	}
} );
