<?php
namespace Swatchly\Compatibility;

class Elementor {
    /**
     * Constructor.
     */
    public function __construct() {
        if( is_plugin_active('elementor/elementor.php') ){
            // for related products
            add_filter('wp_kses_allowed_html', array( $this, 'related_products_compatibility' ), 10, 2);
        }
    }

    public function related_products_compatibility( $allowedposttags, $context ){
        $allowedposttags['form'] = array(
            'id' => true,
            'name' => true,
            'class' => true,
            'data-*' => true,
            'current-image' => true
        );

        $allowedposttags['select'] = array(
            'id' => true,
            'name' => true,
            'class' => true,
            'data-*' => true,
        );
    
        $allowedposttags['option'] = array(
            'id' => true,
            'name' => true,
            'class' => true,
            'data-*' => true,
            'value' => true
        );
    
        return $allowedposttags;
    }
}
