<?php
namespace Swatchly;

/**
 * Admin class.
 */
class Admin {
    public $version = '';
    /**
     * Constructor.
     */
    public function __construct() {
        // Set time as the version for development mode.
        if( defined('WP_DEBUG') && WP_DEBUG ){
            $this->version = time();
        } else {
            $this->version = SWATCHLY_VERSION;
        }
        new Admin\Woo_Config();
        new Admin\Attribute_Taxonomy_Metabox();
        new Admin\Product_Metabox();
        new Admin\Install_Manager\Install_Manager();
        

        // Add a top-level custom menu called "Swatchly" for the plugin
        add_action( 'admin_menu', array( $this, 'add_top_level_menu' ), 10 );
        add_action( 'admin_menu', array( $this, 'dashboard_menu_tweaks' ), 30 );
        add_action( 'admin_menu', array( $this, 'upgrade_menu_tweaks' ), 1000 );
        add_action( 'admin_footer', [ $this, 'enqueue_admin_head_scripts'], 11 );

        // Bind admin page link to the plugin action link.
        add_filter( 'plugin_action_links_swatchly/swatchly.php', array($this, 'action_links_add'), 10, 4 );

        // Admin assets hook into action.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        add_action('admin_footer', array( $this, 'pro_version_notice' ));
        add_action('admin_head', array( $this, 'admin_rating_notice' ));
    }

    public function admin_rating_notice() {
        if(get_option('swatchly_rating_already_rated', false)) {
            return;
        }
        $message = '<div class="hastech-review-notice-wrap">
                    <div class="hastech-rating-notice-logo">
                        <img src="' . esc_url(SWATCHLY_URL . "/assets/images/logo.png") . '" alt="'.esc_attr__('Swatchly','swatchly').'" style="max-width:110px"/>
                    </div>
                    <div class="hastech-review-notice-content">
                        <h3>'.esc_html__('Thank you for choosing Swatchly WooCommerce Variation Swatches for Products to design your website!','swatchly').'</h3>
                        <p>'.esc_html__('Would you mind doing us a huge favor by providing your feedback on WordPress? Your support helps us spread the word and greatly boosts our motivation.','swatchly').'</p>
                        <div class="hastech-review-notice-action">
                            <a href="https://wordpress.org/support/plugin/swatchly/reviews/?filter=5#new-post" class="hastech-review-notice button-primary" target="_blank">'.esc_html__('Ok, you deserve it!','swatchly').'</a>
                            <span class="dashicons dashicons-calendar"></span>
                            <a href="#" class="hastech-notice-close swatchly-review-notice">'.esc_html__('Maybe Later','swatchly').'</a>
                            <span class="dashicons dashicons-smiley"></span>
                            <a href="#" data-already-did="yes" class="hastech-notice-close swatchly-review-notice">'.esc_html__('I already did','swatchly').'</a>
                        </div>
                    </div>
                </div>';

        \Swatchly_Notices::set_notice(
            [
                'id'          => 'swatchly-rating-notice',
                'type'        => 'info',
                'dismissible' => true,
                'message_type' => 'html',
                'message'     => $message,
                'display_after'  => ( 14 * DAY_IN_SECONDS ),
                'expire_time' => ( 20 * DAY_IN_SECONDS ),
                'close_by'    => 'transient'
            ]
        );
    }

    /**
     * Top level menu for the plugin
     */
    public function add_top_level_menu(){
        add_menu_page( 
            __( 'Swatchly Settings', 'swatchly' ),
            __( 'Swatchly', 'swatchly' ),
            'manage_options',
            'swatchly-admin',
            '', // leave it empty to add custom submenus under this menu
            'dashicons-ellipsis',
            57
        );
    }

    public function dashboard_menu_tweaks(){
        add_submenu_page( 'swatchly-admin', esc_html__('Welcome', 'swatchly'), esc_html__( 'Welcome', 'swatchly' ), 'manage_options','swatchly-welcome', array( $this, 'quick_recommended_plugin'), 1);
    }

    public function upgrade_menu_tweaks(){
        if(!is_plugin_active('swatchly-pro/swatchly-pro.php')) {
            add_submenu_page( 'swatchly-admin', esc_html__('Upgrade to Pro', 'swatchly'), esc_html__( 'Upgrade to Pro', 'swatchly' ), 'manage_options','https://hasthemes.com/plugins/swatchly-product-variation-swatches-for-woocommerce-products/?utm_source=admin&utm_medium=mainmenu&utm_campaign=free#pricing');
        }
    }

    function enqueue_admin_head_scripts() {
		printf( '<style>%s</style>', '#adminmenu #toplevel_page_swatchly-admin a.swatchly-upgrade-pro { font-weight: 600; background-color: #ff6e30; color: #ffffff; text-align: center; margin-top: 8px;}' );
		printf( '<script>%s</script>', '(function ($) {
            $("#toplevel_page_swatchly-admin .wp-submenu a").each(function() {
                if($(this)[0].href === "https://hasthemes.com/plugins/swatchly-product-variation-swatches-for-woocommerce-products/?utm_source=admin&utm_medium=mainmenu&utm_campaign=free#pricing") {
                    $(this).addClass("swatchly-upgrade-pro").attr("target", "_blank");
                }
            })
        })(jQuery);' );
    }

    /**
     * Action link add.
     */
    public function action_links_add( $actions, $plugin_file, $plugin_data, $context ){

        $settings_page_link = sprintf(
            /*
             * translators:
             * 1: Settings label
             */
            '<a href="'. esc_url( get_admin_url() . 'admin.php?page=swatchly-admin' ) .'">%1$s</a>',
            esc_html__( 'Settings', 'swatchly' )
        );

        array_unshift( $actions, $settings_page_link );

        return $actions;
    }

    /**
     * Enqueue admin assets.
     */
    public function enqueue_admin_assets() {
        $current_screen = get_current_screen();

        if (
            $current_screen->post_type == 'product' ||
            $current_screen->base == 'toplevel_page_swatchly-admin' ||
            $current_screen->base == 'swatchly_page_swatchly-welcome'
        ) {
            if( $current_screen->base == 'post' ){
                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'wp-color-picker-alpha',  SWATCHLY_ASSETS . '/js/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), $this->version, true );
            }

            add_thickbox();
            wp_enqueue_style( 'swatchly-admin', SWATCHLY_ASSETS . '/css/admin.css', array(), $this->version );
            wp_enqueue_script( 'swatchly-admin', SWATCHLY_ASSETS . '/js/admin.js', array('jquery'), $this->version, true );


            // inline js for the settings submenu
            $is_swatchly_setting = isset( $_GET['page'] ) ? sanitize_text_field($_GET['page']) : ''; // phpcs:ignore
            $is_swatchly_setting = $is_swatchly_setting == 'swatchly-admin' ? 1 : 0;
            wp_add_inline_script( 'swatchly-admin', 'var swatchly_is_settings_page = '. esc_js( $is_swatchly_setting ) .';');

            $localize_vars = array();
            if(get_post_type() == 'product'){
                $localize_vars['product_id'] = get_the_id();
            } else {
                $localize_vars['product_id'] = '';
            }

            $localize_vars['nonce']                   = wp_create_nonce('swatchly_product_metabox_save_nonce');
            $localize_vars['i18n']['saving']          = esc_html__('Saving...', 'swatchly');
            $localize_vars['i18n']['choose_an_image'] = esc_html__('Choose an image', 'swatchly');
            $localize_vars['i18n']['use_image']       = esc_html__('Use image', 'swatchly');
            $localize_vars['pl_override_global']      = swatchly_get_option('pl_override_global');
            $localize_vars['sp_override_global']      = swatchly_get_option('sp_override_global');
            wp_localize_script( 'swatchly-admin', 'swatchly_params', $localize_vars );
        }

        $css = '#adminmenu li a[href="admin.php?page=swatchly-welcome"]{display: none;}';
        wp_add_inline_style('common', $css);
    }

    /**
     * Pro version notice
     */
    public function pro_version_notice(){
    ?>
        <a href="#TB_inline?height=250&width=400&inlineId=swatchly_pro_notice" class="thickbox swatchly_trigger_pro_notice" style="display: none;"><?php echo esc_html__('Pro Notice', 'swatchly') ?></a> 
        <div id="swatchly_pro_notice" style="display: none;">
            <div class="swatchly_pro_notice_wrapper">
                <h3><?php echo esc_html__('Pro Version is Required!', 'swatchly') ?></h3>
                <p><?php echo esc_html__('This feature is available in the pro version.', 'swatchly') ?></p>
                <a target="_blank" href="https://hasthemes.com/plugins/swatchly-product-variation-swatches-for-woocommerce-products/"><?php echo esc_html__('More Details', 'swatchly') ?></a>
            </div>
        </div>
    <?php
    }

    // Recommended plugin page after activating the plugin
    public function quick_recommended_plugin(){
        wp_enqueue_script('ht-install-manager');

            $plugin_slug = 'woolentor-addons';
            $plugin_file = 'woolentor-addons/woolentor_addons_elementor.php';

            // Installed but Inactive.
            if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) && is_plugin_inactive( $plugin_file ) ) {

                $button_classes = 'button ht-activate-now button-primary';
                $button_text    = esc_html__( 'Active Now', 'swatchly' );

            // Not Installed.
            } elseif ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {

                $button_classes = 'button ht-install-now button-primary';
                $button_text    = esc_html__( 'Active Now', 'swatchly' );

            // Active.
            } else {
                $button_classes = 'button disabled';
                $button_text    = esc_html__( 'Activated', 'swatchly' );
            }

            $data_attr = array(
                'slug'      => $plugin_slug,
                'location'  => $plugin_file,
                'name'      => '',
            );
        ?>
        <!-- ht-quick-recommended-plugin-area -->
        <div class="ht-qrp-area">
            <div class="ht-qrp">
                <div class="ht-qrp-body">
                    <div class="ht-qrp-logo">
                        <img src="<?php echo esc_url(SWATCHLY_ASSETS . '/images/woolentor-logo.png') ?>" alt="">
                    </div>
                    <p><?php echo wp_kses_post(__('<span>Want to have complete control over the dull designs of all WooCommerce default pages and create an eye-catching WooCommerce store?</span> If you are interested, don\'t forget to try out the free version of the WooLentor today!', 'swatchly')) ?></p>
                    <button class="<?php echo esc_attr($button_classes); ?>" 
                        data-slug='<?php echo esc_attr($data_attr['slug']); ?>' 
                        data-location="<?php echo esc_attr($data_attr['location']); ?>"
                        data-progress_message="<?php echo esc_attr__('Activating..', 'swatchly') ?>" 
                        data-redirect_after_activate="<?php echo esc_url(admin_url('admin.php?page=swatchly-admin')) ?>">
                        <?php echo esc_html($button_text); ?>
                    </button>
                </div>
            </div>
        </div> <!-- .ht-quick-recommended-plugin-area -->
        <?php
    }
}