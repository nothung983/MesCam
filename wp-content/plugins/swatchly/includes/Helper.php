<?php  
namespace Swatchly;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Helper{

    private static $_instance = null;

    /**
     * Instance
     */
    public static function instance(){
        if( is_null( self::$_instance ) ){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct(){
        
    }

    /**
     * If the request is an AJAX request and the action is elementor, then it's an Elementor preview
     */
    public static function doing_ajax_is_elementor_preview(){
        $server       = wp_unslash( $_SERVER );

        $referer          = !empty($server['referer']) ? $server['referer'] : '';
        $request_uri      = !empty($server['REQUEST_URI']) ? $server['REQUEST_URI'] : '';

        parse_str($referer, $query_str_arr);
        parse_str($request_uri, $request_uri_arr);

        if( !empty($query_str_arr['action']) && $query_str_arr['action'] == 'elementor' ||
            !empty($request_uri_arr['action']) && $request_uri_arr['action'] == 'elementor'
        ){
            return true;
        }

        return false;
    }

}