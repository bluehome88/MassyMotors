<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
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

global $post, $product, $woocommerce;

$zoom     			= etheme_get_option('zoom_effect');
$lightbox 			= etheme_get_option('gallery_lightbox');
$carousel_direction = etheme_get_option('carousel_direction');
$carousel_items 	= ( intval(etheme_get_option('carousel_items')) <= 0 || intval(etheme_get_option('carousel_items')) > 10  ) ? 3 : intval(etheme_get_option('carousel_items'));

$carousel_classes 	= "product-thumbnails images-popups-gallery";

if ( "horizontal" == $carousel_direction ) {
	$carousel_classes .= ' owl-carousel';
}

$attachment_ids = $product->get_gallery_image_ids();

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

if ((has_post_thumbnail() && ( $has_video || $attachment_ids)) || ( $has_video && $attachment_ids)  ) {
	?>
	<div id="product-pager" class="<?php echo $carousel_classes; ?>"><?php

		$loop = 0;
		$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 3 );

        $data_rel = '';
		$image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
		$image_link  = wp_get_attachment_url( get_post_thumbnail_id() );
		$image       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ), array(
			'title' => $image_title
			) );
        if ( has_post_thumbnail() ) {
        	echo sprintf( '<a href="%s" title="%s" class="active-thumbnail" %s>%s</a>', $image_link, $image_title, $data_rel, $image );
        } else {
	    	echo sprintf( '<a href="%s" class="active-thumbnail" ><img src="%s" /></a>', wc_placeholder_img_src(), wc_placeholder_img_src() );
        }

		foreach ( $attachment_ids as $attachment_id ) {

			$classes = array(); //  'zoom'

			if ( $loop == 0 || $loop % $columns == 0 ) {
				$classes[] = 'first';
			}

			if ( ( $loop + 1 ) % $columns == 0 ){
				$classes[] = 'last';
			}

			$image_link = wp_get_attachment_url( $attachment_id );
			$image_src = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );

			if ( ! $image_link )
				continue;

			if ( "horizontal" == $carousel_direction ) {
				$image = '<img data-src="'.esc_url($image_src[0]).'" class="owl-lazy attachment-shop_thumbnail size-shop_thumbnail wp-post-image" alt="'.get_post_meta( $attachment_id, '_wp_attachment_image_alt', true).'" height="'.esc_attr($image_src[1]).'" width="'.esc_attr($image_src[2]).'" />';
			}else if ( "vertical" == $carousel_direction ) {
				$image = '<img data-lazy="'.esc_url($image_src[0]).'" class="attachment-shop_thumbnail size-shop_thumbnail wp-post-image" alt="'.get_post_meta( $attachment_id, '_wp_attachment_image_alt', true).'" height="'.esc_attr($image_src[1]).'" width="'.esc_attr($image_src[2]).'" />';
			}
			$image_class = esc_attr( implode( ' ', $classes ) );
			$image_title = get_post_field( 'post_title', $attachment_id );

			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a href="%s" class="%s" title="%s" %s>%s</a>', $image_link, $image_class, $image_title, $data_rel, $image ), $attachment_id, $post->ID, $image_class );

			$loop++;
		}

	?>
	<?php if(et_get_external_video($product->get_id())): ?>
		<li class="video-thumbnail">
			<span><?php esc_html_e('Video', 'woopress'); ?></span>
		</li>
	<?php endif; ?>

	<?php if(count($video_attachments)>0): ?>
		<li class="video-thumbnail">
			<span><?php esc_html_e('Video', 'woopress'); ?></span>
		</li>
	<?php endif; ?>
	</div>
        <script type="text/javascript">
			<?php if ( "horizontal" == $carousel_direction ): ?>
			     jQuery('.product-thumbnails').owlCarousel({
			        items : <?php echo ( $carousel_items > $loop+1 ) ? $loop+1 : $carousel_items; ?>,
			        animateIn: 'fadeInRight',
			        animateOut: 'fadeInLeft',
			        nav: true,
			        lazyLoad: true,
			        navText: ["",""],
			        responsive: {
			        	0: {
			        		items: 2
			        	},
			        	479: {
			        		items: 2
			        	},
			        	619: {
			        		items: 3
			        	},
			        	768: {
			        		items: <?php echo ( $carousel_items > $loop+1 ) ? $loop+1 : $carousel_items; ?>
			        	},
			        	1200: {
			        		items: <?php echo ( $carousel_items > $loop+1 ) ? $loop+1 : $carousel_items; ?>
			        	},
			        	1600: {
			        		items: <?php echo ( $carousel_items > $loop+1 ) ? $loop+1 : $carousel_items; ?>
			        	}
			        }
			    });

			    jQuery('.product-thumbnails .owl-item').click(function(e) {
				    jQuery(".main-images").trigger("to.owl.carousel", [jQuery(this).index(), 300, true]);
				    jQuery('.active-thumbnail').removeClass('active-thumbnail');
				    jQuery(this).find('a').addClass('active-thumbnail');
			    });

			<?php elseif( "vertical" == $carousel_direction ): ?>
				jQuery('.product-thumbnails').slick({
					  slidesToShow: <?php echo ( $carousel_items >= $loop+1 ) ? $loop+1 : $carousel_items; ?>,
					  slidesToScroll: 1,
					  autoplay: false,
					  vertical: true,
					  verticalSwiping: true,
					  infinite: false,
					  adaptiveHeight: true,
					  lazyLoad: 'ondemand',
					  responsive: [
					    {
					      breakpoint: 482,
					      settings: {
					        slidesToShow: 2,
					        slidesToScroll: 1,
							vertical: false,
							verticalSwiping: false,
							adaptiveHeight: false,
					      }
					    }
					  ]
				  });

			    jQuery('.product-thumbnails .slick-slide').click(function(e) {
				    jQuery(".main-images").trigger("to.owl.carousel", [jQuery(this).index(), 300, true]);
				    jQuery('.slick-active').removeClass('slick-active slick-current active-thumbnail');
				    jQuery(this).addClass('slick-active slick-current active-thumbnail');
			    });
			<?php endif; ?>

		    jQuery('.product-thumbnails a').click(function(e) {
			    e.preventDefault();
		    });

        </script>

	<?php
}
