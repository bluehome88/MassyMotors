<?php

/**
 * Plugin Name: Woobox
 * Plugin URI: http://woobox.com/
 * Description: Embed Woobox promotions using a shortcode. Usage: [woobox offer='abcdef']
 * Version: 1.4
 * Author: Woobox
 * Author URI: http://woobox.com
 * License: GPL
 */

function createWooboxEmbed($atts, $content = null) {
	
	$args = shortcode_atts(array(
		'offer' => '',
		'params' => '',
		'style' => '',
		'trigger' => '',
		'expire' => 0
	), $atts);
	
	if (!empty($args['offer'])) {
		wp_enqueue_script('woobox-sdk',plugins_url('/woobox_requiresdk.js', __FILE__), array('jquery'), false, false);
		$data_str = array();
		foreach($args as $k=>$v) {
			if($v!=='' && $v!=='embed' && $v!==0) {
				if($k === 'params') {
					$v = str_replace("'", '"', $v);
				}
				$data_attrs[] = "data-".$k."='".$v."'";
			}
		} $embed_code = "<div class='woobox-offer' ".implode(" ", $data_attrs)."></div>";
	} else {
		$embed_code = "<div><b>Woobox Plugin Error:</b> You did not specify an offer.</div>";
	}
	
	return $embed_code;
	
} add_shortcode('woobox', 'createWooboxEmbed');

/*

FUTURE release

function woobox_button() {
	
	global $typenow;
    if(!in_array($typenow, array('post', 'page'))) { return; }
    add_filter('mce_external_plugins', 'add_woobox_button_js');
    add_filter('mce_buttons', 'add_woobox_button_key');
} add_action('admin_head', 'woobox_button');

function add_woobox_button_js($plugin_array) {
    $plugin_array['woobox_button'] = plugins_url('/woobox_tinymce.js', __FILE__);
    return $plugin_array;
	
}

function add_woobox_button_key($buttons) {
    array_push($buttons, 'woobox_button');
    return $buttons;
}

*/
