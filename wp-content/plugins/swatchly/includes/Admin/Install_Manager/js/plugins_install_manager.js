/**
 * Plugins Install manager JS
 */
;( function ( $ ) {
    /*
    * Plugin Installation Manager
    */
    var plugin_install_manager = {

        init: function(){
            $( document ).on('click','.ht-install-now', plugin_install_manager.install_now );
            $( document ).on('wp-plugin-installing', plugin_install_manager.installing_process);
            $( document ).on('wp-plugin-install-success', plugin_install_manager.installing_success);
            $( document ).on('wp-plugin-install-error', plugin_install_manager.installing_error);
            $( document ).on('click','.ht-activate-now', plugin_install_manager.activate_plugin);
        },


        /* 
         * Install the plugin.
         * Button with .ht-install-now class will install the plugin
         */
        install_now: function(e){
            e.preventDefault();

            var $button = $( e.target ),
                message = $button.data('progress_message');

            if( !message ){
                message = wp.updates.l10n.install_now;
            }

            if ( $button.hasClass( 'updating-message' ) || $button.hasClass( 'button-disabled' ) ) {
                return;
            }

            /*
             * This is a WordPress core function. It is used to check if the user has permission to
             * install the plugin. If the user has permission, then the plugin will be installed.
             * 
             */
            if ( wp.updates.shouldRequestFilesystemCredentials && !wp.updates.ajaxLocked ) {
                wp.updates.requestFilesystemCredentials( e );
                
                $( document ).on( 'credential-modal-cancel', function() {
                    var $button = $( '.ht-install-now.updating-message' );
                    $button.removeClass( 'updating-message' ).text( message );
                    wp.a11y.speak( wp.updates.l10n.updateCancel, 'polite' );
                });
            }

            // Finally install the plugin
            wp.updates.installPlugin( {
                slug: $button.data( 'slug' )
            });
        },

        /**
         * Installing Process
         */
         installing_process: function(e, args){
            e.preventDefault();
            var $button = $('.ht-install-now[data-slug="'+ args.slug +'"]'),
                message = $button.data('progress_message');

                if( !message ){
                    message = htim_params.i18n.installing;
                }

                $button.text( message ).addClass( 'updating-message' );
        },

        /**
         * After Plugin Install success
         */
         installing_success: function( e, response ) {
            var $button     = $('.ht-install-now[data-slug="'+ response.slug +'"]'),
                plugin_file = $button.data('location'),
                redirect_to = $button.data('redirect_after_activate');

            $button.removeClass( 'install-now installed button-disabled updated-message' )
                .addClass( 'updating-message' )
                .html( htim_params.i18n.activating );

            setTimeout( function() {
                $.ajax( {
                    url: htim_params.ajaxurl,
                    type: 'POST',
                    data: {
                        action   : 'htim_activate_plugin',
                        location : plugin_file,
                    },
                } ).done( function( result ) {
                    if ( result.success ) {
                        $button.removeClass( 'button-primary ht-install-now ht-activate-now updating-message' )
                            .attr( 'disabled', 'disabled' )
                            .addClass( 'disabled' )
                            .text( htim_params.i18n.activated );

                        if( redirect_to ){
                            setTimeout(function(){
                                window.location.href = redirect_to;
                            }, 100);
                        }

                    } else {
                        $button.removeClass( 'updating-message' );
                    }

                });

            }, 1200 );
        },

        /**
         * Installation Error.
         */
        installing_error: function( e, response ) {
            e.preventDefault();

            var $button = $('.ht-install-now[data-slug="'+ response.slug +'"]');
            $button.removeClass( 'button-primary' ).addClass( 'disabled' ).html( wp.updates.l10n.installFailedShort );
        },

        /* 
         * Activate the plugin.
         * Button with .ht-activate-now class will activate the plugin
         */
        activate_plugin: function( e, response ) {
            e.preventDefault();

            var $button     = $( e.target ),
                plugin_file = $button.data('location'),
                message     = $button.data('progress_message'),
                redirect_to = $button.data('redirect_after_activate');

            if ( $button.hasClass( 'updating-message' ) || $button.hasClass( 'button-disabled' ) ) {
                return;
            }

            if( !message ){
                message = htim_params.i18n.activating;
            }

            $button.addClass( 'updating-message button-primary' ).html( message );

            $.ajax( {
                url: htim_params.ajaxurl,
                type: 'POST',
                data: {
                    action   : 'htim_activate_plugin',
                    location : plugin_file,
                },
            }).done( function( response ) {
                if ( response.success ) {
                    $button.removeClass( 'button-primary ht-install-now ht-activate-now updating-message' )
                        .attr( 'disabled', 'disabled' )
                        .addClass( 'disabled' )
                        .text( htim_params.i18n.activated );

                    if( redirect_to ){
                        setTimeout(function(){
                            window.location.href = redirect_to;
                        }, 100);
                    }
                }
            });
        },
    };

    /**
     * Initialize plugin_install_manager
     */
    $( document ).ready( function() {
        plugin_install_manager.init();
    });

} )( jQuery );