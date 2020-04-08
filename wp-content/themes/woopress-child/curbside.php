<?php

// register custom post
add_action( 'init', 'curbside_order' );
function curbside_order() {
    register_post_type( 'curbside_order',
        array(
            'labels' => array(
                'name' => __( 'Curbside Order' ),
                'singular_name' => __( 'Curbside Order' )
            ),
            'exclude_from_search'   => true,
            'public' => true,
            'supports' => array( 'title')                                                                                                                
        )
    );
}


add_action( 'add_meta_boxes', 'my_remove_publish_metabox' );
function my_remove_publish_metabox() {
    remove_meta_box( 'submitdiv', 'curbside_order', 'side' );
}

function insertOrderInfos($info) {
    if ( ! $info)
      return false;
    $info['your_shopping'] = str_replace("\r\n", "<br/>", $info['your_shopping']);
    $orderInfo = array(
      'post_title'    => ucfirst($info['your_name']),
      'post_content'  => json_encode($info, JSON_HEX_APOS), //json_encode( $info ),
      'post_type'     => 'curbside_order',
      'post_status'   => 'publish'
    );

    $order_id = wp_insert_post( $orderInfo );

    return $order_id;
}








add_action( 'edit_form_after_title', 'admin_display_curbside_order_infos' );

function admin_display_curbside_order_infos( $post )
{

  if($post->post_type == "curbside_order"){

    $arrInfos = json_decode($post->post_content) ;
print_r( $arrInfos );
    $render_html = "<table border=1 cellspacing=0 cellpadding=10 style='min-width:50%'>
        <tr>
      <tr><td colspan=2 align='center'><b>Order Information</b></td></tr>";
    $render_html.= "  <tr>
        <td>Customer Name</td>
        <td>".$arrInfos->your_name."</td>
      </tr>";
    $render_html.= "  <tr>
        <td>Order ID</td>
        <td>" . $infos->order_id_prefix .$arrInfos->order_id."</td>
      </tr>";
    $render_html.= "  <tr>
        <td>Phone Number</td>
        <td>".$arrInfos->your_phone."</td>
      </tr>";
    $render_html.= "  <tr>
        <td>Email</td>
        <td>".$arrInfos->your_email."</td>
      </tr>";
    
    if($arrInfos->select_option_div_pic[0] == "Pickup"){
    $render_html.= "  <tr>
        <td>License Plate</td>
        <td>".$arrInfos->your_license."</td>
      </tr>";
  }


    $render_html.= "  <tr>
        <td>Loyalty Card Number</td>
        <td>".$arrInfos->your_cardnumber."</td>
      </tr>";
    $render_html.= "  <tr>
        <td>Preferred store to collect</td>
        <td>".$arrInfos->store_location_opt."</td>
      </tr>";
    $render_html.= "  <tr>
        <td>Shopping List</td>
        <td>".$arrInfos->your_shopping."</td>
      </tr>";

    $render_html.= "  <tr>
          <td>Selected Option</td>
          <td>".$arrInfos->select_option_div_pic[0]."</td>
        </tr>";

        if($arrInfos->select_option_div_pic[0] == "Delivery"){
            $render_html.= "  <tr>
                    <td>Address</td>
                    <td>".$arrInfos->address."</td>
                  </tr>";

              $render_html.= "  <tr>
                      <td>City</td>
                      <td>".$arrInfos->city."</td>
                    </tr>";
        }

   

    $render_html.= "</table>";
    echo  $render_html;

    echo '<style type="text/css">
  span#footer-thankyou {
      display: none;
  }
</style>';
    // echo "<pre>";
    // print_r($post);
    // echo "</pre>";
  }

    
}

add_filter("manage_edit-curbside_order_columns", "curbside_order_edit_columns");
function curbside_order_edit_columns($columns) {
    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => "Person",
        "order_id" => "OrderID",
        "card_number" => "Card Number",
        "contact_email" => "Email",
        "phone_number" => "Phone",
        "date" => "Date"
    );

    return $columns;
}

add_action("manage_posts_custom_column",  "project_custom_columns");
function project_custom_columns($column) {
    global $post;
    $infos = json_decode($post->post_content);

    switch ($column) {
        /* Client Policy Columns */
        case "order_id":
          echo $infos->order_id_prefix . $infos->order_id;
          break;
        case "card_number":
          echo $infos->your_cardnumber;
          break;
        case "contact_email":
          echo $infos->your_email;
          break;
        case "phone_number":
          echo $infos->your_phone;
          break;
    }
}

add_filter( 'post_row_actions', 'remove_row_actions', 10, 1 );
function remove_row_actions( $actions )
{
    // $actions['edit'] = "Detail";
    $actions['edit'] = str_replace("Edit", "Detail", $actions['edit']);
    if( get_post_type() === 'curbside_order' ){
        unset( $actions['view'] );
        unset( $actions['inline hide-if-no-js'] );
    }
    return $actions;
}

function disable_new_posts() {
    // Hide sidebar link
    global $submenu;
    unset($submenu['edit.php?post_type=curbside_order'][10]);
}

add_action('admin_menu', 'disable_new_posts');
?>
