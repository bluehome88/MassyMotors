<?php add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
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



 function getOrderPrefix(){

  $submission = WPCF7_Submission::get_instance();
  if ( $submission ) {
  $posted_data = $_POST;
  }
  $select_your_nearest_store = isset( $posted_data['select_your_nearest_store'] ) ? trim( $posted_data['select_your_nearest_store'] ) : '';
  
  if($select_your_nearest_store){
      $order_prefix = "#WB";
      switch ($select_your_nearest_store) {
        case 'Sunset Crest':
          $order_prefix = "#WSS";
          break;

        case 'Worthing':

            $order_prefix = "#WWO";
            break;

        case 'Skymall':

            $order_prefix = "#WSK";
            break;

        case 'Warrens':

            $order_prefix = "#WWA";
            break;
        
        default:
          $order_prefix = "#WB";
          break;
      }
  }
  
  return $order_prefix;
}





/* curbside functions */
function wpcf7_do_something ($WPCF7_ContactForm) {
  global $wpdb;

    $submission = WPCF7_Submission::get_instance();

  if ( $submission ) {
    $posted_data = $submission->get_posted_data();
    $uploaded_files = $submission->uploaded_files();
  }

  // $posted_data = $_POST;
  //print_r($posted_data);

  // curbside form
  if( $WPCF7_ContactForm->id == 20212 ){
    $wpcf7 = WPCF7_ContactForm::get_current();
    $mail = $wpcf7->prop('mail');

    // echo "<pre>";
    // print_r($mail);
    // echo "</pre>";


    // get incremental order ID
  $querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'curbside_order' ORDER BY ID DESC LIMIT 1";
  $pageposts = $wpdb->get_results($querystr);
    if ($pageposts){
      $last_order = $pageposts[0];
      $order_contents = json_decode($last_order->post_content);

      $order_id = $order_contents->order_id + 1;
    }
    else
    { 
      $order_id = 1;
    }

    $numeric_order_id = (int) $order_contents->order_id; 
    $numeric_order_id = $numeric_order_id + 1;
 
    $order_id = sprintf("%08d", ($numeric_order_id));
    
     session_start();
    // set order ID to thank you page

    $default_order_prefix = getOrderPrefix() !== NULL ? getOrderPrefix() : "#WB";

    $_SESSION['order_id'] = $default_order_prefix . $order_id;

    $posted_data['order_id'] = $order_id;
    $posted_data['order_id_prefix'] = getOrderPrefix();
    $posted_data['store_location_opt'] = $_POST['select_your_nearest_store'];



    $select_your_nearest_store = isset( $posted_data['select_your_nearest_store'] ) ? trim( $posted_data['select_your_nearest_store'] ) : '';

    if( $select_your_nearest_store){

      $mail['recipient'] =  $select_your_nearest_store;//echo "order_prefix = " . $order_prefix;  
      $mail['additional_headers'] = $mail['additional_headers']. "\nCc: digitalteam@simplyintense.com\nBcc: faceb.sandeep@gmail.com";

      // echo "<pre>";
      // print_r($mail);  
      // echo "</pre>";
    }    

    $mail['body'] = str_replace( '[current]', date('F d, Y H:i:s'), $mail['body'] );
    $mail['body'] = str_replace( '#WB[order_id]', getOrderPrefix() . $order_id, $mail['body'] );
    $mail['body'] = str_replace( '[your_collect]', $_POST['select_your_nearest_store'] , $mail['body'] );
    

    $mail['subject'] = "New Order ". getOrderPrefix() .$order_id." submitted by ".$posted_data['your_name'];

    // mail : to admin
    // mail_2 : to customer
    $mail_2 = $wpcf7->prop('mail_2');
    $mail_2['body'] = str_replace( '#WB[order_id]', getOrderPrefix() . $order_id, $mail_2['body'] );
    $mail_2['body'] = str_replace( '[your_collect]', $_POST['select_your_nearest_store'] , $mail_2['body'] );
    $mail_2['subject'] = "Confirmation of Curbside order ".getOrderPrefix().$order_id;

    $newpostid = insertOrderInfos( $posted_data );

    $wpcf7->set_properties(array("mail" => $mail, "mail_2" => $mail_2 ));

    return $wpcf7;
  }  

  return false;
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


add_filter( 'wpcf7_validate_textarea', 'address_validation_filter', 20, 2 );

function address_validation_filter( $result, $tag ) {
  if ( 'address' == $tag->name ) {

    $select_opt = isset( $_POST['select_option_div_pic'] ) ? trim( $_POST['select_option_div_pic'] ) : '';
    $address = isset( $_POST['address'] ) ? trim( $_POST['address'] ) : '';

      if ( $select_opt == "Delivery" && $address == '') {
        $result->invalidate( $tag, "The Address field is required.");
      }
  }

  return $result ;
}


add_filter( 'wpcf7_validate_select', 'select_opt_validation_filter', 20, 2 );

function select_opt_validation_filter( $result, $tag ) {
  if ( 'city' == $tag->name ) {

    $select_opt = isset( $_POST['select_option_div_pic'] ) ? trim( $_POST['select_option_div_pic'] ) : '';
    $city = isset( $_POST['city'] ) ? trim( $_POST['city'] ) : '';

      if ( $select_opt == "Delivery" && $city == '') {
        $result->invalidate( $tag, "The Parish/Area field is required.");
      }
  }
  /*if ( 'your_collect' == $tag->name ) {

    $select_opt = isset( $_POST['select_option_div_pic'] ) ? trim( $_POST['select_option_div_pic'] ) : '';
    $your_collect = isset( $_POST['your_collect'] ) ? trim( $_POST['your_collect'] ) : '';

      if ( $select_opt == "Pickup"  && $your_collect == '') {
        $result->invalidate( $tag, "The Preferred store field is required.");
      }
  }

  if ( 'select_your_nearest_store' == $tag->name ) {

    $select_opt = isset( $_POST['select_option_div_pic'] ) ? trim( $_POST['select_option_div_pic'] ) : '';
    $select_your_nearest_store = isset( $_POST['select_your_nearest_store'] ) ? trim( $_POST['select_your_nearest_store'] ) : '';

      if ( $select_opt == "Delivery"  && $select_your_nearest_store == '') {
        $result->invalidate( $tag, "The nearest store store field is required.");
      }
  }*/


  
  return $result ;
}

add_filter( 'wpcf7_validate_text', 'your_license_validation_filter', 20, 2 );
function your_license_validation_filter( $result, $tag ) {
  if ( 'your_license' == $tag->name ) {

    $select_opt = isset( $_POST['select_option_div_pic'] ) ? trim( $_POST['select_option_div_pic'] ) : '';
    $your_license = isset( $_POST['your_license'] ) ? trim( $_POST['your_license'] ) : '';

      if ( $select_opt == "Pickup" && $your_license == '') {
        $result->invalidate( $tag, "The License Plate field is required.");
      }
  }

  return $result ;
}




add_filter( 'the_content', 'replace_thankyou_order' );
function replace_thankyou_order( $content ) {

  session_start();
  $order_id = isset( $_SESSION['order_id'] ) ? $_SESSION['order_id'] : 0;


    if ( $order_id ) {
        $content = str_replace( '#WB00000000', $order_id, $content );
        // $_SESSION['order_id'] = 0;
    }

    return $content;
}

add_action( 'wp_footer', 'mycustom_wp_footer' );

function mycustom_wp_footer() {
?>
  <script>
  document.addEventListener( 'wpcf7mailsent', function( event ) {
    if (event.detail.contactFormId == '20212' ) {
      window.location.href = '/thanks-curbside/'
    }
  }, false );
  </script>

  <script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#wpcf7-f20212-p20213-o1 input[type="radio"]').click(function(){
            var inputValue = jQuery(this).attr("value");
            
            if(inputValue == "Delivery"){
              jQuery("#cf7_address_city_wrap").show();
              jQuery("#select_your_nearest_store_lable").html("Select your nearest store?*:");
              jQuery("#your_license_wrapper").hide();             
            }
            else{
              jQuery("#cf7_address_city_wrap").hide();
              jQuery("#select_your_nearest_store_lable").html("Preferred store to collect*:");
              jQuery("#your_license_wrapper").show(); 
            }        
        });

        jQuery("#wpcf7-f20212-p20213-o1 .div_pic_opt span.wpcf7-list-item.last").click(function(e){

          jQuery("#wpcf7-f20212-p20213-o1 .div_pic_opt span.wpcf7-list-item.first").removeAttr("style");

          
            jQuery("#wpcf7-f20212-p20213-o1 .div_pic_opt span.wpcf7-list-item.last").css({"background" : "url('http://beta-massybb.simplyintense.com/wp-content/themes/woopress-child/img/original/curbside_on.png')" , "width": "150px" , "height": "150px" , "background-repeat": "no-repeat" , "cursor": "pointer" , "background-size":"contain"});
            jQuery("input[value='Pickup']").prop("checked", true).trigger("click");
          

          
          
          
        });
        jQuery("#wpcf7-f20212-p20213-o1 .div_pic_opt span.wpcf7-list-item.first").click(function(){

            jQuery("#wpcf7-f20212-p20213-o1 .div_pic_opt span.wpcf7-list-item.last").removeAttr("style");
          
            jQuery("#wpcf7-f20212-p20213-o1 .div_pic_opt span.wpcf7-list-item.first").css({"background" : "url('http://beta-massybb.simplyintense.com/wp-content/themes/woopress-child/img/original/delivery_on.png')" , "width": "150px" , "height": "150px" , "background-repeat": "no-repeat" , "cursor": "pointer" , "background-size":"contain"});     
            jQuery("input[value='Delivery']").prop("checked", true).trigger("click"); 


          
        });





    });
  </script>
<?php
}

require_once __DIR__ . '/curbside.php';
require_once __DIR__ . '/export_curbside.php';