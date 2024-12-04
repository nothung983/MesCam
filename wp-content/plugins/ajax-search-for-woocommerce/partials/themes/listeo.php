<?php
// Exit if accessed directly
if ( ! defined( 'DGWT_WCAS_FILE' ) ) {
	exit;
}

// Remove search icon from theme.
add_action( 'init', function () {
	remove_shortcode( 'listeo_search_form' );
	add_shortcode( 'listeo_search_form', function () {
		return do_shortcode( '[fibosearch]' );
	} );
} );


add_action( 'wp_footer', function () {
	$breakpoint    = DGWT_WCAS()->settings->getOption( 'mobile_breakpoint', 992 );
	$mobileOverlay = DGWT_WCAS()->settings->getOption( 'enable_mobile_overlay' ) === 'on';
	?>
	<script>
		(function ($) {

			function fiboListeoThemeFocusInput() {
				$('.mobile-search-trigger').on('click', function (e) {
					if (!$('.mobile-search-trigger').hasClass('visible')) {
						return;
					}
					setTimeout(function () {
						var $input = $('.header-search-container .dgwt-wcas-search-wrapp:not(.dgwt-wcas-layout-icon) .dgwt-wcas-search-input');
						if ($input.length > 0 && $input.val().length === 0) {
							$input.trigger('focus');
						}
					}, 400);
				});
			}

			$(window).on('load', function () {
				<?php if($mobileOverlay): ?>

				// Search icon - mobile
				if ($(window).width() <= <?php echo $breakpoint; ?>) {
					$('.mobile-search-trigger').off('click').on('click', function (e) {
						var $handler = $('.header-search-container .js-dgwt-wcas-enable-mobile-form');
						if ($handler.length) {
							$handler[0].click();
						}
					});
				} else {
					// Search icon - almost desktop
					fiboListeoThemeFocusInput();
				}
				<?php else: ?>
				// Search icon - almost desktop
				fiboListeoThemeFocusInput();
				<?php endif; ?>
			});
		}(jQuery));
	</script>
	<?php
} );
