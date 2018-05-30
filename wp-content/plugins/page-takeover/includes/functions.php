<?php

// Set global variables
$page_takeover_background = get_option('page_takeover_background');

$page_takeover_title = get_option('page_takeover_title');
$page_takeover_title_font = get_option('page_takeover_title_font');
$page_takeover_title_size = get_option('page_takeover_title_size');
$page_takeover_title_color = get_option('page_takeover_title_color');
    
$page_takeover_subtitle = get_option('page_takeover_subtitle');
$page_takeover_subtitle_font = get_option('page_takeover_subtitle_font');
$page_takeover_subtitle_size = get_option('page_takeover_subtitle_size');
$page_takeover_subtitle_color = get_option('page_takeover_subtitle_color');
    
$page_takeover_button = get_option('page_takeover_button');
$page_takeover_button_url = get_option('page_takeover_button_url');
$page_takeover_button_background = get_option('page_takeover_button_background');
$page_takeover_button_font = get_option('page_takeover_button_font');
$page_takeover_button_size = get_option('page_takeover_button_size');
$page_takeover_button_color = get_option('page_takeover_button_color');
    
$page_takeover_decline = get_option('page_takeover_decline');
$page_takeover_decline_font = get_option('page_takeover_decline_font');
$page_takeover_decline_size = get_option('page_takeover_decline_size');
$page_takeover_decline_color = get_option('page_takeover_decline_color');

$page_takeover_status = get_option('page_takeover_status');
$page_takeover_custom_css = get_option('page_takeover_custom_css');

$page_takeover_included_fonts = array ("Arial", "Baumans", "Belgrano", "Chewy", "Cinzel Decorative", "Coming Soon", "Contrail One", "Damion", "Dancing Script", "Droid Sans", "Droid Serif", "Englebert", "Fenix", "Flavors", "Fredoka One", "Georgia", "Gloria Hallelujah", "Gochi Hand", "Grand Hotel", "Helvetica", "Lobster", "Luckiest Guy", "Marcellus SC", "News Cycle", "Nixie One", "Oleo Script", "Open Sans", "Oswald", "Overlock SC", "Pacifico", "Parisienne", "Quicksand", "Racing Sans One", "Raleway", "Roboto Condensed", "Russo One", "Sanchez", "Shadows Into Light", "Share Tech", "Signika Negative", "Tahoma", "Times New Roman", "Titan One", "Unkempt", "Verdana", "Viga");

$page_takeover_trigger = get_option('page_takeover_trigger');
$page_takeover_frequency = get_option('page_takeover_frequency');
$page_takeover_placement = get_option('page_takeover_placement');

// Set default background color
function page_takeover_default_background() {
    global $page_takeover_background;
    if(empty($page_takeover_background)) {
        $page_takeover_background = "#2c3644";
    }
    return $page_takeover_background;
}

// Set default title
function page_takeover_default_title() {
    global $page_takeover_title;
    if(empty($page_takeover_title)) {
        $page_takeover_title = __('Wait a minute :)', 'page-takeover');
    }
    return $page_takeover_title;
}

// Set default title font
function page_takeover_default_title_font() {
    global $page_takeover_title_font;
    if(empty($page_takeover_title_font)) {
        $page_takeover_title_font = "Open Sans";
    }
    return $page_takeover_title_font;
}

// Get title font options
function page_takeover_get_title_font_options() {
    global $page_takeover_title_font;
    global $page_takeover_included_fonts;
    foreach ($page_takeover_included_fonts as $key) {
        echo "<option value=\"" . $key . "\"";
        if($page_takeover_title_font == $key){
            echo "selected=selected";
        }
        echo ">" . $key . "</option>";
    }
}

// Set default title size
function page_takeover_default_title_size() {
    global $page_takeover_title_size;
    if(empty($page_takeover_title_size)) {
        $page_takeover_title_size = "3em";
    }
    return $page_takeover_title_size;
}

// Get title font size options
function page_takeover_get_title_size_options() {
    global $page_takeover_title_size;
    foreach (range(0.5, 4.5, 0.1) as $number) {
        echo "<option value=\"" . $number . "em\"";
        if($page_takeover_title_size == $number . "em") {
            echo "selected=selected";
        }
        echo">" . $number . "em</option>";
    }
}

// Set default title color
function page_takeover_default_title_color() {
    global $page_takeover_title_color;
    if(empty($page_takeover_title_color)) {
        $page_takeover_title_color = "#ffffff";
    }
    return $page_takeover_title_color;
}

// Set default subtitle
function page_takeover_default_subtitle() {
    global $page_takeover_subtitle;
    if(empty($page_takeover_subtitle)) {
        $page_takeover_subtitle = __('How about a discount?', 'page-takeover');
    }
    return $page_takeover_subtitle;
}

// Make sure our subtitle uses TinyMCE
function page_takeover_subtitle_textarea(){
    $page_takeover_tinymce = array( 'setup' => 'function(ed) {
                                ed.onChange.add(function() {
                                    jQuery("#page_takeover_subtitle_text",window.parent.document).val(tinyMCE.activeEditor.getContent());
                                    jQuery("#page_takeover_subtitle_text",window.parent.document).trigger("change");     
                                    });
                                }
                                '
                            );
    $page_takeover_subtitle_tinymce = array( 'textarea_name' => 'page_takeover_subtitle', 'wpautop' => false, 'tinymce' => $page_takeover_tinymce );
    echo wp_editor( page_takeover_default_subtitle(), 'page_takeover_subtitle_text', $page_takeover_subtitle_tinymce );
}

// Set default subtitle font
function page_takeover_default_subtitle_font() {
    global $page_takeover_subtitle_font;
    if(empty($page_takeover_subtitle_font)) {
        $page_takeover_subtitle_font = "Open Sans";
    }
    return $page_takeover_subtitle_font;
}

// Get subtitle font options
function page_takeover_get_subtitle_font_options() {
    global $page_takeover_subtitle_font;
    global $page_takeover_included_fonts;
    foreach ($page_takeover_included_fonts as $key) {
        echo "<option value=\"" . $key . "\"";
        if($page_takeover_subtitle_font == $key){
            echo "selected=selected";
        }
        echo ">" . $key . "</option>";
    }
}

// Set default subtitle size
function page_takeover_default_subtitle_size() {
    global $page_takeover_subtitle_size;
    if(empty($page_takeover_subtitle_size)) {
        $page_takeover_subtitle_size = "1.4em";
    }
    return $page_takeover_subtitle_size;
}

// Get subtitle font size options
function page_takeover_get_subtitle_size_options() {
    global $page_takeover_subtitle_size;
    foreach (range(0.5, 4.5, 0.1) as $number) {
        echo "<option value=\"" . $number . "em\"";
        if($page_takeover_subtitle_size == $number . "em") {
            echo "selected=selected";
        }
        echo">" . $number . "em</option>";
    }
}

// Set default subtitle color
function page_takeover_default_subtitle_color() {
    global $page_takeover_subtitle_color;
    if(empty($page_takeover_subtitle_color)) {
        $page_takeover_subtitle_color = "#ffffff";
    }
    return $page_takeover_subtitle_color;
}

// Set default button
function page_takeover_default_button() {
    global $page_takeover_button;
    if(empty($page_takeover_button)) {
        $page_takeover_button = __('Get Your Coupon Code', 'page-takeover');
    }
    return $page_takeover_button;
}

// Set default button URL
function page_takeover_default_button_url() {
    global $page_takeover_button_url;
    if(empty($page_takeover_button_url)) {
        $page_takeover_button_url = get_site_url();
    }
    return $page_takeover_button_url;
}

// Set default button font
function page_takeover_default_button_font() {
    global $page_takeover_button_font;
    if(empty($page_takeover_button_font)) {
        $page_takeover_button_font = "Open Sans";
    }
    return $page_takeover_button_font;
}

// Get button font options
function page_takeover_get_button_font_options() {
    global $page_takeover_button_font;
    global $page_takeover_included_fonts;
    foreach ($page_takeover_included_fonts as $key) {
        echo "<option value=\"" . $key . "\"";
        if($page_takeover_button_font == $key){
            echo "selected=selected";
        }
        echo ">" . $key . "</option>";
    }
}

// Set default button size
function page_takeover_default_button_size() {
    global $page_takeover_button_size;
    if(empty($page_takeover_button_size)) {
        $page_takeover_button_size = "1.4em";
    }
    return $page_takeover_button_size;
}

// Get button font size options
function page_takeover_get_button_size_options() {
    global $page_takeover_button_size;
    foreach (range(0.5, 4.5, 0.1) as $number) {
        echo "<option value=\"" . $number . "em\"";
        if($page_takeover_button_size == $number . "em") {
            echo "selected=selected";
        }
        echo">" . $number . "em</option>";
    }
}

// Set default button color
function page_takeover_default_button_color() {
    global $page_takeover_button_color;
    if(empty($page_takeover_button_color)) {
        $page_takeover_button_color = "#000000";
    }
    return $page_takeover_button_color;
}

// Set default button background color
function page_takeover_default_button_background() {
    global $page_takeover_button_background;
    if(empty($page_takeover_button_background)) {
        $page_takeover_button_background = "#fec230";
    }
    return $page_takeover_button_background;
}

// Set default decline
function page_takeover_default_decline() {
    global $page_takeover_decline;
    if(empty($page_takeover_decline)) {
        $page_takeover_decline = __('No thanks, I don\'t want to save money', 'page-takeover');
    }
    return $page_takeover_decline;
}

// Set default decline font
function page_takeover_default_decline_font() {
    global $page_takeover_decline_font;
    if(empty($page_takeover_decline_font)) {
        $page_takeover_decline_font = "Open Sans";
    }
    return $page_takeover_decline_font;
}

// Get decline font options
function page_takeover_get_decline_font_options() {
    global $page_takeover_decline_font;
    global $page_takeover_included_fonts;
    foreach ($page_takeover_included_fonts as $key) {
        echo "<option value=\"" . $key . "\"";
        if($page_takeover_decline_font == $key){
            echo "selected=selected";
        }
        echo ">" . $key . "</option>";
    }
}

// Set default decline size
function page_takeover_default_decline_size() {
    global $page_takeover_decline_size;
    if(empty($page_takeover_decline_size)) {
        $page_takeover_decline_size = "1em";
    }
    return $page_takeover_decline_size;
}

// Get decline font size options
function page_takeover_get_decline_size_options() {
    global $page_takeover_decline_size;
    foreach (range(0.5, 4.5, 0.1) as $number) {
        echo "<option value=\"" . $number . "em\"";
        if($page_takeover_decline_size == $number . "em") {
            echo "selected=selected";
        }
        echo">" . $number . "em</option>";
    }
}

// Set default decline color
function page_takeover_default_decline_color() {
    global $page_takeover_decline_color;
    if(empty($page_takeover_decline_color)) {
        $page_takeover_decline_color = "#a3a3a3";
    }
    return $page_takeover_decline_color;
}

// Overlay status
function page_takeover_get_status() {
    global $page_takeover_status;
    if (empty($page_takeover_status)) {
        $page_takeover_status = "active";
    }
    return $page_takeover_status;
}

// Custom CSS
function page_takeover_custom_css() {
    global $page_takeover_custom_css;
    return $page_takeover_custom_css;
}

// Set default impression trigger
function page_takeover_trigger() {
    global $page_takeover_trigger;
    if(empty($page_takeover_trigger)) {
        $page_takeover_trigger = 0;
    }
    return "<script type=\"text/javascript\">jQuery(document).ready(function(){setTimeout(function(){jQuery(\"#page-takeover-output\").dialog({autoOpen: true,dialogClass: \"page-takeover-dialog\",draggable: false,modal: true,closeText:\"\"});}, " . $page_takeover_trigger . "000)});</script>";
}

// Code comment beginning
function page_takeover_code_comment(){
    $page_takeover_code_comment = __('Full-screen overlay created by Page Takeover', 'page-takeover');
    $page_takeover_code_comment_url = "https://wordpress.org/plugins/page-takeover/";
    return "\n\n<!-- " . $page_takeover_code_comment . " -->\n<!-- " . $page_takeover_code_comment_url . " -->\n";
}

// Code comment end
function page_takeover_code_comment_end(){
    $page_takeover_code_comment_end = __('Page Takeover', 'page-takeover');
    return "\n<!-- / " . $page_takeover_code_comment_end . " -->\n\n";
}

// Get overlay code
function page_takeover_get_overlay_code() {
    include( plugin_dir_path( __FILE__ ) . 'full-screen-preview.php');
}

function page_takeover_show_overlay() {
    global $page_takeover_status;
    if (isset($_COOKIE['page_takeover_campaign'])) {
        // do nothing
    }
    else if ($page_takeover_status == "active") {
        return page_takeover_get_overlay_code();
    }
}

// Append overlay to content
add_filter( "wp_footer", "page_takeover_show_overlay" );

// Set cookie to control impression frequency
function page_takeover_set_cookie() {
    global $page_takeover_frequency;
    if ( !is_admin() && !isset($_COOKIE['page_takeover_campaign'])) {
        if (empty($page_takeover_frequency) || $page_takeover_frequency == 0) {
            // dont set a cookie at all :)
        }
        elseif ($page_takeover_frequency == 1) {
            setcookie('page_takeover_campaign', 1, 0, COOKIEPATH, COOKIE_DOMAIN, is_ssl());
        }
        elseif ($page_takeover_frequency == 2) {
            setcookie('page_takeover_campaign', 1, time()+3600*24*1, COOKIEPATH, COOKIE_DOMAIN, is_ssl());
        }
        elseif ($page_takeover_frequency == 3) {
            setcookie('page_takeover_campaign', 1, time()+3600*24*7, COOKIEPATH, COOKIE_DOMAIN, is_ssl());
        }
    }
}
add_action( 'init', 'page_takeover_set_cookie');

// Get used fonts and exclude regular fonts
function page_takeover_get_used_fonts() {
    global $page_takeover_title_font, $page_takeover_subtitle_font, $page_takeover_button_font, $page_takeover_decline_font;
    $page_takeover_active_fonts = array($page_takeover_title_font, $page_takeover_subtitle_font, $page_takeover_button_font, $page_takeover_decline_font);
    $page_takeover_font_delimiter = '';
    $page_takeover_font_string = '';
    
    if (is_array($page_takeover_active_fonts)) {
        foreach ($page_takeover_active_fonts as $val) {
            switch ($val) {
                case 'Arial':
                case 'Georgia':
                case 'Helvetica':
                case 'Tahoma':
                case 'Times New Roman':
                case 'Verdana':
                    break;
                default:
                    $page_takeover_font_string.= $page_takeover_font_delimiter . $val;
                    $page_takeover_font_delimiter = '|';
                    break;
            }
        }
    }
    return $page_takeover_font_string;
}

// Now include only these fonts to optimize load time
function page_takeover_used_fonts() {
	$page_takeover_google_url = "//fonts.googleapis.com/css?family=";
	return $page_takeover_google_url . page_takeover_get_used_fonts();

}

?>