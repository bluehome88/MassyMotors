<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

if ( etheme_get_option( 'related_posts_per_page' ) && intval(etheme_get_option( 'related_posts_per_page' )) > 0 ) {
	$posts_per_page = etheme_get_option( 'related_posts_per_page' );
}else {
	$posts_per_page = 15;
}

$related = wc_get_related_products( $product->get_id(), $posts_per_page );

if ( sizeof( $related ) == 0 ) return;

$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'            => 'product',
	'ignore_sticky_posts'  => 1,
	'no_found_rows'        => 1,
	'posts_per_page'       => $posts_per_page,
	'orderby'              => $orderby,
	'post__in'             => $related,
	'post__not_in'         => array( $product->get_id() )
) );

$slider_args = array(
	'title' => esc_html__( 'Related Products', 'woopress' )
);

etheme_create_slider( $args, $slider_args );

wp_reset_postdata();
