<?php 

add_action( 'admin_init', 'page_takeover_register_settings' );

// Register Page Takeover settings
function page_takeover_register_settings() 
{
    register_setting( 'page-takeover-settings-group', 'page_takeover_background' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_title' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_title_font' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_title_size' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_title_color' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_subtitle' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_subtitle_font' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_subtitle_size' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_subtitle_color' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_button' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_button_background' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_button_font' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_button_size' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_button_color' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_button_url' );
    
    register_setting( 'page-takeover-settings-group', 'page_takeover_decline' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_decline_font' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_decline_size' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_decline_color' );
        
    register_setting( 'page-takeover-settings-group', 'page_takeover_trigger' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_frequency' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_placement' );
    
    register_setting( 'page-takeover-settings-group', 'page_takeover_status' );
    register_setting( 'page-takeover-settings-group', 'page_takeover_custom_css' );

}
?>