/**
 * Swatchly Frontend JS
 */
;( function ( $ ) {
	"use strict";

	if ( typeof swatchly_params === 'undefined' ) {
		return false;
	}

	/* Tooltip JS
	======================================================= */
	function addTooltip(){
		var image_src = $(this).data('tooltip_image'),
			text = $(this).data('tooltip_text');

		if(image_src && text){
			$(this).append(`<div class="swatchly-tooltip"><span class="swatchly-tooltip-text">${text}</span><img src="${image_src}" /></div>`);
		} else if(image_src){
			$(this).append(`<div class="swatchly-tooltip"><img src="${image_src}" /></div>`);
		} else if(text){
			$(this).append(`<div class="swatchly-tooltip"><span class="swatchly-tooltip-text">${text}</span></div>`);
		}
	}

	function removeTooltip(e){
		$('.swatchly-tooltip').remove();
	}

	$(document).ready(function() {
		$(document).on( 'mouseover', 'div.swatchly-swatch,a.swatchly-swatch', addTooltip );
		$(document).on('mouseleave', 'div.swatchly-swatch,a.swatchly-swatch', removeTooltip);
	});
	
	/* For both Single & Product loop
	======================================================= */
	$(document).ready(function(){
		$('.swatchly-hide-this-variation-row').closest('tr').addClass('swatchly_d_none');
	});

	/* Product Loop JS
	======================================================= */
	var product_loop = {
	    /* 
		 * Some thems make the full product loop item linked to the product details. 
		 * Preventing the user from clicking on the product loop item so the swatches will be clickable.
		 */
		prevent_click: function(){
			$('.swatchly_loop_variation_form').on( 'click', function(e){
				if( $(e.target).is('.swatchly-more-button') || $(e.target).closest('.swatchly-more-button').length ){
					return;
				}
				
				e.preventDefault();
				e.stopPropagation();
				e.stopImmediatePropagation();
			});
		},
		init_variation_form: function(){
			var enable_swatches = Boolean(Number(swatchly_params.enable_swatches));

		    if(enable_swatches){
		        $( '.swatchly_loop_variation_form' ).swatchly_loop_variation_form();
		    }
		}
	}

	$(document).ready(function(){
		$('.swatchly_loop_variation_form').addClass('swatchly_loaded_on_ready');
		product_loop.prevent_click();
	});

	// Reset the changes
	$.fn.reset = function (event){
		$(this).find('.swatchly_ajax_add_to_cart').removeClass('alt disabled wc-variation-is-unavailable swatchly_found_variation added');

		if(event === 'click'){
			var $button_text = $(this).find('.swatchly_ajax_add_to_cart').data('select_options_text')
			$(this).find('.swatchly_ajax_add_to_cart').text($button_text);
		}

		// remove veiw cart button
		$(this).find('.added_to_cart.wc-forward').remove();
		$(this).remove_out_of_stock();

		// hide reset button
		$(this).find('.reset_variations').attr('style', 'display: none !important');
	};

	// Reset to the initial price
	$.fn.reset_to_default_price = function(){
		$(this).find('.price').first().removeClass('swatchly_d_none');
		$(this).find('.swatchly_price').remove();
	};

	// Don't show the out of stock element
	$.fn.remove_out_of_stock = function (){
		if($(this).find('.swatchly_pl.swatchly_out_of_stock')){
			$(this).find('.swatchly_pl.swatchly_out_of_stock').remove();
		}
	}

	// Find the product image selector
	$.fn.get_product_image_selector = function(){
		var $product_thumbnail = '',
			product_thumbnail_selector = swatchly_params.product_thumbnail_selector;

		// custom selector priority first
		if(product_thumbnail_selector){
			$product_thumbnail = $(this).find(product_thumbnail_selector);

			if( !$product_thumbnail.length && $(this).closest('.product').find(product_thumbnail_selector).length ){
				$product_thumbnail = $(this).closest('.product').find(product_thumbnail_selector);
			}

			return $product_thumbnail;
		}

		// Check if product has slider
		var has_slider = false;
		if($(this).find('.gallery-image').length){
			has_slider = true;
			return;
		}

		// look for default wc image selector
		$product_thumbnail = $(this).find('img.attachment-woocommerce_thumbnail');

		// ocean theme support
		if($product_thumbnail.length === 0){
			$product_thumbnail = $(this).find('img.woo-entry-image-main');

			// look for other default seletors
			if($product_thumbnail.length === 0){
				$product_thumbnail = $(this).find('img.attachment-woocommerce_thumbnail');

				if($product_thumbnail.length === 0){
					$product_thumbnail = $(this).find('img.wp-post-image');

					if($product_thumbnail.length === 0){
						$product_thumbnail = $(this).find('img').first();
					}
				}
			}
		}

		// Support WL Universal grid image slider
		if($(this).find('.ht-product-image-slider').length !== 0 ){
			$product_thumbnail = $(this).find('.slick-slide.ht-slider-first-item img');
		}

		return $product_thumbnail;
	};

	// Backup product image properties
	// Needed when reset variation & show the default image
	$.fn.backup_product_image = function(){
		var $product_thumbnail = $(this).get_product_image_selector();

		// If the product thumbail does not exists, do nothing
		if(!$product_thumbnail){
			return;   
		}

		// Clone & set default image's properties
		var backup_attributes = {
			"data-backup_alt": $product_thumbnail.attr('alt'),
			"data-backup_src": $product_thumbnail.attr('src'),
			"data-backup_width": $product_thumbnail.attr('width'),
			"data-backup_height": $product_thumbnail.attr('height')
		}

		if( $product_thumbnail.attr('srcset') ) {
		    backup_attributes["data-backup_srcset"] = $product_thumbnail.attr( 'srcset' );
		    backup_attributes["data-backup_sizes"] = $product_thumbnail.attr( 'sizes' );
		}

		$product_thumbnail.attr(backup_attributes);
	};

	// Change the product image when variation found
	$.fn.change_image = function(variation){
		// image selector
		var $product_thumbnail = $(this).get_product_image_selector();

		// If the product thumbail does not exists, do nothing
		if(!$product_thumbnail){
			return;   
		}

		var attributes = {
		    alt: variation.image.alt,
		    src: variation.image.thumb_src,
		    width: variation.image.thumb_src_w,
		    height: variation.image.thumb_src_h
		};

		if( $product_thumbnail.attr('srcset') ) {
		    attributes.srcset = variation.image.srcset;
		    attributes.sizes = variation.image.sizes;
		}

		// Finally change/update image
		$product_thumbnail.attr(attributes);

		// Support WL Universal grid image slider
		if( $(this).find('.ht-product-image-slider').length !== 0 ){
			$(this).find('.ht-product-image-slider').slick('slickGoTo', 0);
		}
	};

	// Reset to the default image when click on "reset" button
	$.fn.reset_to_default_image = function(){
		// Image selector
		var $product_thumbnail = $(this).get_product_image_selector();

		// Get backup attributes before reset
		var backup_attributes = {
			alt: $product_thumbnail.attr('data-backup_alt'),
			src: $product_thumbnail.attr('data-backup_src'),
			width: $product_thumbnail.attr('data-backup_width'),
			height: $product_thumbnail.attr('data-backup_height')
		}

		if( $product_thumbnail.attr('srcset') ) {
			backup_attributes["srcset"] = $product_thumbnail.attr( 'data-backup_srcset' );
			backup_attributes["sizes"]  = $product_thumbnail.attr( 'data-backup_sizes' );
		}

		// Finally reset the image
		$product_thumbnail.attr(backup_attributes);

		// Support WL Universal grid image slider
		if( $(this).find('.ht-product-image-slider').length !== 0 ){
			$(this).find('.ht-product-image-slider').slick('slickGoTo', 0);
		}
	};

	// Change add to cart button text
	$.fn.change_add_to_cart_text = function(){
		var original_cart_text = $(this).text(),
			new_cart_text = $(this).data('add_to_cart_text');

		$(this).html($(this).html().replace(original_cart_text, new_cart_text));
	};

	// Function for each loop variation form
	$.fn.swatchly_loop_variation_form = function(){
		var $price_selector = '.price';

		return this.each( function(){
			var $el_variation_form = $( this ),
				$el_product = $el_variation_form.closest('.product'),
				$el_ajax_add_to_cart = $el_product.find('.swatchly_ajax_add_to_cart');

			$el_product.backup_product_image();

			// hide reset button by default
			$el_product.find('.reset_variations').attr('style', 'display: none !important');

			$el_variation_form.on('found_variation', function(e, variation){
				$el_product.reset();

				var availability_html = variation.availability_html,
					is_in_stock = variation.is_in_stock;

				// If out of stcok
				if(!is_in_stock){
					$(this).append(`<div class="swatchly_pl swatchly_out_of_stock">${availability_html}</div>`);
				} else {
					$el_product.remove_out_of_stock();
				}

				// show reset buton once a vaiation found
				$el_product.find('.reset_variations').attr('style', '');

				// Update price
				if( !$el_product.find('.swatchly_price').length ){
					if($(variation.price_html).length){
						$el_product.find($price_selector).addClass('swatchly_d_none').after( $(variation.price_html).addClass('swatchly_price') );
					}
				} else {
					$el_product.find('.swatchly_price').remove();
					$el_product.find($price_selector).addClass('swatchly_d_none').after($(variation.price_html).addClass('swatchly_price'));
				}

				// Update cart text
				if( $el_ajax_add_to_cart.length ){
					$el_ajax_add_to_cart.change_add_to_cart_text('found_variation');
				}

				// Update Image
				$el_product.change_image(variation);

				// For ajax add to cart
				// Manually generate selected variation attributes
				var selected_variation = {},
					variations = $(this).find( 'select[name^=attribute]' );
				if ( !variations.length) {
				    variations = $(this).find( '[name^=attribute]:checked' );
				}
				if ( !variations.length) {
				    variations = $(this).find( 'input[name^=attribute]' );
				}

				variations.each( function() {
				    var $this_item = $( this ),
				        attribute_name = $this_item.attr( 'name' ),
				        attribute_value = $this_item.val(),
				        index,
				        attribute_tax_name;
				        $this_item.removeClass( 'error' );
				    if ( attribute_value.length === 0 ) {
				        index = attribute_name.lastIndexOf( '_' );
				        attribute_tax_name = attribute_name.substring( index + 1 );
				        $this_item.addClass( 'required error' );
				    } else {
				        selected_variation[attribute_name] = attribute_value;
				    }
				});

				if($el_ajax_add_to_cart.length){
					$el_ajax_add_to_cart.addClass('swatchly_found_variation');
				}
				$el_ajax_add_to_cart.attr('data-variation_id', variation.variation_id);
				$el_ajax_add_to_cart.attr('data-variation', JSON.stringify(selected_variation));

				if($el_ajax_add_to_cart.hasClass('added')){
					$el_ajax_add_to_cart.removeClass('added');
				}

			}).on('click','.reset_variations', function(e){
				$el_product.reset('click');
				$el_product.remove_out_of_stock();
				$el_product.reset_to_default_price();
				$el_product.reset_to_default_image();

			});
		});
	};

	// Product loop init
	$(window).on('load', function(){
	    product_loop.init_variation_form();
	} );

	/*=====  End of Product Loop JS  ======*/
	

	/* Single product page JS
	======================================================= */
	var single_product = {
	    init: function(){
			var enable_swatches           	  = Boolean(Number(swatchly_params.enable_swatches)),
				sp_override_global            = Boolean(Number(swatchly_params.sp_override_global)),
				pl_override_global            = Boolean(Number(swatchly_params.pl_override_global)),
				enable_variation_url          = Boolean(Number(swatchly_params.enable_variation_url)),
				enable_sp_variation_url       = Boolean(Number(swatchly_params.enable_sp_variation_url)),
				enable_pl_variation_url       = Boolean(Number(swatchly_params.enable_pl_variation_url)),
				is_product            		  = Boolean(Number(swatchly_params.is_product)),
				deselect_on_click             = Boolean(Number(swatchly_params.deselect_on_click)),
				show_selected_attribute_name  = Boolean(Number(swatchly_params.show_selected_attribute_name)),
				variation_label_separator     = swatchly_params.variation_label_separator;

			if( enable_swatches){
				$.fn.swatchly_variation_form = function(){
					return this.each( function(){
						var $el_variation_form = $( this );

						// Actions while select a swatch
						$el_variation_form.on( 'click', 'div.swatchly-swatch', function ( e ) {
							var $el_swatch = $( this ),
								$el_default_select = $el_swatch.closest( '.value' ).find( 'select' ),
								value   = $el_swatch.attr( 'data-attr_value' );

							// Add url parameter in product link using selected attribute
							if(!is_product && ((pl_override_global && enable_pl_variation_url) || (!pl_override_global && enable_variation_url))) {
								const product = e.target.closest( '.product' ),
									productLink = product.querySelector('.woocommerce-loop-product__link') || product.querySelector('a'),
									links = product.querySelectorAll('a');
								Array.from(links).forEach(link => {
									const url = new URL(link.href),
										productUrl = new URL(productLink.href);
									if(url.pathname === productUrl.pathname || url.href.indexOf('/product/') !== -1) {
										const paramName = e.target.closest( '.value' ).querySelector('select').dataset.attribute_name;
										if(!$el_swatch.hasClass('swatchly-selected')) {
											if(!url.searchParams.get(paramName)) {
												url.searchParams.append(paramName, value);
											} else {
												url.searchParams.set(paramName, value)
											}
										} else {
											url.searchParams.delete(paramName)
										}
										link.href = url.toString();
									}
								});
							}

							if(is_product && ((sp_override_global && enable_sp_variation_url) || (!sp_override_global && enable_variation_url))) {
								const url = new URL(location),
									paramName = e.target.closest( '.value' ).querySelector('select').dataset.attribute_name;
								if(!$el_swatch.hasClass('swatchly-selected')) {
									if(!url.searchParams.get(paramName)) {
										url.searchParams.append(paramName, value);
									} else {
										url.searchParams.set(paramName, value)
									}
								} else {
									url.searchParams.delete(paramName)
								}
								history.pushState({}, '', url.href);
							}
								
							if( !deselect_on_click ){
								// Add selected class & remove siblings selected class
								$el_swatch.addClass('swatchly-selected').siblings('div.swatchly-swatch').removeClass('swatchly-selected');

								// Show selected variation name
								if( show_selected_attribute_name ){
									if($el_swatch.closest('tr').find('.swatchly_selected_variation_name').length){
										$el_swatch.closest('tr').find('.swatchly_selected_variation_name').text( variation_label_separator + $el_swatch.data('attr_label') );
									} else {
										$el_swatch.closest('tr').find('.label label').append('<span class="swatchly_selected_variation_name">'+ variation_label_separator + $el_swatch.data('attr_label') +'</span>');
									}
								}

							} else {

								if($el_swatch.hasClass('swatchly-selected')){

									// Remove selection
									$el_swatch.removeClass('swatchly-selected');

									// Change select field value to empty
									value = '';

									// Remove selected variation name
									$el_swatch.closest('tr').find('.swatchly_selected_variation_name').text( '' );

								} else {

									// Add selected class & remove siblings selected class
									$el_swatch.addClass('swatchly-selected').siblings('div.swatchly-swatch').removeClass('swatchly-selected');

									// Show selected variation name
									if( show_selected_attribute_name ){
										if($el_swatch.closest('tr').find('.swatchly_selected_variation_name').length){
											$el_swatch.closest('tr').find('.swatchly_selected_variation_name').text( variation_label_separator + $el_swatch.data('attr_label') );
										} else {
											$el_swatch.closest('tr').find('.label label').append('<span class="swatchly_selected_variation_name">'+ variation_label_separator + $el_swatch.data('attr_label') +'</span>');
										}
									}

								}
							}

							$el_default_select.val(value);
							$el_default_select.change();
							
						})
						.on('change', '.value select', function(e) {
							const paramName = $(this)[0].dataset.attribute_name,
								value = $(this)[0].value;
							// Add url parameter in product link using selected attribute
							if(!is_product && ((pl_override_global && enable_pl_variation_url) || (!pl_override_global && enable_variation_url))) {
								const product = e.target.closest( '.product' ),
									productLink = product.querySelector('.woocommerce-loop-product__link') || product.querySelector('a'),
									links = product.querySelectorAll('a');
								Array.from(links).forEach(link => {
									const url = new URL(link.href),
										productUrl = new URL(productLink.href);
									if(url.pathname === productUrl.pathname || url.href.indexOf('/product/') !== -1) {
										if(value) {
											if(!url.searchParams.get(paramName)) {
												url.searchParams.append(paramName, value);
											} else {
												url.searchParams.set(paramName, value)
											}
										} else {
											url.searchParams.delete(paramName)
										}
										link.href = url.toString();
									}
								});
							}

							if(is_product && ((sp_override_global && enable_sp_variation_url) || (!sp_override_global && enable_variation_url))) {
								const url = new URL(location);
								if(value) {
									if(!url.searchParams.get(paramName)) {
										url.searchParams.append(paramName, value);
									} else {
										url.searchParams.set(paramName, value)
									}
								} else {
									url.searchParams.delete(paramName)
								}
								history.pushState({}, '', url.href);
							}
						})
						.on( 'woocommerce_update_variation_values', function() {
							setTimeout( function() {

								// Loop through each variation row
								$el_variation_form.find( 'tbody tr' ).each( function() {
									var $tr = $(this),
									values = [];

									// Set default attribute label
									if( show_selected_attribute_name && !$tr.find('.swatchly_selected_variation_name').length ){
										var default_attr_label = $tr.find('.swatchly-type-wrap').attr('data-default_attr_value');

										if(default_attr_label){
											$tr.find('.label label').append('<span class="swatchly_selected_variation_name">'+ variation_label_separator + default_attr_label +'</span>');
										} else {
											$tr.find('.label label').append('<span class="swatchly_selected_variation_name"></span>');
										}
										
									}

									// List all attribute values
									$tr.find('select').find('option').each(function(index, option){
										values.push( option.value );
									});

									// Disable unavailable swatches
									$tr.find( 'div.swatchly-swatch' ).each( function() {
										var $el_swatch = $( this ),
											value = $el_swatch.attr( 'data-attr_value' );

										if( values.indexOf( value ) == -1 ){
											$el_swatch.addClass('swatchly-disabled');
										} else {
											$el_swatch.removeClass('swatchly-disabled');
										}
									});

								}); // tbody tr each
							}, 100 ); // timeout

							// Update price for product loop
							var $price_selector = '.price',
								$el_product = $el_variation_form.closest('.product');
							if(!swatchly_params.is_product){
								if($el_product.find('.swatchly_price').length){
									$el_product.find($price_selector).removeClass('swatchly_d_none');
									$el_product.find('.swatchly_price').remove();
								}
							}
						})
						.on('found_variation', function(e, variation){
							// some user use single product add to cart button in the product loop
							// so we need to change the image for the product loop
							if( !swatchly_params.is_product ){
								var $product_thumbnail = $(this).get_product_image_selector();

								let $el_product = $(this).closest('.product');
								$el_product.backup_product_image();
	
								$product_thumbnail.attr('src', variation.image.url);
								$product_thumbnail.attr('srcset', variation.image.srcset);
							}
						})
						.on( 'click', '.reset_variations', function () {
							const url = new URL(location);
							$el_variation_form.find( '.value select' ).each(function() {
								url.searchParams.delete($(this)[0].name);
							})
							history.pushState({}, '', url.href);
							$el_variation_form.find( '.swatchly-selected' ).removeClass( 'swatchly-selected' );
							$el_variation_form.find( '.swatchly-disabled' ).removeClass( 'swatchly-disabled' );
							$el_variation_form.find('.swatchly_selected_variation_name').text( '' );

							// some user use single product add to cart button in the product loop
							// so we need to reset the image for the product loop
							if( !swatchly_params.is_product ){
								let $el_product = $(this).closest('.product');
								$el_product.reset_to_default_image()
							}
						}); // on click div.swatchly-swatch
					});
				}

				// Do stuffs for each variations form
				$( function () {
					$( '.variations_form:not(.swatchly_variation_form), .product-type-bundle form.bundle_form:not(.swatchly_variation_form)' ).addClass('swatchly_variation_form').swatchly_variation_form();
				} );

				// All major quick view plugin support
				$(document).ajaxComplete(function (event, request, settings) {	
					$( '.variations_form:not(.swatchly_variation_form), .product-type-bundle form.bundle_form:not(.swatchly_variation_form)' ).addClass('swatchly_variation_form').swatchly_variation_form();
				});
			}
		}
	}

	// Single product init
	single_product.init();
	

	/* Third party plugin/theme's compatibility
	======================================================= */

	/**
	 * 1. annasta Woocommerce Product Filters
	 */
	 $( document ).on( 'awf_after_ajax_products_update', function() {
		product_loop.prevent_click();
		product_loop.init_variation_form();
		single_product.init();
	});

	/**
	 * 2. Jet Smart Filters
	 */
	 $( document ).on( 'jet-filter-content-rendered', function() {
		product_loop.prevent_click();
		product_loop.init_variation_form();
		single_product.init();
	});

	/**
	 * 3. Woolentor Support
	 */
	$( document ).on('woolentor_quick_view_rendered', function(){
		product_loop.prevent_click();
		product_loop.init_variation_form();
		single_product.init();
	});

	/**
	 * 4. infiniteScroll Support
	 * It's oly work under document.ready and on 'append.infiniteScroll'
	 */
	$(document).ready(function(){
		$('.products').on('append.infiniteScroll', function(){
			product_loop.prevent_click();
			product_loop.init_variation_form();
			single_product.init();
		});
	});

	/**
	 * 5. YITH infiniteScroll Support
	 */
	$(document).on('yith_infs_added_elem', function(){
		product_loop.prevent_click();
		product_loop.init_variation_form();
		single_product.init();
	});

	/**
	 * 6. Barn2 WCF Product Filter
	 */
	$(window).on('load', function(){
		if( $('.wcf-filter-form').length && typeof window.wp.hooks.addAction !== 'undefined' ){
			window.wp.hooks.addAction( 'wcpf.filteringDone', 'namespace', function( config, filteredDOM ){
				product_loop.prevent_click();
				product_loop.init_variation_form();
				single_product.init();
			} );
		}
	});

	/**
	 * 7. Astra Pro quick view Support
	 */
	$( document ).on('ast_quick_view_loader_stop', function(){
		product_loop.prevent_click();
		product_loop.init_variation_form();
		single_product.init();
	});

	/**
	 * Themes compatibility
	 * 1. Airi theme
	 * It is intended to add compatibility for the airi theme's infinite scroll
	 */
	$( document ).on('ajaxComplete', function (event, jqxhr, settings) {
		$('.swatchly_loop_variation_form:not(.swatchly_loaded_on_ready)').each(function(){
			product_loop.prevent_click();
			product_loop.init_variation_form();
			single_product.init();
		});
	});
})(jQuery);