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
            'public' => true
        )
    );
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

function admin_display_curbside_order_infos( $arrInfos )
{

  $shopping_list_item_name_col = $arrInfos->shopping_list_item_name;

  $col_0 = array_column($shopping_list_item_name_col, 0);
  $col_1 = array_column($shopping_list_item_name_col, 1);
  $col_2 = array_column($shopping_list_item_name_col, 2);
  $col_3 = array_column($shopping_list_item_name_col, 3);
  $col_4 = array_column($shopping_list_item_name_col, 4);
  $col_5 = array_column($shopping_list_item_name_col, 5);


    $render_html = "<table border=1 cellspacing=0 cellpadding=10 style='min-width:50%'>
        <tr>
      <tr><td colspan=2 align='center'><b>Order Information</b></td></tr>";
    $render_html.= "  <tr>
        <td>Customer Name</td>
        <td>".$arrInfos->your_name."</td>
      </tr>";
    $render_html.= "  <tr>
        <td>Order ID</td>
        <td>#W".$arrInfos->order_id."</td>
      </tr>";
    $render_html.= "  <tr>
        <td>Phone Number</td>
        <td>".$arrInfos->your_phone."</td>
      </tr>";
    $render_html.= "  <tr>
        <td>Email</td>
        <td>".$arrInfos->your_email."</td>
      </tr>";
    $render_html.= "  <tr>
        <td>License Plate</td>
        <td>".$arrInfos->your_license."</td>
      </tr>";
    $render_html.= "  <tr>
        <td>Loyalty Card Number</td>
        <td>".$arrInfos->your_cardnumber."</td>
      </tr>";
    $render_html.= "  <tr>
        <td>Preferred store to collect</td>
        <td>".$arrInfos->your_collect."</td>
      </tr>";

      $render_html .= "<tr><td colspan='2'>Shopping List</td></tr>";

      $s_no = 0;
      

      for ($i=0; $i <= count($arrInfos->shopping_list_item_name) - 1 ; $i++) { 
        $s_no++;
        $render_html.= "<tr><td colspan='2'>". "#$s_no " . $arrInfos->shopping_list_item_name[$i] . " | " . $arrInfos->shopping_list_brand_name[$i] . " | " . $arrInfos->shopping_list_description[$i] . " | " . $arrInfos->shopping_list_size_weight[$i] . " | " . $arrInfos->shopping_list_quantity[$i] ."</td></tr>";
                 
      }

    $render_html.= "</table>";
    echo $render_html;

    // echo "<pre>";
    // print_r($arrInfos);
    // echo "</pre>";
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
          echo "#W".$infos->order_id;
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

?>
