<?php
/**
 * Necessary functions of the plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Get global options value.
 *
 * @since 1.0.0
 *
 * @param string   $option_name Option name.
 * @param null $default Default value.
 * @param null $override_for Override global options for product list/product details page. Accepted values are: pl, sp
 *
 * @return string|null
 */
function swatchly_get_option( $option_name = '', $default = null, $override_for = null ) {
    $options = get_option( 'swatchly_options' );

    $global_general_setting_fields = array(
        'enable_swatches',
        'auto_convert_dropdowns_to_label',
        'swatch_width',
        'swatch_height',
        'tooltip',
        'show_swatch_image_in_tooltip',
        'tooltip_image_size',
        'shape_style',
        'enable_shape_inset',
        'shape_inset_size',
        'deselect_on_click',
        'show_selected_attribute_name',
        'disabled_attribute_type',
        'disable_out_of_stock',
    );

    if($override_for == 'sp' && isset($options['sp_override_global']) && $options['sp_override_global']){
        $opt_name = 'sp_'. $option_name;

        if(in_array($option_name, $global_general_setting_fields)){
            $option_name = $opt_name;
        }
    }

    if($override_for == 'pl' && isset($options['pl_override_global']) && $options['pl_override_global']){
        $opt_name = 'pl_'. $option_name;

        if(in_array($option_name, $global_general_setting_fields)){
            $option_name = $opt_name;
        }
    }

    return isset( $options[$option_name] ) ? $options[$option_name] : $default;
}

/**
 * Get product meta options value.
 *
 * @since 1.0.0
 *
 * @param int   $product_id Product ID.
 * @param string   $option_name Option name.
 * @param null $default Default value.
 *
 * @return string|null
 */
function swatchly_get_post_meta( $product_id, $taxonomy, $option_name = '', $default = '') {
    // product override
    $product_meta = get_post_meta( $product_id, '_swatchly_product_meta', true );

    $meta_value = isset( $product_meta[$taxonomy][$option_name] ) && $product_meta[$taxonomy][$option_name] ? $product_meta[$taxonomy][$option_name] : $default;

    return $meta_value;
}

/**
 * Get image sizes.
 *
 * @since 1.0.0
 *
 * @return array
 */
function swatchly_get_image_sizes() {
	global $_wp_additional_image_sizes;

	$image_sizes = array();
	$default_image_sizes = array( 'thumbnail', 'medium', 'medium_large', 'large'  );
	foreach ( $default_image_sizes as $size ) {
		$image_sizes[$size]['width']  = intval( get_option( "{$size}_size_w") );
		$image_sizes[$size]['height'] = intval( get_option( "{$size}_size_h") );
		$image_sizes[$size]['crop']   = get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
	}
	
	if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ){
		$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
	}
		
	return array_keys($image_sizes);
}

/**
 * Get swatch type by taxonomy name
 *
 * @since 1.0.0
 *
 * @param string   $taxonomy Taxonomy name.
 * @param null $product_id Product id.
 *
 * @return string
 */
function swatchly_get_swatch_type( $taxonomy, $product_id = null ) {
    $swatch_type = 'select';

    // txonomy override
    if( taxonomy_exists( $taxonomy ) ){
        global $wpdb;

        $attr = substr( $taxonomy, 3 );
        $attr = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = %s", $attr ) ); //phpcs:ignore
        $swatch_type = isset($attr->attribute_type) ? $attr->attribute_type : '';

        if(is_admin()){
            return $swatch_type;
        }
    }

    // product override
    if( $product_id ){
        $product_meta                    = get_post_meta( $product_id, '_swatchly_product_meta', true );
        $auto_convert_dropdowns_to_label = isset($product_meta['auto_convert_dropdowns_to_label']) ? $product_meta['auto_convert_dropdowns_to_label'] : '';
        $swatch_type                     = isset( $product_meta[$taxonomy]['swatch_type'] ) && $product_meta[$taxonomy]['swatch_type'] ? $product_meta[$taxonomy]['swatch_type'] : $swatch_type;    
    }

    return $swatch_type;
}

/**
 * Get the directory name of the current theme regardless of the child theme.
 * 
 * @return The directory name of the theme's "stylesheet" files, inside the theme root.
 */
function swatchly_get_current_theme_directory(){
    $current_theme_dir  = '';
    $current_theme      = wp_get_theme();
    if( $current_theme->exists() && $current_theme->parent() ){
        $parent_theme = $current_theme->parent();

        if( $parent_theme->exists() ){
            $current_theme_dir = $parent_theme->get_stylesheet();
        }
    } elseif( $current_theme->exists() ) {
        $current_theme_dir = $current_theme->get_stylesheet();
    }

    return $current_theme_dir;
}