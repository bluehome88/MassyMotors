<?php
/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
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
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$class = '';
$html = '<a href="%s" data-quantity="%s" class="btn border-grey %s product_type_%s" %s><i class="ico-smallpacket"></i> %s</a>';

if(etheme_get_option('ajax_addtocart') && $product->is_purchasable() && $product->is_in_stock() > 0 && $product->get_type() == 'simple') {
	$class .= 'etheme_add_to_cart_button ajax_add_to_cart ';
	$html = '<a href="%s" data-quantity="%s" class="add_to_cart_button btn border-grey progress-button %s product_type_%s" %s data-style="shrink" data-horizontal><i class="ico-smallpacket"></i> %s</a>';
}

echo apply_filters( 'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
	sprintf( $html,
		esc_url( $product->add_to_cart_url() ),
		esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
		esc_attr( isset( $args['class'] ) ? $args['class'] : $class ),
		esc_attr( $product->get_type() ),
		isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
		esc_html( $product->add_to_cart_text() )
	),
$product, $args );
