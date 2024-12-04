<?php
namespace Swatchly\Admin;

/**
 * Attribute_Taxonomy_Metabox class
 */
class Attribute_Taxonomy_Metabox{

    /**
     * Constructor
     */
    public function __construct() {
        $this->settings();
    }

    /**
     * Meta options
     */
    public function settings() {
		$taxonomy    = isset( $_GET['taxonomy'] ) ? sanitize_text_field( $_GET['taxonomy'] ) : ''; //phpcs:ignore
		$swatch_type = $this->get_taxonomy_swatch_type( $taxonomy );

		$prefix      			  = 'swatchly_taxonomy_meta';
		$taxonomies_obj           = wc_get_attribute_taxonomies();
		$attribute_taxonomy_slugs = array_map( function($taxonomies_obj){
    		return 'pa_'. $taxonomies_obj->attribute_name;
    	}, $taxonomies_obj);

    	$fields = array(
    		array(
				'id'           => 'swatchly_image',
				'type'         => 'media',
				'title'        => esc_html__( 'Image', 'swatchly' ),
				'url'          => false,
				'class'        => 'swatchly_show_if_image'
    		),

    		array(
                'id'      => 'swatchly_image_size',
                'type'    => 'text',
                'title'   => esc_html__( 'Image Size', 'swatchly' ),
                'after'   => esc_html__( 'Place the image size name here. WordPress default image sizes are: thumbnail, medium, medium_large, large and full. Custom image size also can be used. Default is: thumbnail', 'swatchly' ),
                'default' => 'thumbnail',
                'class'   => 'swatchly_show_if_image'
    		),

			array(
			    'id'    => 'swatchly_tooltip',
			    'type'  => 'select',
			    'title' => esc_html__( 'Swatch Tooltip', 'swatchly' ),
			    'options'   => array(
					''        => esc_html__('Use Global Setting', 'swatchly'),
					'text'    => esc_html__('Text', 'swatchly'),
					'image'   => esc_html__('Image', 'swatchly'),
					'disable' => esc_html__('Disable', 'swatchly'),
			    ),
			    'default' => '',
                'class' => 'swatchly_hide_if_select'
			),

			array(
				'id'         => 'swatchly_tooltip_text',
				'type'       => 'text',
				'title'      => esc_html__( 'Tooltip Text', 'swatchly' ),
				'after'      => esc_html__( 'By default, the "Attribute Name" will be shown as the tooltip. If you want custom tooltip text, put it here.', 'swatchly' ),
				'dependency' => array( 'swatchly_tooltip', '==', 'text' ),
			),

    		array(
				'id'         => 'swatchly_tooltip_image',
				'type'       => 'media',
				'title'      => esc_html__( 'Tooltip Image', 'swatchly' ),
				'url'        => false,
				'dependency' => array( 'swatchly_tooltip', '==', 'image' ),
    		),

			array(
                'id'         => 'swatchly_tooltip_image_size',
                'type'       => 'text',
                'title'      => esc_html__( 'Tooltip Image Size', 'swatchly' ),
	            'after'   => sprintf(
					/* translators: %s: global settings page link */
	              	esc_html__('Place the image size name here. WordPress default image sizes are: thumbnail, medium, medium_large, large and full. Custom image size also can be used. Leave it empty to use the vlaue from %s.', 'swatchly'),	
	              	'<a href="'. esc_url(admin_url('admin.php?page=swatchly-admin#tab=general-settings/global')) .'">' . esc_html__('global settings', 'swatchly') . '</a>'
	            ),
                'dependency' => array( 'swatchly_tooltip', '==', 'image' ),
			),

			array(
			    'id'    => 'swatchly_color',
			    'type'  => 'color',
			    'title' => esc_html__( 'Swatch Color', 'swatchly' ),
			    'class' => 'swatchly_show_if_color swatchly_hide_if_select'
			),

			array(
			    'id'    => 'swatchly_enable_multi_color',
			    'type'  => 'checkbox',
			    'title' => esc_html__( 'Enable Multi Color', 'swatchly' ),
			    'label' => esc_html__( 'By checking this will enable you to set multiple color.', 'swatchly' ),
			    'class' => 'swatchly_show_if_color swatchly_hide_if_select'
			),

			array(
                'id'         => 'swatchly_color_2',
                'type'       => 'color',
                'title'      => esc_html__( 'Swatch Color 2', 'swatchly' ),
                'dependency' => array( 'swatchly_enable_multi_color', '==', '1' ),
				'class'      => 'swatchly_show_if_color swatchly_hide_if_select'
			),
    	);

    	 // Create taxonomy meta option wrapper
    	 \CSF::createTaxonomyOptions( $prefix, array(
    	   'taxonomy'  => $attribute_taxonomy_slugs,
    	   'data_type' => 'unserialize', // The type of the database save options. `serialize` or `unserialize`
    	   'class'	   => 'swatchly_type_'. $swatch_type
    	 ) );

    	 // Create a section & fields
    	 \CSF::createSection( $prefix, array(
    	 	'fields'      => $fields
    	 ));
    }

    /**
     * Get swatch type of given taxonomy
     */
    public function get_taxonomy_swatch_type( $taxonomy ){
		if( empty($taxonomy) ){
			return '';
		}
		
        global $wpdb;

        $attr = substr( $taxonomy, 3 );
        $attr = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = %s", $attr ) ); // phpcs:ignore
        $swatch_type = isset($attr->attribute_type) ? $attr->attribute_type : '';

        return $swatch_type;
    }
}