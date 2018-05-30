<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $product, $etheme_global, $post;

$zoom = etheme_get_option('zoom_effect');
if( class_exists( 'YITH_WCWL_Init' ) ) {
	add_action( 'woocommerce_single_product_summary', create_function( '', 'echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );' ), 31 );
}
remove_all_actions( 'woocommerce_product_thumbnails' );

$etheme_global['zoom'] = 'disable';

$args['images']   	  = etheme_get_option( 'quick_images' );
$args['descr'] 		  = etheme_get_option( 'quick_descr' );
$args['name'] 		  = etheme_get_option( 'quick_product_name' );
$args['rating']		  = etheme_get_option( 'quick_rating' );
$args['price'] 		  = etheme_get_option( 'quick_price' );
$args['cart_btn']     = etheme_get_option( 'quick_add_to_cart' );
$args['share'] 		  = etheme_get_option( 'quick_share' );
$args['share_icon']	  = etheme_get_option( 'share_icons' );
$args['link'] 		  = etheme_get_option( 'product_link' );
$args['class'] = ( $args['descr'] || $args['name']  || $args['rating'] || $args['cart_btn'] || $args['share'] || $args['link'] ) ? 'on' : 'off';

?>

    <div class="row">
        <div class="col-md-12 col-sm-12 product-content information-<?php echo $args['class']; ?>">
            <div class="row">

	            <?php if ( $args['images'] != 'none'): ?>
	                <div class="col-lg-6 col-sm-6 product-images<?php echo ( $args['images'] == 'slider' ) ? ' type-slider product-images-slider' : ' type-image'; ?>">
		                <?php if ( $args['images'] == 'slider' ): ?>
		                	<?php
		                		/**
		                		 * woocommerce_before_single_product_summary hook
		                		 *
		                		 * @hooked woocommerce_show_product_sale_flash - 10
		                		 * @hooked woocommerce_show_product_images - 20
		                		 */
		                		do_action( 'woocommerce_before_single_product_summary' );
		                		?>  <script type="text/javascript">
										// jQuery('.quick-view-popup .main-images').imagesLoaded(function(){
											jQuery('.quick-view-popup .main-images').owlCarousel({
												items:1,
												nav: true,
												lazyLoad: true,
												autoHeight: true,
												rewind: false,
												addClassActive: true,
												responsive: {
													1600: {
														items: 1
													}
												}
											});
									// });
					           	 	</script>	 <?php
		                	?>
		                <?php else:
		                	$default_src = wp_get_attachment_image_src( get_post_thumbnail_id(), apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ));
							echo get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
							'title' => get_post_field( 'post_title', get_post_thumbnail_id()) , 'data-thumbnail-src' => $default_src[0] )
							); ?>
		                <?php endif; ?>
	                </div><!-- Product images/ END -->
				<?php endif; ?>

                <?php if ( $args['class'] == 'on' ) : ?>
	                <div class="col-lg-<?php if ( $args['images'] != 'none'): ?>6<?php else: ?>12<?php endif; ?> col-sm-6 product-information">
	                	<?php if ( $args['descr'] ): ?>
		                    <div class="product-navigation clearfix">
								<h4 class="zlarge-h meta-title"><span><?php esc_html_e( 'Product Description', 'woopress' ); ?></span></h4>
							</div>
	                	<?php endif; ?>

						<?php // ! nead name for ajax add to cart modal ?>
						<h3 class="product-name test-triggers<? if ( ! $args['name'] ) echo ' hidden'; ?>"><?php the_title(); ?></h3>

						<?php if ( $args['rating'] ): ?>
							<?php woocommerce_template_loop_rating(); ?>
						<?php endif ?>

						<?php if ( $args['price'] ): ?>
							<?php woocommerce_template_single_price(); ?>
						<?php endif; ?>

						<?php if ( $args['descr'] ): ?>
							<?php woocommerce_template_single_excerpt(); ?>
						<?php endif; ?>

						<?php if ( $args['cart_btn']  ): ?>
							<?php woocommerce_template_single_add_to_cart(); ?>
						<?php endif; ?>

						<?php if ( $args['share'] ): ?>
				        	<?php if( $args['share_icon'] ) echo do_shortcode( '[share]' ); ?>
						<?php endif; ?>

						<?php if ( $args['link'] ): ?>
				        	<a href="<?php the_permalink(); ?>" class="show-full-details"><?php esc_html_e( 'Show full details', 'woopress' ); ?></a>
						<?php endif; ?>
	                </div><!-- Product information/ END -->
	            <?php endif; ?>
            </div>

        </div> <!-- CONTENT/ END -->
    </div>
