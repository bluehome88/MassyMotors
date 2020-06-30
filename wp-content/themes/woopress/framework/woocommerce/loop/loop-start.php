<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
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

global $woocommerce_loop;

$view_mode = etheme_get_option('view_mode');
if( !empty($woocommerce_loop['view_mode'])) {
        $view_mode = $woocommerce_loop['view_mode'];
} else {
        $woocommerce_loop['view_mode'] = $view_mode;
}

if($view_mode == 'list' || $view_mode == 'list_grid') {
    $view_class = 'products-list';
}else{
    $view_class = 'products-grid';
}

?>
<div class="row products-loop <?php echo $view_class; ?> row-count-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?> columns-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?>">
