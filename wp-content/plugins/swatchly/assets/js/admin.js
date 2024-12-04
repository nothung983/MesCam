/**
 * Admin JS
 */
;( function ( $ ) {
    'use strict';

    /**
     * Ajax request on product variation changes
     */
     $('#woocommerce-product-data').on('woocommerce_variations_loaded', function () {
     	var data = {
     		action: 'swatchly_ajax_reload_metabox_panel',
     		product_id: swatchly_params.product_id,
     		nonce: swatchly_params.nonce,
     	};

     	$.ajax({
     		type: 'POST',
     		url:  woocommerce_admin.ajax_url,
     		data: data,
     		beforeSend: function(){},
     		success: function ( response ) {
     			$('#swatchly_swatches_product_data .wc-metaboxes').html(response.data);

     			// Reinitialize color picker
     			if($('.swatchly_color_picker').length){
     				$( ".swatchly_color_picker" ).wpColorPicker();
     			}
     		},
     		error: function(errorThrown) {
     		    console.log(errorThrown);
     		},
     	});
     });

    /**
     * Ajax request on product metabox save swatches
     */
    $('.swatchly_save_swatches').on('click', function(e){
    	e.preventDefault();

    	var $message = $('.swatchly.wc-metaboxes-wrapper .woocommerce-message'),
    	    $product_data = $('#woocommerce-product-data'),
            data = {
                action: 'swatchly_ajax_save_product_meta',
                product_id: swatchly_params.product_id,
                input_fields: $('#swatchly_swatches_product_data').find(':input').serializeJSON({checkboxUncheckedValue: "0"}),
                nonce: swatchly_params.nonce
            };


    	$product_data.block({
    		message: null,
    		overlayCSS: {
    			background: '#fff',
    			opacity: 0.6
    		}
    	});

		$.ajax({
			type: 'POST',
			url:  woocommerce_admin.ajax_url,
			data: data,
			beforeSend: function(){
			    $message.html(`<p>${ swatchly_params.i18n.saving }</p>`);
			},
			success: function ( response ) {
				$message.addClass('updated').html(`<p>${ response.data.message }</p>`).css('display', 'block');
				$product_data.unblock();
			},
			error: function(errorThrown) {
			    console.log(errorThrown);
			},
		});
    });

    /**
     * Reset to default
     */
    $('.swatchly_reset_to_default').on('click', function(e){
        e.preventDefault();

        var $message = $('.swatchly.wc-metaboxes-wrapper .woocommerce-message'),
            $product_data = $('#woocommerce-product-data'),
            data = {
                action: 'swatchly_ajax_reset_product_meta',
                product_id: swatchly_params.product_id,
                nonce: swatchly_params.nonce
            };

        $product_data.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        if( confirm("Are you sure??") ){
            $.ajax({
                type: 'POST',
                url:  woocommerce_admin.ajax_url,
                data: data,
                beforeSend: function(){
                    $message.html(`<p>${ swatchly_params.i18n.saving }</p>`);
                },
                success: function ( response ) {
                    $message.addClass('updated').html(`<p>${ response.data.message }</p>`).css('display', 'block');
                    $product_data.unblock();
                    $product_data.trigger('woocommerce_variations_loaded');
                },
                error: function(errorThrown) {
                    console.log(errorThrown);
                },
            });
        }
    });

    /**
     * Media uploader field
     */
    $( document ).ready( function () {
    	// Only show the "remove image" button when needed
        $( '.swatchly_media_field .swatchly_input' ).each(function(){
            if( !$(this).val() ){
                $(this).siblings('.swatchly_remove_image').hide();
            }
        });

    	$( document ).on( 'click', '.button.swatchly_upload_image', function( event ) {
    		event.preventDefault();

    		var file_frame;
    		var $this = $(this);

    		// If the media frame already exists, reopen it.
    		if ( file_frame ) {
    			file_frame.open();
    			return;
    		}

    		// Create the media frame.
    		file_frame = wp.media.frames.downloadable_file = wp.media({
    			title: swatchly_params.i18n.choose_an_image,
    			button: {
    				text: swatchly_params.i18n.use_image
    			},
    			multiple: false
    		});

    		// When an image is selected, run a callback.
    		file_frame.on( 'select', function() {
    			var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
    			var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

    			$this.closest('.swatchly_media_field').find( '.swatchly_input' ).val( attachment.id );
    			$this.closest('.swatchly_media_field').find( '.swatchly_media_preview' ).html('<img width="60px" height="60x" src="'+ attachment_thumbnail.url +'" alt="" />');
    			$this.closest('.swatchly_media_field').find( '.button.swatchly_remove_image' ).show();
    		});

    		// // Finally, open the modal.
    		file_frame.open();
    	});

    	$( document ).on( 'click', '.button.swatchly_remove_image', function() {
    		var $this = $(this);

    		$this.closest('.swatchly_media_field').find( '.swatchly_input' ).val( '' );
    		$this.closest('.swatchly_media_field').find( '.swatchly_media_preview' ).html('');
    		$this.closest('.swatchly_media_field').find( '.button.swatchly_remove_image' ).hide();

    		return false;
    	});
    });

    /**
     * Color picker field
     */
    if($('.swatchly_color_picker').length){
    	$( ".swatchly_color_picker" ).wpColorPicker();
    }

    /**
     * Auto change swatch types (when click parent)
     */
    $(document).on('change', '.swatchly_swatch_type.swatchly_2', function(e){    
    	var $inner_swatch_types = $(this).closest('.wc-metabox').find('.swatchly_swatch_type.swatchly_1'),
            $wc_metabox1 = $(this).closest('.wc-metabox').find('.wc-metabox.swatchly_1'),
            $wc_metabox2 = $(this).closest('.wc-metabox.swatchly_2');

    	if(this.value == 'label' || this.value == 'color' || this.value == 'image'){
    		// Remove class
    		 $wc_metabox2.removeClass( 'swatchly_type_ swatchly_type_select swatchly_type_label swatchly_type_color swatchly_type_image' );
    		 $wc_metabox1.removeClass( 'swatchly_type_ swatchly_type_select swatchly_type_label swatchly_type_color swatchly_type_image' );

    		// Set curret swatch type class
    		$wc_metabox2.addClass( 'swatchly_type_'+ this.value );
    		$wc_metabox1.addClass( 'swatchly_type_'+ this.value );

    		// Auto select swatch types
    		$inner_swatch_types.val(this.value).change();
    	} else {
    		// Remove current swatch type class
    		 $wc_metabox2.removeClass( 'swatchly_type_label swatchly_type_color swatchly_type_image' );
    		 $wc_metabox1.removeClass( 'swatchly_type_label swatchly_type_color swatchly_type_image' );

    		// Set current swatch type class
    		$wc_metabox2.addClass( 'swatchly_type_'+ this.value );
    		$wc_metabox1.addClass( 'swatchly_type_'+ this.value );

	        // Auto select swatch types
            if( this.value == 'select' ){
                $inner_swatch_types.val(this.value).change();
            } else {
                $inner_swatch_types.val(this.value).change('');
            }
            
    		$inner_swatch_types.attr('selected', 'selected');
    		$wc_metabox2.find('.wc-metabox-content').css('display', 'none');
    	}
    });
 
    /**
     * Disable toggle while swatch type = select
     */
    $(document).on('click','#wpcontent .wc-metabox.swatchly_2.swatchly_type_select > h3,#wpcontent .wc-metabox.swatchly_2.swatchly_type_ > h3', function(e){
    	$(this).closest('.wc-metabox.swatchly_2').find('.wc-metabox-content').css('display', 'none');
    });

    /**
     * Tooltip fields conditon
     */
    $(document).on('change', '.swatchly_tooltip', function(){
    	var $wc_metabox = $(this).closest('.wc-metabox');

		$wc_metabox.removeClass('swatchly_tooltip_ swatchly_tooltip_disable swatchly_tooltip_text swatchly_tooltip_image');
		$wc_metabox.addClass( 'swatchly_tooltip_'+ this.value );
    });

    /**
     * Enable multi color field condition
     */
    $(document).on('change', '.wc-metabox.swatchly_1 .enable_multi_color', function(){
    	var $wc_metabox1 = $(this).closest('.wc-metabox.swatchly_1');
    	var $val = this.checked == true ? '1' : '';

		$wc_metabox1.removeClass('swatchly_enable_multi_color_ swatchly_enable_multi_color_1' );
		$wc_metabox1.addClass( 'swatchly_enable_multi_color_'+ $val );
    });

    /**
     * Active settigns page
     */
    if (typeof swatchly_is_settings_page != "undefined" && swatchly_is_settings_page === 1){
        $('li.toplevel_page_swatchly-admin .wp-first-item').addClass('current');
    }

    /**
     * Pro version notice
     */
    $('.csf .swatchly_pro_notice, .csf .swatchly_pro_opacity, .wc-metaboxes .swatchly_pro_notice').on('click', function(){
        $('.thickbox.swatchly_trigger_pro_notice').click();
    });


    /**
     * Active status of global general settings override
     */
    $(document).ready(function(){
        // generate_active_class function
        $.fn.generate_active_class = function(selector){
            var sp_override_global = parseInt( $('[name="swatchly_options[sp_override_global]').val() ),
                pl_override_global = parseInt( $('[name="swatchly_options[pl_override_global]').val() );

            if( sp_override_global && pl_override_global ){
                $('.csf-nav-options .csf-tab-item:first-child ul li:nth-child(1)').removeClass('swatchly_active');
            } else {
                $('.csf-nav-options .csf-tab-item:first-child ul li:nth-child(1)').addClass('swatchly_active');
            }

            if( parseInt($(this).val()) ){
                $(selector).addClass('swatchly_active');
            } else {
                $(selector).removeClass('swatchly_active');
            }
        };

        if( parseInt(swatchly_params.sp_override_global) && parseInt(swatchly_params.sp_override_global) ){
            $('.csf-nav-options .csf-tab-item:first-child ul li:nth-child(1)').removeClass('swatchly_active');
        } else {
            $('.csf-nav-options .csf-tab-item:first-child ul li:nth-child(1)').addClass('swatchly_active');
        }

        if( parseInt(swatchly_params.sp_override_global) ){
            $('.csf-nav-options .csf-tab-item:first-child ul li:nth-child(2)').addClass('swatchly_active');
        }
        
        if( parseInt(swatchly_params.pl_override_global) ){
            $('.csf-nav-options .csf-tab-item:first-child ul li:nth-child(3)').addClass('swatchly_active');
        }

        $('[name="swatchly_options[sp_override_global]').on('change', function(){
            $(this).generate_active_class('.csf-nav-options .csf-tab-item:first-child ul li:nth-child(2)');
        });

        $('[name="swatchly_options[pl_override_global]').on('change', function(){
            $(this).generate_active_class('.csf-nav-options .csf-tab-item:first-child ul li:nth-child(3)');
        });
    });

} )( jQuery );