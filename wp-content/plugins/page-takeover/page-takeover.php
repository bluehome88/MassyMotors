<?php 
/*
Plugin Name: Page Takeover
Plugin URI: https://wordpress.org/plugins/page-takeover
Description: Create a full-screen overlay without a developer. Promote your content and offers in a full-screen popup.
Author: Boris Beo
Version: 1.0.2
Author URI: https://twitter.com/b_ris
Text Domain: page-takeover
Domain Path:   /languages/
License:
  Copyright 2016 RIS MEDIA
 
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.
 
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
 
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Add translation to plugin
function page_takeover_init() {
    $plugin_dir = basename(dirname(__FILE__));
    load_plugin_textdomain( 'page-takeover', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'page_takeover_init');

// Allow for translation of plugin description
$plugin_header_translate = array(
	__('Create a full-screen overlay without a developer. Promote your content and offers in a full-screen popup.', 'page-takeover'),
);

// Add our menu to WordPress
add_action( 'admin_menu', 'page_takeover_menu' );

function page_takeover_menu() {
    // Added a menu position decimal fix to prevent conflict with other themes using 31, such as Thesis Theme
    // @http://gabrielharper.com/blog/2012/08/wordpress-admin-menu-positioning-conflicts/
    $submenu = add_menu_page(__('Page Takeover','page-takeover'), __('Page Takeover','page-takeover'), 'manage_options', 'page-takeover', 'page_takeover_main_page', plugins_url('page-takeover/images/icon.png'), '30.3');
	
    // We want our JS and CSS loaded on our admin pages only, so let's just load them for now
    add_action( 'load-' . $submenu, 'load_page_takeover_admin_scripts' );
}

// Enqueue our CSS and JS on Page Takeover admin pages only
function load_page_takeover_admin_scripts() {
    add_action( 'admin_enqueue_scripts', 'page_takeover_admin_scripts' );
}

// Include our registration settings
include( plugin_dir_path( __FILE__ ) . 'includes/register-settings.php');
// Include our regular functions
include( plugin_dir_path( __FILE__ ) . 'includes/functions.php');

// Add our CSS and JS to admin head, but just for our pages (see load_page_takeover_admin_scripts above!)
function page_takeover_admin_scripts() {
    wp_enqueue_style('page-takeover-admin-stylesheet', plugins_url('/css/page-takeover-admin.css', __FILE__ ), array('page-takeover-google-font'));
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('page-takeover-color', plugins_url('/js/page-takeover-color.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
    wp_enqueue_script('page-takeover-toggle', plugins_url('/js/custom.js', __FILE__ ));
    wp_enqueue_script('page-takeover-autosize', plugins_url('/js/autosize.js', __FILE__ ));
    wp_register_style('page-takeover-google-font', '//fonts.googleapis.com/css?family=Share+Tech|Droid+Sans|Lobster|Fenix|Unkempt|Flavors|Viga|Damion|Oleo+Script|Racing+Sans+One|Nixie+One|Fredoka+One|Open+Sans|Overlock+SC|Bubbler+One|Contrail+One|Gochi+Hand|Roboto+Condensed|Russo+One|Cinzel+Decorative|News+Cycle|Marcellus+SC|Chewy|Quicksand|Sanchez|Signika+Negative|Gloria+Hallelujah|Grand+Hotel|Droid+Serif|Englebert|Oswald|Pacifico|Titan+One|Shadows+Into+Light|Dancing+Script|Luckiest+Guy|Parisienne|Coming+Soon|Baumans|Belgrano|Raleway');
}

// Enqueue our form CSS on front end
    add_action( 'wp_enqueue_scripts', 'page_takeover_scripts' );

// Add our CSS and JS to front-end head
function page_takeover_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_style('page-takeover-stylesheet', plugins_url('/css/page-takeover.css', __FILE__ ), array('page-takeover-google-font'));
    wp_register_style('page-takeover-google-font', page_takeover_used_fonts());
    $jquery_ui = array(
        "jquery-ui-core", //UI Core - do not remove this one
        "jquery-ui-dialog",
    );
    foreach ( $jquery_ui as $script ) {
        wp_enqueue_script( $script );
    }
}

// Make sure user can manage options
function page_takeover_options() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
?>

<?php }
function page_takeover_main_page() {
    { ?>
    <div class="wrap">
    	<h2><?php echo __('Page Takeover', 'page-takeover'); ?></h2>
        <h4 class="title"><a href="https://wordpress.org/support/plugin/page-takeover" target="_blank"><?php echo __('Get support', 'page-takeover'); ?></a>. <?php echo __('Plugin by Boris Beo', 'page-takeover'); ?>. <a href="https://twitter.com/b_ris" target="_blank"><?php echo __('Follow me on Twitter', 'page-takeover'); ?></a> ;)</h4>
    </div><!--wrap-->

    <?php if( isset($_GET['settings-updated']) ) { ?>
        <div id="message" class="updated">
            <p><strong><?php _e('Settings updated') ?></strong></p>
        </div>
    <?php } ?>
    
    <div id="page-takeover">
    	<div>
            <div class="page-takeover-container-left">
                <?php include( plugin_dir_path( __FILE__ ) . 'includes/full-screen-preview.php'); ?>
            </div><!--page-takeover-container-left-->
            <div class="page-takeover-container-right">
            	<?php include( plugin_dir_path( __FILE__ ) . 'includes/overlay-options.php'); ?>
            </div><!--page-takeover-container-right-->
            <div class="clear"></div>
        </div>
        
    </div><!--page-takeover-->

<?php }

} ?>