<?php

namespace DgoraWcas\Integrations\Plugins\WooCommerceWholeSalePricesIntegration;

use DgoraWcas\Helpers;
use DgoraWcas\Multilingual;
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Integration with WooCommerce Wholesale Prices
 *
 * Plugin URL: https://wholesalesuiteplugin.com
 * Author: Rymera Web Co
 */
class WooCommerceWholeSalePricesIntegration {
    public function init() {
        if ( !class_exists( 'WooCommerceWholeSalePricesPremium' ) ) {
            return;
        }
        if ( version_compare( \WooCommerceWholeSalePricesPremium::VERSION, '1.24.4' ) < 0 ) {
            return;
        }
        add_filter( 'dgwt/wcas/search_query/args', array($this, 'filterSearchQueryArgs') );
        add_filter( 'dgwt/wcas/search/product_cat/args', array($this, 'filterProductCatArgs') );
        add_filter( 'dgwt/wcas/troubleshooting/renamed_plugins', array($this, 'getFolderRenameInfo') );
    }

    /**
     * Exclude hidden products from search results (native engine)
     *
     * @param array $args
     *
     * @return array
     */
    public function filterSearchQueryArgs( $args ) {
        global $wc_wholesale_prices_premium;
        if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) ) {
            return $args;
        }
        return $wc_wholesale_prices_premium->wwpp_query->pre_get_posts_arg( $args );
    }

    /**
     * Exclude hidden categories from search results (native engine)
     *
     * @param array $args
     *
     * @return array
     */
    public function filterProductCatArgs( $args ) {
        global $wc_wholesale_prices_premium;
        if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) ) {
            return $args;
        }
        $postsArgs = array(
            'tax_query' => array(),
        );
        $postsArgs = $wc_wholesale_prices_premium->wwpp_query->pre_get_posts_arg( $postsArgs );
        if ( !isset( $args['exclude'] ) ) {
            $args['exclude'] = array();
        }
        $args['exclude'] = array_merge( $args['exclude'], $this->getExcludedCategoryIds( $postsArgs ) );
        return $args;
    }

    /**
     * Get info about renamed plugin folder
     *
     * @param array $plugins
     *
     * @return array
     */
    public function getFolderRenameInfo( $plugins ) {
        $filters = new Filters();
        $result = Helpers::getFolderRenameInfo__premium_only( 'WooCommerce Wholesale Prices Premium', $filters->plugin_names );
        if ( $result ) {
            $plugins[] = $result;
        }
        return $plugins;
    }

    private function getExcludedCategoryIds( $postsArgs ) {
        $categoryIds = array();
        if ( !empty( $postsArgs['tax_query'] ) ) {
            foreach ( $postsArgs['tax_query'] as $taxQuery ) {
                if ( isset( $taxQuery['taxonomy'] ) && $taxQuery['taxonomy'] === 'product_cat' && isset( $taxQuery['operator'] ) && $taxQuery['operator'] === 'NOT IN' ) {
                    $categoryIds = $taxQuery['terms'];
                }
            }
        }
        return $categoryIds;
    }

}
