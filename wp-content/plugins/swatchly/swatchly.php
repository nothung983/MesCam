<?php
/**
 * Plugin Name: Swatchly - Variation Swatches for WooCommerce Products
 * Plugin URI:  https://plugindemo.hasthemes.com/swatchly/
 * Description: Variation Swatches for WooCommerce Products
 * Version:     1.3.7
 * Author:      HasThemes
 * Author URI:  https://hasthemes.com
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: swatchly
 * Domain Path: /languages
 */

// If this file is accessed directly, exit
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main class
 *
 * @since 1.0.0
 */
final class Swatchly {

    /**
     * Version
     *
     * @since 1.0.0
     */
    public $version = '1.3.7';

    /**
     * The single instance of the class
     *
     * @since 1.0.0
     */
    protected static $_instance = null;

    /**
     * Main Instance
     *
     * Ensures only one instance of this pluin is loaded
     *
     * @since 1.0.0
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    private function __construct() {
        $this->define_constants();
        $this->includes();
        $this->run();
    }

    /**
     * Define the required constants
     *
     * @since 1.0.0
     */
    private function define_constants() {
        define( 'SWATCHLY_VERSION', $this->version );
        define( 'SWATCHLY_FILE', __FILE__ );
        define( 'SWATCHLY_PATH', __DIR__ );
        define( 'SWATCHLY_URL', plugins_url( '', SWATCHLY_FILE ) );
        define( 'SWATCHLY_ASSETS', SWATCHLY_URL . '/assets' );
    }

    /**
     * Include required core files
     *
     * @since 1.0.0
     */
    public function includes() {
        /**
         * Including Codestar Framework.
         */
        if ( ! class_exists( 'CSF' ) ) {
            require_once SWATCHLY_PATH .'/libs/codestar-framework/codestar-framework.php';
        }
        
        /**
         * Composer autoload file.
         */
        require_once SWATCHLY_PATH . '/vendor/autoload.php';

        /**
         * Including plugin file for secutiry purpose
         */
        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if ( !function_exists( 'get_current_screen' ) ){ 
            require_once ABSPATH . '/wp-admin/includes/screen.php'; 
        }

        if( is_admin() ){
            require_once SWATCHLY_PATH .'/includes/Admin/recommendations/init.php';
            require_once SWATCHLY_PATH .'/includes/Admin/Diagnostic_Data.php';
            require_once SWATCHLY_PATH .'/includes/Admin/Notices.php';
            require_once SWATCHLY_PATH .'/includes/Admin/Swatchly_Trial.php';
        }
    }

    /**
     * First initialization of the plugin
     *
     * @since 1.0.0
     */
    private function run() {
        register_activation_hook( __FILE__, array( $this, 'register_activation_hook_cb' ) );

        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            add_action( 'admin_notices', array( $this, 'build_dependencies_notice' ) );
        } else {
            // Set up localisation.
            add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

            // Finally initialize this plugin
            add_action( 'plugins_loaded', array( $this, 'init' ) );

            // Redirect to welcome page after activate the plugin
			add_action('admin_init', array( $this, 'redirect_after_activate') );
        }
    }

    /**
     * Do stuff upon plugin activation
     *
     * @since 1.0.0
     */
    public function register_activation_hook_cb() {
        // deactivate the free plugin if active
        if( is_plugin_active('swatchly-pro/swatchly-pro.php') ){
            add_action('update_option_active_plugins', function(){
                deactivate_plugins('swatchly-pro/swatchly-pro.php');
            });
        }

        $installed = get_option( 'swatchly_installed' );

        if ( ! $installed ) {
            update_option( 'swatchly_installed', time() );
        }

        update_option( 'swatchly_version', SWATCHLY_VERSION );

        // It sets a transient that will be used to redirect the user to the welcome page after
        // activating the plugin.
        set_transient( 'swatchly_do_activation_redirect', true, 30 );
    }

     /**
     * It checks if a transient exists, if it does, it deletes it and redirects to the welcome page
     * 
     * @return the value of the transient.
     */
    public function redirect_after_activate(){
        $woolentor_status = is_plugin_active('woolentor-addons/woolentor_addons_elementor.php');

        // return when woolentor is already active
        if( $woolentor_status ){
            return;
        }

        if ( current_user_can('manage_options') && get_transient('swatchly_do_activation_redirect') ) {
            delete_transient( 'swatchly_do_activation_redirect' );
            
            exit( esc_url(wp_redirect("admin.php?page=swatchly-admin")) );
        }
    }

    /**
     * Load the plugin textdomain
     *
     * @since 1.0.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( 'swatchly', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Initialize this plugin
     *
     * @since 1.0.0
     */
    public function init() {
        new Swatchly\Helper();
        new Swatchly\Admin\Global_Settings();
        new Swatchly\Frontend\Woo_Config();

        if ( is_admin() ) {
            new Swatchly\Admin();
        } elseif( !swatchly_get_option('disable_plugin') ) {
            new Swatchly\Frontend();
            new Swatchly\Compatibility\Elementor();
        }
    }

    /**
     * Output a admin notice when build dependencies not met
     *
     * @since 1.0.0
     */
    public function build_dependencies_notice() {
        $message = sprintf(
            /*
             * translators:
             * 1: Swatchly.
             * 2: WooCommerce.
             */
            esc_html__( '%1$s plugin requires the %2$s plugin to be installed and activated in order to work.', 'swatchly' ),
            '<strong>' . esc_html__( 'Swatchly', 'swatchly' ) . '</strong>',
            '<strong>' . esc_html__( 'WooCommerce', 'swatchly' ) . '</strong>'
        );

        printf( '<div class="notice notice-warning"><p>%1$s</p></div>', wp_kses_post($message) );
    }

}

/**
 * Returns the main instance of Swatchly
 *
 * @since 1.0.0
 */

function swatchly() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
    return Swatchly::instance();
}

// Kick-off the plugin
swatchly();
