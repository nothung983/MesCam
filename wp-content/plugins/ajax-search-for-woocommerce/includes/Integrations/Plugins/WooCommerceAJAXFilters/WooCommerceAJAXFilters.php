<?php

namespace DgoraWcas\Integrations\Plugins\WooCommerceAJAXFilters;

use DgoraWcas\Helpers;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Integration with Advanced AJAX Product Filters
 *
 * Plugin URL: https://wordpress.org/plugins/woocommerce-ajax-filters/
 * Author: BeRocket
 */
class WooCommerceAJAXFilters {
	public function init() {
		if ( ! defined( 'BeRocket_AJAX_filters_version' ) ) {
			return;
		}
		if ( version_compare( BeRocket_AJAX_filters_version, '1.4.1.8' ) <= 0 ) {
			return;
		}

		add_filter( 'berocket_aapf_get_attribute_values_post__in_outside', array( $this, 'filterPostInIds' ), 20 );
		add_filter( 'dgwt/wcas/helpers/is_search_query', array( $this, 'markQueryToProcess' ), 10, 2 );
	}

	/**
	 * Passing our search results to plugin
	 *
	 * The plugin uses our products IDs to determine the values in the displayed filters.
	 *
	 * @param boolean|integer[] $post__in
	 *
	 * @return boolean|integer[]
	 */
	public function filterPostInIds( $post__in ) {
		global $wp_query;

		if ( $wp_query->get( 'dgwt_wcas', false ) === false ) {
			return $post__in;
		}

		$posts_ids = apply_filters( 'dgwt/wcas/search_page/result_post_ids', array() );

		if ( ! empty( $posts_ids ) ) {
			return $posts_ids;
		}

		return $post__in;
	}

	/**
	 * @since 1.27.0
	 */
	public function markQueryToProcess( $enabled, $query ) {
		if (
			$query->is_search() &&
			( $query->get( 'post_type' ) && is_string( $query->get( 'post_type' ) ) && $query->get( 'post_type' ) === 'product' ) &&
			Helpers::is_running_inside_class('BeRocket_AAPF_Widget', 20)
		) {
			$enabled = true;
		}

		return $enabled;
	}
}
