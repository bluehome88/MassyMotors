<?php
/**
 * Displayed when no products are found matching the current query.
 *
 * Override this template by copying it to yourtheme/woocommerce/loop/no-products-found.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div class="empty-category-block">
	<h2><?php esc_html_e( 'No products were found', 'woopress' ); ?></h2>
	<p><a class="btn medium" href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>"><span><?php esc_html_e( 'Return To Shop', 'woopress' ) ?></span></a></p>
</div>
