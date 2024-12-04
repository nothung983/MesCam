<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Reload metabox data on variation update
 */
function swatchly_ajax_reload_metabox_panel(){
    // Verify nonce
    if(!empty($_REQUEST['nonce'])) {
        $nonce = sanitize_text_field(wp_unslash($_REQUEST['nonce']));
    }
    if ( !wp_verify_nonce( $nonce, 'swatchly_product_metabox_save_nonce' ) ) {
        wp_send_json_error(array(
            'message' => esc_html__( 'No naughty business please!', 'swatchly' )
        ));
    }

    if(!empty($_REQUEST['product_id'])) {
        $product_id = absint($_REQUEST['product_id']);
    }
    $product    = wc_get_product($product_id);
    if ( $product && !$product->is_type('variable') ) {
        wp_die( -1 );
    }

    // Panel inner HTML
    ob_start();
    \Swatchly\Admin\Product_Metabox::metabox_panel_inner_html($product_id);
    $html = ob_get_clean();
    
    wp_send_json_success($html);
}
add_action( 'wp_ajax_swatchly_ajax_reload_metabox_panel', 'swatchly_ajax_reload_metabox_panel' );

/**
 * Save product metabox data (ajax)
 */
function swatchly_ajax_save_product_meta(){
    // verify nonce
    if(!empty($_REQUEST['nonce'])) {
        $nonce = sanitize_text_field(wp_unslash($_REQUEST['nonce']));
    }
    if ( !wp_verify_nonce( $nonce, 'swatchly_product_metabox_save_nonce' ) ) {
        wp_send_json_error(array(
            'message' => esc_html__( 'No naughty business please!', 'swatchly' )
        ));
    }

    // check current user privilege
    if ( !current_user_can( 'edit_products' ) ) {
        wp_die( -1 );
    }

    if(!empty($_REQUEST['product_id'])) {
        $product_id = absint(wp_unslash($_REQUEST['product_id']));
    }
    $product    = wc_get_product($product_id);
    if ( $product && !$product->is_type('variable') ) {
        wp_die( -1 );
    }

    if(!empty($_REQUEST['input_fields']['swatchly_product_meta'])) {
        $meta_data  = map_deep( wp_unslash( $_REQUEST['input_fields']['swatchly_product_meta'] ), 'sanitize_text_field' );
    }
    $updated    = update_post_meta( $product_id, '_swatchly_product_meta', $meta_data );

    wp_send_json_success(array(
        'message' => esc_html__('Saved!', 'swatchly')
    ));
}
add_action( 'wp_ajax_swatchly_ajax_save_product_meta', 'swatchly_ajax_save_product_meta' );

/**
 * Reset product metabox data (ajax)
 */
function swatchly_ajax_reset_product_meta(){
    // verify nonce
    if(!empty($_REQUEST['nonce'])){
        $nonce = sanitize_text_field(wp_unslash($_REQUEST['nonce']));
    }
    if ( !wp_verify_nonce( $nonce, 'swatchly_product_metabox_save_nonce' ) ) {
        wp_send_json_error(array(
            'message' => esc_html__( 'No naughty business please!', 'swatchly' )
        ));
    }

    // check current user privilege
    if ( !current_user_can( 'edit_products' ) ) {
        wp_die( -1 );
    }

    if(!empty($_REQUEST['product_id'])) {
        $product_id = absint($_REQUEST['product_id']);
    }
    $updated    = update_post_meta( $product_id, '_swatchly_product_meta', '' );

    wp_send_json_success(array(
        'message' => esc_html__('Reset Done!', 'swatchly')
    ));
}
add_action( 'wp_ajax_swatchly_ajax_reset_product_meta', 'swatchly_ajax_reset_product_meta' );