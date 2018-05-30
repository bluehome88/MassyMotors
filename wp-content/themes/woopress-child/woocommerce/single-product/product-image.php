<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce, $etheme_global, $product;
$zoom     			= etheme_get_option('zoom_effect');
$lightbox 			= etheme_get_option('gallery_lightbox');
$slider   			= etheme_get_option('images_slider');
$carousel_direction = etheme_get_option('carousel_direction');

if(!empty($etheme_global['zoom'])) {
	$zoom = $etheme_global['zoom'];
}

$has_video = false;

$video_attachments = array();
$videos = et_get_attach_video($product->get_id());
//$videos = explode(',', $videos[0]);
if(isset($videos[0]) && $videos[0] != '') {
	$video_attachments = get_posts( array(
		'post_type' => 'attachment',
		'include' => $videos[0]
	) );
}


if(count($video_attachments)>0 || et_get_external_video($product->get_id()) != '') {
	$has_video = true;
}

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );

$placeholder       = has_post_thumbnail() ? 'with-images' : 'without-images';

$wrapper_classes_array = array(
	'woocommerce-product-gallery',
	'woocommerce-product-gallery--' . $placeholder,
	'woocommerce-product-gallery--columns-' . absint( $columns ),
	'images',
);

if (count($product->get_gallery_image_ids()) == 0) {
	$wrapper_classes_array[] = "full-width-gallery";
}

$wrapper_classes   = apply_filters( 'woocommerce_single_product_image_gallery_classes', $wrapper_classes_array );

?>
<div class="images woocommerce-product-gallery <?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">

	<?php

            $data_rel = '';
			$image_title = get_post_field( 'post_title', get_post_thumbnail_id() );
			$image_link  = wp_get_attachment_url( get_post_thumbnail_id() );
			$image_src = wp_get_attachment_image_src( get_post_thumbnail_id(), apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
			$image_srcset = wp_get_attachment_image_srcset( get_post_thumbnail_id(), apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
			$image       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
				'title' => $image_title
				) );
            $attachment_ids = $product->get_gallery_image_ids();
			$attachment_count = count( $attachment_ids );

			if ( $attachment_count > 0 ) {
				$gallery = '[product-gallery]';
			} else {
				$gallery = '';
			}

            if($lightbox) $data_rel = 'data-rel="gallery' . $gallery . '"';

            ?>

            <figure class="woocommerce-product-gallery__wrapper <?php if ($slider): ?>product-images-slider owl-carousel owl-theme<?php else: ?>product-images-gallery<?php endif ?> main-images images-popups-gallery <?php if ($zoom != 'disable') { echo 'zoom-enabled'; } ?>">
            	<?php if ( has_post_thumbnail() ) { ?>
	            	<div>
		                <?php echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="product-main-image product-image" data-o_href="%s" data-o_srcset="%s" data-thumbnail-src = "%s" data-thumb-src = " " data-o_src="%s" title="%s">%s</a>', $image_link, $image_link, $image_srcset, $image_src[0], $image_src[0], $image_title, $image ), $post->ID ); ?>
		                <?php if($lightbox): ?>
		                	<a href="<?php echo $image_link; ?>" class="product-lightbox-btn" <?php echo $data_rel; ?>>lightbox</a>
		                <?php endif; ?>
	            	</div>
            	<?php } else { ?>
	            	<div>
		                <?php echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="product-main-image product-image" data-o_href="%s"><img src="%s" /></a>', wc_placeholder_img_src(), wc_placeholder_img_src(), wc_placeholder_img_src() ), $post->ID ); ?>
	            	</div>
            	<?php } ?>
                <?php
                	$_i = 0;
                    if($attachment_count > 0) {
            			foreach($attachment_ids as $id) {
            				$_i++;
            				?>
            				<div>
	            				<?php

	                			$image_title = esc_attr( get_the_title( $id ) );
	                			$image_link  = wp_get_attachment_url( $id );
	                            $image = wp_get_attachment_image_src($id, 'shop_single');

								if ( $slider ):
	                            	echo sprintf( '<a href="%s" itemprop="image" class="woocommerce-additional-image product-image" title="%s"><img class="owl-lazy" data-src="%s" width="%s" height="%s" /></a>', $image_link, $image_title, esc_url( $image[0] ), esc_attr( $image[1] ), esc_attr( $image[2] )  );
								else:
									echo sprintf( '<a href="%s" itemprop="image" class="woocommerce-additional-image product-image" title="%s"><img class="" src="%s" width="%s" height="%s" /></a>', $image_link, $image_title, esc_url( $image[0] ), esc_attr( $image[1] ), esc_attr( $image[2] )  );
								endif;

	                            if($lightbox):
		                            ?>
		                            	<a href="<?php echo $image_link; ?>" class="product-lightbox-btn" <?php echo $data_rel; ?>>lightbox</a>
		                            <?php
	                            endif;
	            				?>
            				</div>
            				<?php

            			}

            		}
                ?>
				<?php if(et_get_external_video($product->get_id())): ?>
					<div>
						<?php echo et_get_external_video($product->get_id()); ?>
					</div>
				<?php endif; ?>


				<?php if(count($video_attachments)>0): ?>
						<div>
							<video controls="controls">
								<?php foreach($video_attachments as $video):  ?>
									<?php $video_ogg = $video_mp4 = $video_webm = false; ?>
									<?php if($video->post_mime_type == 'video/mp4' && !$video_mp4): $video_mp4 = true; ?>
										<source src="<?php echo $video->guid; ?>" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
									<?php endif; ?>
									<?php if($video->post_mime_type == 'video/webm' && !$video_webm): $video_webm = true; ?>
										<source src="<?php echo $video->guid; ?>" type='video/webm; codecs="vp8, vorbis"'>
									<?php endif; ?>
									<?php if($video->post_mime_type == 'video/ogg' && !$video_ogg): $video_ogg = true; ?>
										<source src="<?php echo $video->guid; ?>" type='video/ogg; codecs="theora, vorbis"'>
										<?php esc_html_e('Video is not supporting by your browser', 'woopress'); ?>
										<a href="<?php echo $video->guid; ?>"><?php esc_html_e('Download Video', 'woopress'); ?></a>
									<?php endif; ?>
								<?php endforeach; ?>
							</video>
						</div>
				<?php endif; ?>
            </figure>

            <script type="text/javascript">
	            <?php if ($slider): ?>
	            	jQuery(window).on('load',function(){
	            		jQuery('.main-images').owlCarousel({
					        items:1,
					        nav: true,
					        navText: ["",""],
					        lazyLoad: true,
					        rewindNav: false,
					        autoHeight:true,
					        responsive: {
					        	1600: {
					        		items: 1
					        	}
					        }
					    });
						<?php if ( "horizontal" == $carousel_direction ): ?>
						    jQuery('.main-images').on('changed.owl.carousel', function(e) {
							    var owlMain = jQuery(".main-images").data('owl.carousel');
					            var owlThumbs = jQuery(".product-thumbnails");
					            jQuery('.active-thumbnail').removeClass('active-thumbnail');
					            jQuery(".product-thumbnails").find('.owl-item').eq(e.item.index).find("a").addClass('active-thumbnail');
					           	jQuery(".product-thumbnails").trigger("to.owl.carousel", [e.item.index, 300, true]);
							}).on('load.owl.lazy', function(){
								jQuery(this).addClass('loading');
							}).on('loaded.owl.lazy', function(){
								jQuery(this).removeClass('loading');
							});
						<?php elseif( "vertical" == $carousel_direction ): ?>
							jQuery('.main-images').on('changed.owl.carousel', function(e) {
								jQuery( '.active-thumbnail' ).removeClass( 'active-thumbnail' );
					            jQuery( ".product-thumbnails" ).find( '.slick-slide' ).eq( e.item.index ).addClass( 'active-thumbnail' );
								jQuery( '.product-thumbnails' ).slick( 'slickGoTo', parseInt( e.item.index ) );
							});
						<?php endif; ?>
	            	});
			    <?php endif ?>
				<?php if ($zoom != 'disable') {
					?>
					jQuery(document).ready(function () {
						if(jQuery(window).width() > 768){
							<?php if($slider): ?>
								jQuery('.main-images').on('initialize.owl.carousel initialized.owl.carousel loaded.owl.lazy',function(){
				            		jQuery('.main-images .owl-item.active .product-image').swinxyzoom({mode:'<?php echo $zoom ?>', controls: false, size: '100%', dock: { position: 'right' } }); // dock window slippy lens
				            	});
							<?php else: ?>
								jQuery('.main-images .product-image').swinxyzoom({mode:'<?php echo $zoom ?>', controls: false, size: '100%', dock: { position: 'right' } }); // dock window slippy lens
							<?php endif; ?>
						}
						});
					<?php
				} ?>
				jQuery('.main-images a').click(function(e){
					e.preventDefault();
				});
            </script>

	<?php if ($slider): ?>
		<?php do_action( 'woocommerce_product_thumbnails' ); ?>
	<?php endif ?>

</div>
