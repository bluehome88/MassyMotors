<?php
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Define our settings
$page_takeover_background = 'page_takeover_background';
$page_takeover_title = 'page_takeover_title';
$page_takeover_title_font = 'page_takeover_title_font';
$page_takeover_title_size = 'page_takeover_title_size';
$page_takeover_title_color = 'page_takeover_title_color';
$page_takeover_subtitle = 'page_takeover_subtitle';
$page_takeover_subtitle_font = 'page_takeover_subtitle_font';
$page_takeover_subtitle_size = 'page_takeover_subtitle_size';
$page_takeover_subtitle_color = 'page_takeover_subtitle_color';
$page_takeover_button = 'page_takeover_button';
$page_takeover_button_background = 'page_takeover_button_background';
$page_takeover_button_font = 'page_takeover_button_font';
$page_takeover_button_size = 'page_takeover_button_size';
$page_takeover_button_color = 'page_takeover_button_color';
$page_takeover_button_url = 'page_takeover_button_url';
$page_takeover_decline = 'page_takeover_decline';
$page_takeover_decline_font = 'page_takeover_decline_font';
$page_takeover_decline_size = 'page_takeover_decline_size';
$page_takeover_decline_color = 'page_takeover_decline_color';
$page_takeover_trigger = 'page_takeover_trigger';
$page_takeover_frequency = 'page_takeover_frequency';
$page_takeover_placement = 'page_takeover_placement';
$page_takeover_status = 'page_takeover_status';
$page_takeover_custom_css = 'page_takeover_custom_css';

// Delete them from wp_options
delete_option( $page_takeover_background );

delete_option( $page_takeover_title );
delete_option( $page_takeover_title_font );
delete_option( $page_takeover_title_size );
delete_option( $page_takeover_title_color );
delete_option( $page_takeover_subtitle );
delete_option( $page_takeover_subtitle_font );
delete_option( $page_takeover_subtitle_size );
delete_option( $page_takeover_subtitle_color );
delete_option( $page_takeover_button );
delete_option( $page_takeover_button_background );
delete_option( $page_takeover_button_font );
delete_option( $page_takeover_button_size );
delete_option( $page_takeover_button_color );
delete_option( $page_takeover_button_url );
delete_option( $page_takeover_decline );
delete_option( $page_takeover_decline_font );
delete_option( $page_takeover_decline_size );
delete_option( $page_takeover_decline_color );
delete_option( $page_takeover_trigger );
delete_option( $page_takeover_frequency );
delete_option( $page_takeover_placement );
delete_option( $page_takeover_status );
delete_option( $page_takeover_custom_css );

?>