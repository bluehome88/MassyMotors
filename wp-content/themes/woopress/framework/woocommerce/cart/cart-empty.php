<?php
/**
 * Empty cart page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

wc_print_notices();

/**
 * @hooked wc_empty_cart_message - 10
*/

?>

<?php do_action('woocommerce_cart_is_empty'); ?>

<div class="cart-empty empty-cart-block">
	<i class="icon-shopping-cart"></i>
	
	<?php etheme_option('empty_cart_content'); ?>	

	<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
		<p><a class="btn active big filled" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"><span><?php esc_html_e('Return To Shop', 'woopress') ?></span></a></p>

		<?php endif; ?>

	
</div>