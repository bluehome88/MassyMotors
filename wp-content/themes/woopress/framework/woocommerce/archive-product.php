<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
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

	get_header( 'shop' );

	$l = et_page_config();

	$full_width = etheme_get_option('shop_full_width');

	if($full_width) {
		$l['content-class'] = 'col-md-12';
		$l['sidebar'] = 'without';
	}


	/**
	 * Hook: woocommerce_before_main_content.
	 *
	 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
	 * @hooked woocommerce_breadcrumb - 20
	 * @hooked WC_Structured_Data::generate_website_data() - 30
	 */
	do_action( 'woocommerce_before_main_content' );
?>

<?php $cat_desc = etheme_get_option('product_banner_position') ? etheme_get_option('product_banner_position') : ''; ?>
<div class="<?php echo (!$full_width) ? 'container' : 'shop-full-width'; ?>">
	<div class="page-content sidebar-position-<?php echo esc_attr( $l['sidebar'] ); ?> <?php if (etheme_get_option('grid_sidebar') != 'without'): ?> sidebar-mobile-<?php esc_attr_e( $l['shop-sidebar-mobile'] ); ?> <?php endif; ?>">
		<div class="row">

			<div class="content main-products-loop <?php esc_attr_e( $l['content-class'] ); ?>">
                <div <?php echo ($full_width) ? 'class="container"' : ''; ?>>
					<?php if ( $cat_desc == 'top' || $cat_desc == '' ): ?>
						<?php etheme_category_header();?>
					<?php endif; ?>
					<?php
						/**
						 * Hook: woocommerce_archive_description.
						 *
						 * @hooked woocommerce_taxonomy_archive_description - 10
						 * @hooked woocommerce_product_archive_description - 10
						 */
						do_action( 'woocommerce_archive_description' );
					?>
                </div>

				<div class="shop-filters-area">
					<?php if(!function_exists('dynamic_sidebar') || !dynamic_sidebar('shop-widgets-area')): ?>
					<?php endif; ?>
				</div>


				<?php if ( have_posts() ) : ?>

					<?php if (woocommerce_products_will_display()): ?>
	                    <div class="filter-wrap">
	                    	<div class="filter-content">
		                    	<?php
		                    		/**
		                    		 * woocommerce_before_shop_loop hook
		                    		 *
		                    		 * @hooked woocommerce_result_count - 20
		                    		 * @hooked woocommerce_catalog_ordering - 30
		                             * @hooked et_grid_list_switcher - 35
		                    		 */
		                    		do_action( 'woocommerce_before_shop_loop' );
		                    	?>
	                    	</div>
	                    </div>
					<?php endif ?>

					<?php

					woocommerce_product_loop_start();

					if ( wc_get_loop_prop( 'total' ) ) {
						while ( have_posts() ) {
							the_post();

							/**
							 * Hook: woocommerce_shop_loop.
							 *
							 * @hooked WC_Structured_Data::generate_product_data() - 10
							 */
							do_action( 'woocommerce_shop_loop' );

							wc_get_template_part( 'content', 'product' );
						}
					}

					woocommerce_product_loop_end();

					?>

					<?php
						/**
						 * woocommerce_after_shop_loop hook
						 *
						 * @hooked woocommerce_pagination - 10
						 */
						do_action( 'woocommerce_after_shop_loop' );
					?>

				<?php else: ?>

					<?php
						/**
						 * Hook: woocommerce_no_products_found.
						 *
						 * @hooked wc_no_products_found - 10
						 */
						do_action( 'woocommerce_no_products_found' );
					 ?>

				<?php endif; ?>

			<?php
				/**
				 * woocommerce_after_main_content hook
				 *
				 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
				 */
				do_action( 'woocommerce_after_main_content' );
			?>
			<?php if ( $cat_desc == 'bottom' ) {
					etheme_category_header();
				}; ?>
			</div>

			<?php if(!$full_width) : ?>
				<?php if ( etheme_get_option('shop_sidebar_responsive_display') && etheme_get_option('grid_sidebar') != 'without' && wc_get_loop_prop( 'columns', wc_get_default_products_per_row() ) < 6) : ?>
				<button type="button" class="btn filled medium" id="show-shop-sidebar"><?php esc_html_e('Filters', 'woopress'); ?> </button> <div class="hidden-shop-sidebar already-hidden"> <?php endif; ?>
				<?php do_action( 'woocommerce_sidebar' ); ?>
					<?php if ( etheme_get_option('shop_sidebar_responsive_display') && etheme_get_option('grid_sidebar') != 'without' && wc_get_loop_prop( 'columns', wc_get_default_products_per_row() ) < 6) echo '</div>'; ?>
				<?php endif; ?>

		</div>

	</div>
</div>




<?php get_footer( 'shop' ); ?>
