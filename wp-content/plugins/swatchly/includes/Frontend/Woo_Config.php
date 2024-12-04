<?php
namespace Swatchly\Frontend;
use Swatchly\Helper as Helper;

/**
 * Woo_Config class
 */
class Woo_Config {

    public $version = '';

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        // Set time as the version for development mode.
        if( defined('WP_DEBUG') && WP_DEBUG ){
            $this->version = time();
        } else {
            $this->version = SWATCHLY_VERSION;
        }
        if( swatchly_get_option('disable_plugin') ){
            return;
        }

        // Filter through each variation form to inject the swatch html
        add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array( $this, 'dropdown_variation_attribute_options_html_cb' ), 200, 2 );

        $enable_swatches = swatchly_get_option('enable_swatches', '', 'pl');
        $current_theme   = swatchly_get_current_theme_directory();

        // before title
        if( $enable_swatches && swatchly_get_option('pl_position') == 'before_title' ){
            if( $current_theme == 'astra' ){
                add_action('astra_woo_shop_title_before', array( $this, 'loop_variation_form_html'));
            } else {
                add_action('woocommerce_shop_loop_item_title', array( $this, 'loop_variation_form_html'), 0);
            }

            // For Universal Layout
            add_action('woolentor_universal_before_title', array( $this, 'loop_variation_form_html'), 0 );
        }

        // after title
        if( $enable_swatches && swatchly_get_option('pl_position') == 'after_title' ){
            if( $current_theme == 'astra' ){
                add_action('astra_woo_shop_title_after', array( $this, 'loop_variation_form_html'));
            } else {
                add_action('woocommerce_shop_loop_item_title', array( $this, 'loop_variation_form_html'), 9999);
            }

            // For Universal Layout
            add_action('woolentor_universal_after_title', array( $this, 'loop_variation_form_html'), 0 );
        }

        // before price
        if( $enable_swatches && swatchly_get_option('pl_position') == 'before_price' ){
            if( $current_theme == 'astra' ){
                add_action('astra_woo_shop_price_before', array( $this, 'loop_variation_form_html'));
            } else {
                add_action('woocommerce_after_shop_loop_item_title', array( $this, 'loop_variation_form_html'), 9);
            }

            // For Universal Layout
            add_action('woolentor_universal_before_price', array( $this, 'loop_variation_form_html'), 0 );
        }

        // after price
        if( $enable_swatches && swatchly_get_option('pl_position') == 'after_price' ){
            if( $current_theme == 'astra' ){
                add_action('astra_woo_shop_price_after', array( $this, 'loop_variation_form_html'));
            } else {
                add_action('woocommerce_after_shop_loop_item_title', array( $this, 'loop_variation_form_html'), 11);
            }

            // For Universal Layout
            add_action('woolentor_universal_after_price', array( $this, 'loop_variation_form_html'), 0 );
        }

        // before/after cart
        if( $enable_swatches && swatchly_get_option('pl_position') == 'before_cart' || swatchly_get_option('pl_position') == 'after_cart' ){
            add_filter('woocommerce_loop_add_to_cart_link',  array( $this, 'filter_loop_add_to_cart_link'), 99, 2);
        }

        // custom position
        if( $enable_swatches && swatchly_get_option('pl_position') == 'custom_position'){
            $priority = swatchly_get_option('pl_custom_position_hook_priority') ? swatchly_get_option('pl_custom_position_hook_priority') : 10;
            add_action( swatchly_get_option('pl_custom_position_hook_name') , array( $this, 'loop_variation_form_html'), $priority );
        }

        if( swatchly_get_option('enable_swatches') && swatchly_get_option('pl_position') == 'shortcode'){
            // shortcode [swatchly_pl_swatches]
            add_shortcode( 'swatchly_pl_swatches', array( $this, 'get_loop_variation_form_html') );
        }

        // Ajax add to cart
        add_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'filter_loop_add_to_cart_args' ), 20, 2 );

        // Ajax variation threshold
        add_filter( 'woocommerce_ajax_variation_threshold', array( $this, 'ajax_variation_threshold') , 15, 2 );
        
        // This tweak for adding support with infinite scrolling
        // Context - the 1st/2nd page of the shop there is no variable product, so the add_to_cart_variation_js js file won't load initially
        add_action('wp_enqueue_scripts', function(){
            $force = apply_filters('swatchly_force_load_add_to_cart_variation_js_file_in_shop', false);

            if( $force ){
                $this->enqueue_add_to_cart_variation_js();
            }
        });

        add_filter('swatchly_force_load_add_to_cart_variation_js_file_in_shop', '__return_true');
    }

    /**
     * Filter variation dropdown HTML
     * Consists each of the <select></select> element
     */
    public function dropdown_variation_attribute_options_html_cb(  $old_html,  $args ){
        $product     = $args['product'];
        
        if( is_admin() && !wp_doing_ajax() && !Helper::doing_ajax_is_elementor_preview() ){
            return $old_html;
        }

        // return default select for the grouped products for the single page and also for the 
        // smart group products created by the WPC Grouped Product for WooCommerce plugin
        $should_return_default_select = false;
        if( is_product() ){
            $single_product = wc_get_product(get_the_id());
            if( $single_product->get_type() == 'grouped' || $single_product->get_type() == 'woosg' ){
                $should_return_default_select = true;
            }   
        }

        $should_return_default_select = apply_filters( 'swatchly_return_default_select', $should_return_default_select, $args );
        if( $should_return_default_select ){
            return $old_html;
        }

        $enable_swatches = swatchly_get_option('enable_swatches', '', 'pl');
        if(is_product()){
            $enable_swatches = swatchly_get_option('enable_swatches', '', 'sp');
        }

        if(!$enable_swatches && !is_product()){
            return $old_html;
        }

        if(!$enable_swatches && is_product()){
            return $old_html;
        }

        $background_color   = '';
        $enable_multi_color = '';
        $background_color_2 = '';
        $background_image   = '';
        $tooltip_image      = '';
        $image_id           = '';
        $auto_convert_dropdowns_to_image    = '';
        
        $product_id           = $product->get_id();
        $product_meta         = (array) get_post_meta( $product_id, '_swatchly_product_meta', true );
        $taxonomy             = $args['attribute'];

        $meta_data                       = get_post_meta($product_id, '_swatchly_product_meta', true);
        $swatch_type                     = swatchly_get_swatch_type( $taxonomy, $product_id );
        $tooltip                         = swatchly_get_option('tooltip', '', 'pl');
        $show_swatch_image_in_tooltip    = swatchly_get_option('show_swatch_image_in_tooltip', '', 'pl');
        $tooltip_image_size              = swatchly_get_option('tooltip_image_size', '', 'pl');
        $shape_style                     = swatchly_get_option('shape_style', '', 'pl');
        $enable_shape_inset              = swatchly_get_option('enable_shape_inset', '', 'pl');
        $disabled_attribute_type         = swatchly_get_option('disabled_attribute_type',  '', 'pl');
        $auto_convert_dropdowns_to_label = swatchly_get_option('auto_convert_dropdowns_to_label', '', 'pl');

        if(is_product()){
            $tooltip                         = swatchly_get_option('tooltip', '', 'sp');
            $show_swatch_image_in_tooltip    = swatchly_get_option('show_swatch_image_in_tooltip', '', 'sp');
            $tooltip_image_size              = swatchly_get_option('tooltip_image_size', '', 'sp');
            $shape_style                     = swatchly_get_option('shape_style', '', 'sp');
            $enable_shape_inset              = swatchly_get_option('enable_shape_inset', '', 'sp');
            $disabled_attribute_type         = swatchly_get_option('disabled_attribute_type',  '', 'sp');
            $auto_convert_dropdowns_to_label = swatchly_get_option('auto_convert_dropdowns_to_label', '', 'sp');
        }

        $tooltip_image_size = $tooltip_image_size ? $tooltip_image_size : 'thumbnail';

        // Override product level meta
        $auto_convert_dropdowns_to_label = isset($product_meta['auto_convert_dropdowns_to_label']) && $product_meta['auto_convert_dropdowns_to_label'] ? $product_meta['auto_convert_dropdowns_to_label'] : $auto_convert_dropdowns_to_label;
        $swatch_type_p_meta = isset($product_meta[$taxonomy]['swatch_type']) && $product_meta[$taxonomy]['swatch_type'] ? $product_meta[$taxonomy]['swatch_type'] : '';
        $swatch_type = $swatch_type_p_meta ? $swatch_type_p_meta : $swatch_type;
        
        // Override Product attribute taxonomy level
        $shape_style = swatchly_get_post_meta($product_id, $taxonomy, 'shape_style', $shape_style);
        $enable_shape_inset = swatchly_get_post_meta($product_id, $taxonomy, 'enable_shape_inset', $enable_shape_inset);
        
        if($enable_shape_inset == 'disable' || !$enable_shape_inset){
            $inset_class = '';
        } elseif($enable_shape_inset){
            $inset_class    = 'swatchly-inset';
        }
        
        $disabled_attribute_type_class = str_replace( '_', '-', $disabled_attribute_type );

        // Check if we've found any swatch type from attribute meta / product meta
        $found_swatch_type = in_array( $swatch_type, array( 'label', 'color', 'image') );

        // Decide & get which attribute key should be used for auto convert image type swatch (pro)
        $auto_convert_image_arr = array();

        // Decide which swatch type should be assigned for this attribute
        if( $swatch_type_p_meta == 'select' ){

            return $old_html;

        } elseif( !$found_swatch_type && $auto_convert_dropdowns_to_label ){

            $swatch_type = 'label';

        } elseif( !$found_swatch_type ){

            return $old_html;

        }

        // Featured Attribute class (pro)
        $featured_class = '';

        // Add class to hide/exclude specific variation row
        $hide_this_variation_row_class = '';
        $hide_this_variation_row = apply_filters( 'swatchly_exclude_variation', false, $args );
        if( $hide_this_variation_row ){
            $hide_this_variation_row_class = 'swatchly-hide-this-variation-row';
        }

        $variations         = $product->get_children();
        $meta_attributes    = get_post_meta($product_id, '_product_attributes', true);
        $selected           = $args['selected'];
        $current_term       = get_term_by( 'slug', $selected, $taxonomy );
        $current_term_label = $current_term ? $current_term->name : $selected;

        $html = "<div class='swatchly_default_select $hide_this_variation_row_class'>";
        $html .= $old_html;
        $html .= '</div>';

        if( $hide_this_variation_row_class ){
            // return the default select, since this variation is hidden by css
            // don't load the further custom swatch markup, which is not needed anymore for this variation row
            return $html; 
        }

        $attr_class = "class='swatchly-type-wrap swatchly-shape-type-$shape_style swatchly-type-$swatch_type $inset_class swatchly-$disabled_attribute_type_class $featured_class $hide_this_variation_row_class'";
        $attr_default_attr_value  = "data-default_attr_value='$current_term_label'";
        $html .= "<div $attr_class $attr_default_attr_value>";

            if ( taxonomy_exists( $taxonomy ) ) {
                $attribute_terms  = array();
                $terms_with_order_support = wc_get_product_terms(
                    $product->get_id(),
                    $taxonomy,
                    array(
                        'fields' => 'all',
                    )
                );

                // Collect all the term slugs, used array_map instead of array_colums beacuse of PHP 5.3 version compatibility
                $attribute_terms = array_map( function( $x ) { return $x->slug; }, $terms_with_order_support );

                foreach ( $attribute_terms as $index => $term_value ) {
                    $term = get_term_by('slug', $term_value, $taxonomy);
                    $image_id = ( isset($auto_convert_image_arr[$image_id]) && $auto_convert_image_arr[$image_id] ) ? $auto_convert_image_arr[$image_id] : '';

                    $count = $index + 1;
                    $tooltip_text = $term->name;

                    // Tooltip (Global level)
                    if( is_product() ) {
                        $tooltip = swatchly_get_option('tooltip', '', 'sp');
                    } else {
                        $tooltip = swatchly_get_option('tooltip', '', 'pl');
                    }

                    // Tooltip override (Term level)
                    $tooltip2         = get_term_meta( $term->term_id, 'swatchly_tooltip', true );
                    if($tooltip2 == 'disable'){
                        $tooltip = false;
                    }

                    $tooltip_text2 = get_term_meta( $term->term_id, 'swatchly_tooltip_text', true );
                    if($tooltip2 == 'text'){
                        $tooltip_text = $tooltip_text2;
                    }

                    $tooltip_image2 = get_term_meta( $term->term_id, 'swatchly_tooltip_image', true );
                    $tooltip_image_size2 = get_term_meta( $term->term_id, 'swatchly_tooltip_image_size', true );
                    if($tooltip2 == 'image'){
                        $tooltip = true;
                        $tooltip_image = $tooltip_image2['id'];
                        $tooltip_image_size = $tooltip_image_size2 ? $tooltip_image_size2 : $tooltip_image_size;
                    }

                    // Tooltip override (Product level taxonomy option)
                    if(isset($meta_data[$term->taxonomy]) && is_array($meta_data[$term->taxonomy])){
                        $p_tax_meta             = $meta_data[$term->taxonomy];
                        $p_tax_meta_swatch_type = $p_tax_meta['swatch_type'];

                        if( in_array($p_tax_meta_swatch_type, array('label', 'color', 'image')) ){
                            $tooltip3 = $p_tax_meta['tooltip'];

                            if($tooltip3 == 'disable'){
                                $tooltip = false;
                            }
                            
                            $tooltip_text3 = $p_tax_meta['tooltip_text'];
                            $tooltip_image3 = $p_tax_meta['tooltip_image'];
                            $tooltip_image_size3 = $p_tax_meta['tooltip_image_size'];
                            if($tooltip3 == 'text'){
                                $tooltip = true;
                                $tooltip_text = $tooltip_text3;
                            }

                            if($tooltip3 == 'image'){
                                $tooltip = true;
                                $tooltip_image = $tooltip_image3;
                                $tooltip_image_size = $tooltip_image_size3 ? $tooltip_image_size3 : $tooltip_image_size;
                            }
                        }
                    }

                    // Tooltip override (Product level term option)
                    if(isset($meta_data[$term->taxonomy]['terms'][$term->term_id]) && is_array($meta_data[$term->taxonomy]['terms'][$term->term_id])){
                        $p_tax_meta             = $meta_data[$term->taxonomy];
                        $p_tax_meta_swatch_type = $p_tax_meta['swatch_type'];

                        if( in_array($p_tax_meta_swatch_type, array('label', 'color', 'image')) ){
                           $p_term_meta = $meta_data[$term->taxonomy]['terms'][$term->term_id];
                           $tooltip4 = $p_term_meta['tooltip'];

                           if($tooltip4 == 'disable'){
                               $tooltip = false;
                           }

                           $tooltip_text4 = $p_term_meta['tooltip_text'];
                           $tooltip_image4 = $p_term_meta['tooltip_image'];
                           $tooltip_image_size4 = $p_term_meta['tooltip_image_size'];
                           if($tooltip4 == 'text'){
                               $tooltip      = true;
                               $tooltip_text = $tooltip_text4;
                           }

                           if($tooltip4 == 'image'){
                               $tooltip            = true;
                               $tooltip_image      = $tooltip_image4;
                               $tooltip_image_size = $tooltip_image_size4 ? $tooltip_image_size4 : $tooltip_image_size;
                           } 
                        }
                    }

                    // Term Meta
                    $background_color   = get_term_meta( $term->term_id, 'swatchly_color', true );
                    $enable_multi_color = get_term_meta( $term->term_id, 'swatchly_enable_multi_color', true );
                    $background_color_2 = get_term_meta( $term->term_id, 'swatchly_color_2', true );
                    $image_arr          = get_term_meta( $term->term_id, 'swatchly_image', true );
                    $swatch_image_size  = get_term_meta( $term->term_id, 'swatchly_image_size', true );
                    $image_id           = isset($image_arr['id']) ? $image_arr['id'] : '';

                    $selected_class = ($term->slug == $selected) ? 'swatchly-selected' : '';

                    // Product level term options Override
                    if(isset($meta_data[$term->taxonomy]['terms'][$term->term_id]) && is_array($meta_data[$term->taxonomy]['terms'][$term->term_id])){
                        $p_term_meta = $meta_data[$term->taxonomy]['terms'][$term->term_id];
                        
                        if( !empty($p_term_meta['swatch_type']) && $p_term_meta['swatch_type'] == 'color'){
                            $swatch_type        = isset($p_term_meta['swatch_type']) ? $p_term_meta['swatch_type'] : '';
                            $background_color   = isset($p_term_meta['color']) ?  $p_term_meta['color'] : '';
                            $enable_multi_color = isset($p_term_meta['enable_multi_color']) ? $p_term_meta['enable_multi_color']: '';
                            $background_color_2 = isset($p_term_meta['color_2']) ? $p_term_meta['color_2'] : '';
                        }

                        if( !empty($p_term_meta['swatch_type']) && $p_term_meta['swatch_type'] == 'image'){
                            $swatch_type = isset($p_term_meta['swatch_type']) ? $p_term_meta['swatch_type'] : '';
                            $image_id    = isset($p_term_meta['image']) ? $p_term_meta['image'] : '';
                        }

                        if( !empty($p_term_meta['swatch_type']) && $p_term_meta['swatch_type'] == 'label'){
                        }
                    }

                    // HTML Markup
                    $tooltip_image      = wp_get_attachment_image_url( $tooltip_image, $tooltip_image_size );
                    $attr_class         = "class='swatchly-swatch $selected_class'";
                    $attr_value         = "data-attr_value='$term->slug'";
                    $attr_label         = "data-attr_label='$term->name'";
                    $attr_tooltip_text  = $tooltip && $tooltip_text ? 'data-tooltip_text="'. esc_attr($tooltip_text) .'"' : '';
                    $attr_tooltip_image = $tooltip && $tooltip_image ? 'data-tooltip_image="'. esc_attr($tooltip_image) .'"' : '';

                    if($swatch_type == 'label'){
                        $html .= "<div $attr_class $attr_tooltip_text $attr_tooltip_image $attr_label $attr_value>";
                            $html .= '<span class="swatchly-content"><span class="swatchly-text">'. esc_html($term->name) .'</span></span>';
                        $html .= '</div>';

                    }

                    if($swatch_type == 'color'){
                        $attr_inline_style = $background_color ? "style='background-color: $background_color;'" : '';

                        if($enable_multi_color){
                            $attr_inline_style = $background_color_2 ? "style='background: linear-gradient(-50deg, $background_color 50%, $background_color_2 50%);'" : '';
                        }

                        $html .= "<div $attr_class $attr_inline_style $attr_tooltip_text $attr_tooltip_image $attr_label $attr_value>";
                            $html .= '<span class="swatchly-content"></span>';
                        $html .= '</div>';
                    }

                    if($swatch_type == 'image'){
                        if( $auto_convert_dropdowns_to_image && !$image_id && (isset($auto_convert_image_arr[$term_value]) && $auto_convert_image_arr[$term_value]) ){
                            $image_id = $auto_convert_image_arr[$term_value];
                        }

                        $swatch_image_size = $swatch_image_size ? $swatch_image_size : 'woocommerce_gallery_thumbnail';
                        if( $show_swatch_image_in_tooltip ){
                            $swatch_image_size  = $tooltip_image_size;
                        }
                        
                        $background_image   = $image_id ? wp_get_attachment_image_url( $image_id, $swatch_image_size ) : wc_placeholder_img_src( $swatch_image_size );

                        if( $show_swatch_image_in_tooltip ){
                            $attr_tooltip_image = $tooltip && $background_image ? 'data-tooltip_image="'. esc_attr($background_image) .'"' : '';
                        }

                        $attr_inline_style  = $background_image ? " style='background-image: url( $background_image );'" : '';

                        $html .= "<div $attr_class $attr_inline_style $attr_tooltip_text $attr_tooltip_image $attr_label $attr_value>";
                            $html .= '<span class="swatchly-content"></span>';
                        $html .= '</div>';
                    }
                }
            } else{
                $attachment_id = '';
                $custom_variation = $args['options'];

                foreach( $custom_variation as $index => $variation_name ){
                    $count = $index + 1;
                    $tooltip_text = $variation_name;
                    $image_id = ( isset($auto_convert_image_arr[$variation_name]) && $auto_convert_image_arr[$variation_name] ) ? $auto_convert_image_arr[$variation_name] : '';

                    // Tooltip (Global level)
                    if( is_product() ) {
                        $tooltip = swatchly_get_option('tooltip', '', 'sp');
                    } else {
                        $tooltip = swatchly_get_option('tooltip', '', 'pl');
                    }

                    // Tooltip override (Product level taxonomy option)
                    if(isset($meta_data[$taxonomy]) && is_array($meta_data[$taxonomy])){
                        $p_tax_meta = $meta_data[$taxonomy];
                        $p_tax_meta_swatch_type = $p_tax_meta['swatch_type'];

                        if( in_array($p_tax_meta_swatch_type, array('label', 'color', 'image')) ){
                            $tooltip3 = isset($p_tax_meta['tooltip']) ?  $p_tax_meta['tooltip'] : '';

                            if($tooltip3 == 'disable'){
                                $tooltip = false;
                            }
                            
                            $tooltip_text3 = isset($p_tax_meta['tooltip_text']) ?  $p_tax_meta['tooltip_text'] : '';
                            $tooltip_image3 = isset($p_tax_meta['tooltip_image']) ? $p_tax_meta['tooltip_image'] : '';
                            $tooltip_image_size3 = isset($p_tax_meta['tooltip_image_size']) ? $p_tax_meta['tooltip_image_size'] : '';
                            if($tooltip3 == 'text'){
                                $tooltip = true;
                                $tooltip_image = '';
                                $tooltip_text = $tooltip_text3;
                            }

                            if($tooltip3 == 'image'){
                                $tooltip = true;
                                $tooltip_image = $tooltip_image3;
                                $tooltip_image_size = $tooltip_image_size3 ? $tooltip_image_size3 : $tooltip_image_size;
                            }
                        }
                    }

                    // Tooltip override (Product level term option)
                    if(isset($meta_data[$taxonomy]['terms'][$variation_name]) && is_array($meta_data[$taxonomy]['terms'][$variation_name])){
                        $p_term_meta = $meta_data[$taxonomy]['terms'][$variation_name];

                        if( in_array($p_tax_meta_swatch_type, array('label', 'color', 'image')) ){
                            $tooltip4 = $p_term_meta['tooltip'];

                            if($tooltip4 == 'disable'){
                                $tooltip = false;
                            }

                            $tooltip_text4 = isset($p_term_meta['tooltip_text']) ? $p_term_meta['tooltip_text'] :  '';
                            $tooltip_image4 = isset($p_term_meta['tooltip_image']) ?  $p_term_meta['tooltip_image'] :  '';
                            $tooltip_image_size4 = isset($p_term_meta['tooltip_image_size']) ? $p_term_meta['tooltip_image_size'] : '';
                            if($tooltip4 == 'text'){
                                $tooltip = true;
                                $tooltip_image = '';
                                $tooltip_text = $tooltip_text4;
                            }

                            if($tooltip4 == 'image'){
                                $tooltip = true;
                                $tooltip_image = $tooltip_image4;
                                $tooltip_image_size = $tooltip_image_size4 ? $tooltip_image_size4 : $tooltip_image_size;
                            }
                        }
                    }


                    if(isset($meta_data[$taxonomy]['terms'][$variation_name]) && isset($meta_data[$taxonomy]['terms'][$variation_name])){
                        $p_term_meta = $meta_data[$taxonomy]['terms'][$variation_name];
                        
                        if( !empty($p_term_meta['swatch_type']) && $p_term_meta['swatch_type'] == 'color'){
                            $swatch_type        = isset($p_term_meta['swatch_type']) ? $p_term_meta['swatch_type'] : '';
                            $background_color   = isset($p_term_meta['color']) ? $p_term_meta['color'] : '';
                            $enable_multi_color = isset($p_term_meta['enable_multi_color']) ?  $p_term_meta['enable_multi_color'] : '';
                            $background_color_2 = isset($p_term_meta['color_2']) ? $p_term_meta['color_2'] : '';
                        }

                        if( !empty($p_term_meta['swatch_type']) && $p_term_meta['swatch_type'] == 'image'){
                            $swatch_type = isset($p_term_meta['swatch_type']) ? $p_term_meta['swatch_type'] : '';
                            $image_id    = isset($p_term_meta['image']) ? $p_term_meta['image'] : '';
                        }

                        if( !empty($p_term_meta['swatch_type']) && $p_term_meta['swatch_type'] == 'label'){
                        }
                    }

                    if(!in_array($swatch_type, array('label', 'color', 'image'))){
                        break;
                    }

                    // HTML Markup
                    $selected_class = ($variation_name == $selected) ? 'swatchly-selected' : '';

                    $tooltip_image      = wp_get_attachment_image_url( $tooltip_image, $tooltip_image_size );
                    $attr_class         = "class='swatchly-swatch $selected_class'";
                    $attr_label         = "data-attr_label='$variation_name'";
                    $attr_value         = "data-attr_value='$variation_name'";
                    $attr_tooltip_text  = $tooltip && $tooltip_text ? 'data-tooltip_text="'. esc_attr($tooltip_text) .'"' : '';
                    $attr_tooltip_image = $tooltip && $tooltip_image ? 'data-tooltip_image="'. esc_attr($tooltip_image) .'"' : '';

                    if($swatch_type == 'label'){
                        $html .= "<div $attr_class $attr_tooltip_text $attr_tooltip_image $attr_label $attr_value>";
                            $html .= '<span class="swatchly-content"><span class="swatchly-text">'. esc_html($variation_name) .'</span></span>';
                        $html .= '</div>';

                    }

                    if($swatch_type == 'color'){
                        $attr_inline_style = $background_color ? "style='background-color: $background_color;'" : '';

                        if($enable_multi_color){
                            $attr_inline_style = $background_color_2 ? "style='background: linear-gradient(-50deg, $background_color 50%, $background_color_2 50%);'" : '';
                        }

                        $html .= "<div $attr_class $attr_inline_style $attr_tooltip_text $attr_tooltip_image $attr_label $attr_value>";
                            $html .= '<span class="swatchly-content"></span>';
                        $html .= '</div>';
                    }

                    if($swatch_type == 'image'){
                        if( $auto_convert_dropdowns_to_image && !$image_id && (isset($auto_convert_image_arr[$variation_name]) && $auto_convert_image_arr[$variation_name]) ){
                            $image_id = $auto_convert_image_arr[$variation_name];
                        }

                        $swatch_image_size  = 'woocommerce_gallery_thumbnail';
                        $background_image   = $image_id ? wp_get_attachment_image_url( $image_id, $swatch_image_size ) : wc_placeholder_img_src( $swatch_image_size );

                        if( $show_swatch_image_in_tooltip ){
                            $swatch_image_size  = $tooltip_image_size;
                            $attr_tooltip_image = $tooltip && $background_image ? 'data-tooltip_image="'. esc_attr($background_image) .'"' : '';
                        }
                        $attr_inline_style  = $background_image ? " style='background-image: url( $background_image );'" : '';

                        $html .= "<div $attr_class $attr_inline_style $attr_tooltip_text $attr_tooltip_image $attr_label $attr_value>";
                            $html .= '<span class="swatchly-content"></span>';
                        $html .= '</div>';
                    }

                }
            }
        $html .= '</div> <!-- /.swatchly-type-wrap --> ';

        return $html;
    }

    /**©
     * Filter loop add cart button to insert the variation form.
     */
    public function filter_loop_add_to_cart_link( $html, $product ){
        $position = swatchly_get_option('pl_position');

        if( !in_array($position, array( 'before_cart', 'after_cart' )) ){
            return $html;
        }

        if( $position == 'before_cart' ){
            $html = $this->get_loop_variation_form_html() . $html;
        } else {
            $html =  $html . $this->get_loop_variation_form_html();
        }

        return $html;
    }

    /**
     * Loop variation form HTML
     */
    public function get_loop_variation_form_html(){
        global $product;

        $request_data         = wp_unslash($_REQUEST); //phpcs:ignore
        $is_elementor_preview = isset($request_data['elementor-preview']) || (isset($request_data['action']) && $request_data['action'] == 'elementor' ) || is_admin();

        // Through a notice if anyone try to using on elementor mode
        if( swatchly_get_option('pl_position') == 'shortcode' && $is_elementor_preview && !wp_doing_ajax() ){
            return '<div class="elementor-panel"><div class="elementor-panel-alert elementor-panel-alert-warning">'. __( 'Please make sure that you are using this shortcode inside product loop/list. Otherwise it worn\'t work.', 'swatchly' ) . '</div></div>';
        }

        // This shortcode only work for product loop
        if( !$product || is_product() ){
            return;
        }

        if ( ! $product->is_type( 'variable' ) ) {
            return;
        }

        // hide out of stock meesage if the product is not in stock & user opt-in to hide the message
        if( !$product->get_available_variations() ){
            return;
        }

        $align = swatchly_get_option('pl_align');

        // Enqueue variation scripts.
        $this->enqueue_add_to_cart_variation_js();

        // Get Available variations?
        $get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
        $available_variations = $get_variations ? $product->get_available_variations() : false;
        $attributes           = $product->get_variation_attributes();
        $selected_attributes  = $product->get_default_attributes();

        $attribute_keys  = array_keys( $attributes );
        $variations_json = wp_json_encode( $available_variations );
        $variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

        $html = '';
        ob_start();
        ?>
            <form class="swatchly_loop_variation_form variations_form swatchly_align_<?php echo esc_attr($align); ?>" data-product_variations="<?php echo esc_attr( $variations_json ); ?>" data-product_id="<?php echo esc_attr(absint( $product->get_id() )); ?>" data-product_variations="<?php echo esc_attr($variations_attr); // WPCS: XSS ok. ?>">

                <?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
                    <p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'swatchly' ) ) ); ?></p>
                <?php else : ?>
                    <table class="variations" cellspacing="0">
                        <tbody>
                            <?php foreach ( $attributes as $attribute_name => $options ) : ?>
                                <tr>
                                    <?php if(swatchly_get_option('pl_show_swatches_label')): ?>
                                    <td class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo esc_html(wc_attribute_label( $attribute_name )); // WPCS: XSS ok. ?></label></td>
                                    <?php endif; ?>

                                    <td class="value">
                                        <?php
                                            wc_dropdown_variation_attribute_options(
                                                array(
                                                    'options'   => $options,
                                                    'attribute' => $attribute_name,
                                                    'product'   => $product,
                                                )
                                            );

                                            if(swatchly_get_option('pl_show_clear_link')){
                                                echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'swatchly' ) . '</a>' ) ) : '';
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </form> <!-- .swatchly_loop_variation_form -->
        <?php

        $html = ob_get_clean();
        return $html;
    }

    /**
     * Loop variation form HTML
     */
    public function loop_variation_form_html(){
        global $product;
        if ( ! $product->is_type( 'variable' ) ) {
            return;
        }

        // hide out of stock meesage if the product is not in stock & user opt-in to hide the message
        if( !$product->get_available_variations() ){
            return;
        }

        $align = swatchly_get_option('pl_align');

        $this->enqueue_add_to_cart_variation_js();

        // Get Available variations?
        $get_variations       = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
        $available_variations = $get_variations ? $product->get_available_variations() : false;
        $attributes           = $product->get_variation_attributes();
        $selected_attributes  = $product->get_default_attributes();

        $attribute_keys  = array_keys( $attributes );
        $variations_json = wp_json_encode( $available_variations );
        $variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );
        ?>
            <div class="swatchly_loop_variation_form variations_form swatchly_align_<?php echo esc_attr($align); ?>" data-product_variations="<?php echo esc_attr( $variations_json ); ?>" data-product_id="<?php echo esc_attr(absint( $product->get_id() )); ?>" data-product_variations="<?php echo esc_attr($variations_attr); // WPCS: XSS ok. ?>">

                <?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
                    <p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'swatchly' ) ) ); ?></p>
                <?php else : ?>
                    <table class="variations" cellspacing="0">
                        <tbody>
                            <?php foreach ( $attributes as $attribute_name => $options ) : ?>
                                <tr>
                                    <?php if(swatchly_get_option('pl_show_swatches_label')): ?>
                                    <td class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo esc_html(wc_attribute_label( $attribute_name )); // WPCS: XSS ok. ?></label></td>
                                    <?php endif; ?>

                                    <td class="value">
                                        <?php
                                            wc_dropdown_variation_attribute_options(
                                                array(
                                                    'options'   => $options,
                                                    'attribute' => $attribute_name,
                                                    'product'   => $product,
                                                )
                                            );

                                            if(swatchly_get_option('pl_show_clear_link')){
                                                echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'swatchly' ) . '</a>' ) ) : '';
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php
    }

    /**
     * Filter loop add to cart button HTML attributes
     */
    public function filter_loop_add_to_cart_args( $wp_parse_args, $product ){
        if(swatchly_get_option('pl_enable_ajax_add_to_cart')){
            if( $product->is_type( 'variable' ) ){
                $add_to_cart_text = swatchly_get_option('pl_add_to_cart_text');

                $wp_parse_args['class'] .= ' swatchly_ajax_add_to_cart';
                $wp_parse_args['attributes']['data-add_to_cart_text'] = $add_to_cart_text ? $add_to_cart_text : esc_html__('Add to Cart', 'swatchly');
                $wp_parse_args['attributes']['data-select_options_text'] = apply_filters( 'woocommerce_product_add_to_cart_text', $product->add_to_cart_text(), $product );
            }
        }

        return $wp_parse_args;
    }

    /**
     * Ajax variation threshold
     */
    public function ajax_variation_threshold( $qty, $product ){
        if(swatchly_get_option('ajax_variation_threshold')){
            $qty = absint(swatchly_get_option('ajax_variation_threshold'));
        }

        return $qty;
    }

    /**
     * Enqueue add_to_cart_variation_js
     */
    public function enqueue_add_to_cart_variation_js(){
        if( !is_product() ){
            wp_deregister_script('wc-add-to-cart-variation');
            wp_register_script( 'wc-add-to-cart-variation', SWATCHLY_ASSETS . '/js/add-to-cart-variation.js', array('jquery', 'wp-util', 'jquery-blockui' ), $this->version, true );
            
            wp_enqueue_script( 'wc-add-to-cart-variation' );

            // localization doesn't load for some themes
            // that's why we needed to manually add the localization
            wp_localize_script( 'wc-add-to-cart-variation', 'wc_add_to_cart_variation_params', array(
                'wc_ajax_url'                      => \WC_AJAX::get_endpoint( '%%endpoint%%' ),
                'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
                'i18n_make_a_selection_text'       => esc_attr__( 'Please select some product options before adding this product to your cart.', 'woocommerce' ),
                'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ),
            ));
        }
    }
}