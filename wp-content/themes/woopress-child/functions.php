<?php 

if ( ! session_id() ) {
    session_start();
}
	
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( 'bootstrap', 'parent-style' ) );
}

/* Team */

function my_post_type_team() {
	register_post_type( 'team',
                array( 
				'label' => __('Team'), 
				'singular_label' => __('Team Item', 'hilofoodstores'),
				'_builtin' => false,
				'public' => true, 
				'show_ui' => true,
				'show_in_nav_menus' => true,
				'hierarchical' => true,
				'capability_type' => 'page',
				'menu_icon' => 'dashicons-groups',
				'rewrite' => array(
					'slug' => 'team-view',
					'with_front' => FALSE,
				),
				'supports' => array(
						'title',
						'editor',
						'thumbnail',
						'excerpt',
						'custom-fields',
						'comments')
					) 
				);
	register_taxonomy('team_category', 'team', array('hierarchical' => true, 'label' => 'Team Categories', 'singular_name' => 'Category', "rewrite" => true, "query_var" => true));
}

add_action('init', 'my_post_type_team');


/* Vacancies */
function my_post_type_vacancy() {
	register_post_type( 'vacancy',
                array( 
				'label' => __('Vacancy'), 
				'singular_label' => __('Vacancy Item', 'hilofoodstores'),
				'_builtin' => false,
				'public' => true, 
				'show_ui' => true,
				'show_in_nav_menus' => true,
				'hierarchical' => true,
				'capability_type' => 'page',
				'menu_icon' => 'dashicons-businessman',
				'rewrite' => array(
					'slug' => 'vacancies',
					'with_front' => FALSE,
				),
				'supports' => array(
						'title',
						'editor',
						'thumbnail',
						'excerpt',
						'custom-fields',
						'comments')
					) 
				);
	register_taxonomy('vacancy_category', 'vacancy', array('hierarchical' => true, 'label' => 'Job Categories', 'singular_name' => 'Category', "rewrite" => true, "query_var" => true));
}

add_action('init', 'my_post_type_vacancy');



/* Store Locator */
function storelocator_func(){
	query_posts('cat=30&posts_per_page=30&orderby=name&order=ASC');
	$bad_char = array("'", " ", ".");
	while (have_posts()) : the_post();
		echo "<div style=\"padding-left:30px; display:none;\" class=\"stores\" id=\"" . str_replace($bad_char, "", urldecode(get_the_title())) . "\">";
			the_title('<h2><strong>', '</strong></h2>');
			the_content();
		echo "</div>";
	endwhile;
wp_reset_query(); 

echo '<script type="text/javascript">
 jQuery(document).ready(function($) {
	//jQuery(".stores").hide();
	jQuery("#select_store").on("change", function(){
		$(".stores").hide();
		var store_id = $(this).val().replace("\'", "");
		store_id = store_id.replace(" ", "");
		store_id = store_id.replace(".", "");
		jQuery("#"+store_id).show();
	});
});

</script>';

}

add_shortcode( 'storelocator', 'storelocator_func' );


// Hook in
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

function custom_override_checkout_fields( $fields ) {
     unset($fields['billing']['billing_state']);
     unset($fields['billing']['billing_postcode']);
     unset($fields['shipping']['shipping_state']);
     unset($fields['shipping']['shipping_postcode']);


     $fields['billing']['billing_massycard'] = array(
        'label'     => __('Massy Card Number', 'woocommerce'),
        'placeholder'   => _x('', 'placeholder', 'woocommerce'),
        'required'  => false,
        'class'     => array('form-row-wide'),
        'clear'     => true
      );

     return $fields;

}

/**
 * Add Handling Fee
 */
add_action( 'woocommerce_cart_calculate_fees','massy_handling_fee' );

function massy_handling_fee() {
     global $woocommerce;
 
     if ( is_admin() && ! defined( 'DOING_AJAX' ) )
          return;
     	$percentage = 0.0198;
		$fee = 6.00;
		$surcharge = ($woocommerce->cart->cart_contents_total * $percentage)+$fee;
     	$woocommerce->cart->add_fee( 'Handling', $surcharge, true, 'standard' );
}


/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'massy_custom_checkout_field_display_admin_order_meta', 10, 1 );

function massy_custom_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Massy Card').':</strong> ' . get_post_meta( $order->id, '_billing_massycard', true ) . '</p>';
}


/**
 * Add Massy Card Number to order emails
 **/
add_filter('woocommerce_email_order_meta_keys', 'massy_woocommerce_email_order_meta_keys');

function massy_woocommerce_email_order_meta_keys( $keys ) {
	$keys['Massy Card'] = '_billing_massycard';
	return $keys;
}


/**
 *  Edit Admin Email Subject to Show Customer Name
 **/
add_filter('woocommerce_email_subject_new_order', 'change_admin_email_subject', 1, 2);

function change_admin_email_subject( $subject, $order ) {
	global $woocommerce;
	$subject = sprintf( 'Order (#%s) - %s %s', $order->id, $order->billing_first_name, $order->billing_last_name);
	return $subject;
}


/**
 *  Hide Empty Categories
 **/
function massy_hide_product_categories_widget( $list_args ){
            $list_args[ 'hide_empty' ] = 1;
            return $list_args;
}
add_filter( 'woocommerce_product_categories_widget_args', 'massy_hide_product_categories_widget' );

/**
 *  Change login logo
 **/
function my_login_logo() { ?>
    <style type="text/css">
        .login h1 a {
            background-image: url(http://massystorestt.com.php53-1.ord1-1.websitetestlink.com/wp-content/uploads/2015/11/massy-stores-logo-2.png);
            padding-bottom: 0px;
	        height: 70px;
    		width: 260px;
		background-size: 254px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

/**
 *  Login using email
 **/
function login_with_email_address($username) {
	$user = get_user_by_email($username);
	if(!empty($user->user_login))
		$username = $user->user_login;
	return $username;
}
add_action('wp_authenticate','login_with_email_address');

add_filter('deprecated_constructor_trigger_error', '__return_false');

function woocommerce_disable_shop_page() {
    global $post;
    if (is_shop()):
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    endif;
}
add_action( 'wp', 'woocommerce_disable_shop_page' );


/* curbside functions */
function wpcf7_do_something ($WPCF7_ContactForm) {
  global $wpdb;

    $submission = WPCF7_Submission::get_instance();

  if ( $submission ) {
    $posted_data = $submission->get_posted_data();
    $uploaded_files = $submission->uploaded_files();
  }

  // curbside form
  if( $WPCF7_ContactForm->id == 20854 ){
    $wpcf7 = WPCF7_ContactForm::get_current();
    $mail = $wpcf7->prop('mail');

    // get incremental order ID
	$querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'curbside_order' ORDER BY ID DESC LIMIT 1";
	$pageposts = $wpdb->get_results($querystr);
    if ($pageposts){
    	$last_order = $pageposts[0];
    	$order_contents = json_decode($last_order->post_content);

    	$order_id = $order_contents->order_id + 1;
    }
    else
    	$order_id = 1;

   	$order_id = sprintf("%08d", ($order_contents->order_id + 1));
    // set order ID to thank you page
    $_SESSION['order_id'] = $order_id;

    $posted_data['order_id'] = $order_id;

    $mail['body'] = str_replace( '[current]', date('F d, Y H:i:s'), $mail['body'] );
    $mail['body'] = str_replace( '[order_id]', $order_id, $mail['body'] );

    $mail['subject'] = "New Order #W".$order_id." submitted by Si Media";

    // mail : to admin
    // mail_2 : to customer
    $mail_2 = $wpcf7->prop('mail_2');
    $mail_2['body'] = str_replace( '[order_id]', $order_id, $mail_2['body'] );
    $mail_2['subject'] = "Confirmation of Curbside order #W".$order_id;

    $newpostid = insertOrderInfos( $posted_data );

    $wpcf7->set_properties(array("mail" => $mail, "mail_2" => $mail_2 ));
  }

  return $wpcf7;
}
add_action("wpcf7_before_send_mail", "wpcf7_do_something");

// custom validation 
add_filter( 'wpcf7_validate_text*', 'number_validation_filter', 20, 2 );
function number_validation_filter( $result, $tag ) {
  if ( 'your_cardnumber' == $tag->name ) {
    $your_cardnumber = isset( $_POST['your_cardnumber'] ) ? trim( $_POST['your_cardnumber'] ) : '';
  
    if ( floor(log10($your_cardnumber)+1 ) < 9 ) {
  		$result->invalidate( $tag, "Loyalty card number must be 9 or 11 digits");
    }
    if ( strlen((string)$your_cardnumber) > 11 ) {
  		$result->invalidate( $tag, "Loyalty card number must be 9 or 11 digits");
    }
    if ( !is_numeric($your_cardnumber)) {
  		$result->invalidate( $tag, "Loyalty card number must be 9 or 11 digits");
    }
  }
  if ( 'your_phone' == $tag->name ) {
/*
  	$your_phone = isset( $_POST['your_phone'] ) ? trim( $_POST['your_phone'] ) : '';
  	if ( floor(log10($your_phone)+1 ) != 7 || !is_numeric($your_phone) ) {
  		$result->invalidate( $tag, "Please input correct number.");
    }
*/
  }

  return $result;
}


add_filter( 'the_content', 'replace_thankyou_order' );
function replace_thankyou_order( $content ) {
	$order_id = isset( $_SESSION['order_id'] ) ? $_SESSION['order_id'] : 0;

    if ( $order_id ) {
        $content = str_replace( '00000000', $order_id, $content );
        // $_SESSION['order_id'] = 0;
    }

    return $content;
}

add_action( 'wp_footer', 'mycustom_wp_footer' );

function mycustom_wp_footer() {
?>
	<script>
	document.addEventListener( 'wpcf7mailsent', function( event ) {
	  if (event.detail.contactFormId == '20854' ) {
	    window.location.href = '/thanks-curbside/'
	  }
	}, false );
	</script>
<?php
}

require_once __DIR__ . '/curbside.php';
require_once __DIR__ . '/export_curbside.php';