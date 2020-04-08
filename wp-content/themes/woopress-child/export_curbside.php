<?php

setlocale(LC_MONETARY, 'en_US');

if ( ! function_exists('curbside_order_admin_page') ) :

add_action( 'admin_menu' , 'curbside_order_admin_page' );

/**
 * Generate sub menu page for settings
 *
 * @uses curbside_order_export_display()
 */
function curbside_order_admin_page()
{
    add_submenu_page(
        'edit.php?post_type=curbside_order',
        __('Export', 'curbside_order'),
        __('Export', 'curbside_order'),
        'edit_posts',
        'export_curbside_info',
        'curbside_order_export_display');
}
endif;

if ( ! function_exists('curbside_order_export_display') ) :
/**
 * Display the form on the Rush Hour Projects Settings sub menu page.
 *
 * Used by 'curbside_order_admin_page'.
 */
function curbside_order_export_display()
{
    // Create a header in the default WordPress 'wrap' container
    echo '<div class="wrap">';

    settings_errors();

    echo '<form method="post" action="">';

    settings_fields( 'edit.php?post_type=curbside_order&page=export_curbside_info' );

    do_settings_sections( 'edit.php?post_type=curbside_order&page=export_curbside_info' );

    submit_button('Export');

    echo '</form></div><!-- .wrap -->';
}
endif;

add_action( 'admin_init', 'curbside_order_settings' );

/**
 * Register settings and add settings sections and fields to the admin page.
 */
function curbside_order_settings()
{

    add_settings_section(
        'export_curbside_info_header', // Section $id
        __('Export Curbside Orders', 'curbside_info'),
        'curbside_info_export_section_title', // Callback
        'edit.php?post_type=curbside_order&page=export_curbside_info' // Settings Page Slug
        );

    add_settings_field(
        'start_date',          // Field $id
        __('Start Date', 'curbside_info'),          // Setting $title
        'export_curbside_info_start_date_callback',
        'edit.php?post_type=curbside_order&page=export_curbside_info',   // Settings Page Slug
        'export_curbside_info_header',          // Section $id
        array('Text to display in the archive header.')
        );   

   	add_settings_field(
        'end_date',          // Field $id
        __('End Date', 'curbside_info'),          // Setting $title
        'export_curbside_info_end_date_callback',
        'edit.php?post_type=curbside_order&page=export_curbside_info',   // Settings Page Slug
        'export_curbside_info_header',          // Section $id
        array('Text to display in the archive header.')
        );

    register_setting(
        'edit.php?post_type=curbside_order&page=export_curbside_info', // $option_group
        'curbside_order_archive'  // $option_name
        );

    // export action
    if( isset($_POST['export_curbside_info']) && isset( $_POST['submit'])){
		$file_name = 'curbside_info-export-' . date('Y-m-d_H-i-s') . '.csv';
		
		header( "Content-Description: File Transfer" );
		header( "Content-Disposition: attachment; filename={$file_name}" );
		header( "Content-Type: application/json; charset=utf-8" );
		
		$table_headers = array(	"No", 
								"Full Name",
								"Order Number",
								"Phone",
								"Email",
								"License Plate",
								"Loyalty Card Number",
                                "Collection",
                                "Shopping List",
								"Time"
						);
		foreach( $table_headers as $d )
			echo $d.",";

		echo "\n";

		$start_date = $_POST['export_curbside_info']['start_date'] ? $_POST['export_curbside_info']['start_date'] : "2010-01-01";
	    $end_date = $_POST['export_curbside_info']['end_date'] ? $_POST['export_curbside_info']['end_date'] : "2100-12-31";

	    $args = array(
	          	'date_query' => array(
			        array(
			            'after'     => $start_date,
			            'before'    => $end_date,
			            'inclusive' => true,
			        ),
			    ),
			'orderby' => 'date',
			'order' => 'ASC',
			'posts_per_page' => 100000,
	        'post_type'      => 'curbside_order',
	        'post_status'    => array('publish'),
	      );
	    $query      =  new WP_Query($args);
    	$curbside_infos = $query->get_posts();

    	foreach( $curbside_infos as $curbside_info)
    	{
    		$arrInfos = json_decode( $curbside_info->post_content);
    		$recordInfo = array(
    			"id"=>"", 
				"your_name"=>"",
				"order_id"=>"",
				"your_phone"=>"",
				"your_email"=>"",
				"your_license"=>"",
				"your_cardnumber"=>"",
                "your_collect"=>"",
                "your_shopping"=>"",
                "order_date"=>""
    		);
    		$recordInfo['id'] = $curbside_info->ID;
    		$recordInfo['your_name']      = ucfirst($arrInfos->your_name);
    		$recordInfo['order_id']       = "#W".$arrInfos->order_id;
            $recordInfo['your_phone']     = $arrInfos->your_phone;
    		$recordInfo['your_email']     = $arrInfos->your_email;
    		$recordInfo['your_license']   = $arrInfos->your_license;
    		$recordInfo['your_cardnumber']  = $arrInfos->your_cardnumber;
    		$recordInfo['your_collect']     = $arrInfos->your_collect;
            $recordInfo['your_shopping']    = $arrInfos->your_shopping;
            $recordInfo['order_date']       = $curbside_info->post_date;

    		foreach( $recordInfo as $key => $d ){
    			$d = str_replace(',', '.', $d);
    			if( $key == "order_date" )
    				echo $d."\n";
    			else
    				echo ucfirst($d).",";
    		}
    	}
		die;
    }
}

/**
 * Callback for settings section.
 *
 * Commented out until settings are working.
 * 
 * @param  array $args Gets the $id, $title and $callback.
 */
function curbside_info_export_section_title( $args ) {
    // printf( '<h2>%s</h2>', apply_filters( 'the_title', $args['title'] ) );
}

/**
 * Settings fields callbacks.
 */
function export_curbside_info_start_date_callback($args)
{
    $options = get_option('export_curbside_info');

    echo '<input class="" id="start_date" name="export_curbside_info[start_date]" type="date" placeholder="2019-01-20"/>';
}

function export_curbside_info_end_date_callback($args)
{
    $options = get_option('export_curbside_info');

    echo '<input class="" id="end_date" name="export_curbside_info[end_date]" type="date" placeholder="2019-01-25"/>';
}
