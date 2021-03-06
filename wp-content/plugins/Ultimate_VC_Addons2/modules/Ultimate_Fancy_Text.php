<?php
/*
* Add-on Name: Ultimate Fancy Text
* Add-on URI: http://dev.brainstormforce.com
*/
if(!class_exists('Ultimate_FancyText')){
	class Ultimate_FancyText{
		
		function __construct(){
			add_action('admin_init',array($this,'ultimate_fancytext_init'));
			add_shortcode('ultimate_fancytext',array($this,'ultimate_fancytext_shortcode'));
			add_action('wp_enqueue_scripts', array($this, 'register_fancytext_assets'));
		}
		function register_fancytext_assets()
		{
			wp_register_style('ultimate-fancytext-style',plugins_url('../assets/min-css/fancytext.min.css',__FILE__),array(),ULTIMATE_VERSION);
			wp_register_script('ultimate-typed-js',plugins_url('../assets/min-js/typed.min.js',__FILE__),array('jquery'),ULTIMATE_VERSION);
			wp_register_script('ultimate-vticker-js',plugins_url('../assets/min-js/vticker.min.js',__FILE__),array('jquery'),ULTIMATE_VERSION);
		}

		function ultimate_fancytext_init(){
			if(function_exists("vc_map")){
				vc_map(
					array(
					   "name" => __("Fancy Text","ultimate_vc"),
					   "base" => "ultimate_fancytext",
					   "class" => "vc_ultimate_fancytext",
					   "icon" => "vc_ultimate_fancytext",
					   "category" => "Ultimate VC Addons",
					   "description" => __("Fancy lines with animation effects.","ultimate_vc"),
					   "params" => array(
					   		array(
								"type" => "textfield",
								"param_name" => "fancytext_prefix",
								"heading" => __("Prefix","ultimate_vc"),
								"value" => "",
							),
							array(
								'type' => 'textarea',
								'heading' => __( 'Fancy Text', 'ultimate_vc' ),
								'param_name' => 'fancytext_strings',
								'description' => __('Enter each string on a new line','ultimate_vc'),
								'admin_label' => true
							),
							array(
								"type" => "textfield",
								"param_name" => "fancytext_suffix",
								"heading" => __("Suffix","ultimate_vc"),
								"value" => "",
							),
							array(
								"type" => "dropdown",
								"heading" => __("Effect", "ultimate_vc"),
								"param_name" => 'fancytext_effect',
								"value" => array(
									__("Type", "ultimate_vc") => "typewriter",
									__("Slide Up", "ultimate_vc") => "ticker",
									__("Slide Down", "ultimate_vc") => "ticker-down"
								),
							),
							array(
								"type" => "dropdown",
								"heading" => __("Alignment", "ultimate_vc"),
								"param_name" => "fancytext_align",
								"value" => array(
									__("Center","ultimate_vc") => "center",
									__("Left","ultimate_vc") => "left",
									__("Right","ultimate_vc") => "right"
								)
							),
							array(
								"type" => "number",
								"heading" => __("Type Speed", "ultimate_vc"),
								"param_name" => "strings_textspeed",
								"min" => 0,
								"value" => 35,
								"suffix" => __("In Miliseconds","ultimate_vc"),
								"group" => "Advanced Settings",
								"dependency" => array("element" => "fancytext_effect", "value" => array("typewriter")),
								"description" => __("Speed at which line progresses / Speed of typing effect.", "ultimate_vc")
							),
							array(
								"type" => "number",
								"heading" => __("Backspeed", "ultimate_vc"),
								"param_name" => "strings_backspeed",
								"min" => 0,
								"value" => 0,
								"suffix" => __("In Miliseconds","ultimate_vc"),
								"group" => "Advanced Settings",
								"dependency" => array("element" => "fancytext_effect", "value" => array("typewriter")),
								"description" => __("Speed of delete / backspace effect.", "ultimate_vc")
							),

							array(
								"type" => "number",
								"heading" => __("Start Delay", "ultimate_vc"),
								"param_name" => "strings_startdelay",
								"min" => 0,
								"value" => 200,
								"suffix" => __("In Miliseconds","ultimate_vc"),
								"group" => "Advanced Settings",
								"dependency" => array("element" => "fancytext_effect", "value" => array("typewriter")),
								"description" => __("Example - If set to 5000, the first string will appear after 5 seconds.", "ultimate_vc")
							),
							
							array(
								"type" => "number",
								"heading" => __("Back Delay", "ultimate_vc"),
								"param_name" => "strings_backdelay",
								"min" => 0,
								"value" => 1500,
								"suffix" => __("In Miliseconds","ultimate_vc"),
								"group" => "Advanced Settings",
								"dependency" => array("element" => "fancytext_effect", "value" => array("typewriter")),
								"description" => __("Example - If set to 5000, the string will remain visible for 5 seconds before backspace effect.","ultimate_vc")
							),
							array(
								"type" => "ult_switch",
								"heading" => __("Enable Loop","ultimate_vc"),
								"param_name" => "typewriter_loop",
								"value" => "true",
								"options" => array(
									"true" => array(
										"label" => "",
										"on" => "Yes",
										"off" => "No"
									)
								),
								"group" => "Advanced Settings",
								"dependency" => array("element" => "fancytext_effect", "value" => array("typewriter"))
							),
							array(
								"type" => "ult_switch",
								"heading" => __("Show Cursor","ultimate_vc"),
								"param_name" => "typewriter_cursor",
								"value" => "true",
								"options" => array(
									"true" => array(
										"label" => "",
										"on" => "Yes",
										"off" => "No",
									)
								),
								"group" => "Advanced Settings",
								"dependency" => array("element" => "fancytext_effect", "value" => array("typewriter"))
							),
							array(
								"type" => "textfield",
								"heading" => __("Cursor Text","ultimate_vc"),
								"param_name" => "typewriter_cursor_text",
								"value" => "|",
								"group" => "Advanced Settings",
								"dependency" => array("element" => "typewriter_cursor", "value" => array("true"))
							),
							array(
								"type" => "number",
								"heading" => __("Animation Speed", "ultimate_vc"),
								"param_name" => "strings_tickerspeed",
								"min" => 0,
								"value" => 200,
								"suffix" => __("In Miliseconds","ultimate_vc"),
								"group" => "Advanced Settings",
								"dependency" => array("element" => "fancytext_effect", "value" => array("ticker","ticker-down")),
								"description" => __("Duration of 'Slide Up' animation", "ultimate_vc")
							),
							array(
								"type" => "number",
								"heading" => __("Pause Time", "ultimate_vc"),
								"param_name" => "ticker_wait_time",
								"min" => 0,
								"value" => "3000",
								"suffix" => __("In Miliseconds","ultimate_vc"),
								"group" => "Advanced Settings",
								"dependency" => array("element" => "fancytext_effect", "value" => array("ticker","ticker-down")),
								"description" => __("How long the string should stay visible?","ultimate_vc")
							),
							array(
								"type" => "number",
								"heading" => __("Show Items", "ultimate_vc"),
								"param_name" => "ticker_show_items",
								"min" => 1,
								"value" => 1,
								"group" => "Advanced Settings",
								"dependency" => array("element" => "fancytext_effect", "value" => array("ticker","ticker-down")),
								"description" => __("How many items should be visible at a time?", "ultimate_vc")
							),
							array(
								"type" => "ult_switch",
								"heading" => __("Pause on Hover","ultimate_vc"),
								"param_name" => "ticker_hover_pause",
								"value" => "",
								"options" => array(
									"true" => array(
										"label" => "",
										"on" => "Yes",
										"off" => "No",
									)
								),
								"group" => "Advanced Settings",
								"dependency" => array("element" => "fancytext_effect", "value" => array("ticker","ticker-down"))
							),
							array(
								"type" => "textfield",
								"heading" => __("Extra Class","ultimate_vc"),
								"param_name" => "ex_class"
							),
							array(
								"type" => "ultimate_google_fonts",
								"heading" => __("Font Family", "ultimate_vc"),
								"param_name" => "strings_font_family",
								"description" => __("Select the font of your choice.","ultimate_vc")." ".__("You can","ultimate_vc")." <a target='_blank' href='".admin_url('admin.php?page=ultimate-font-manager')."'>".__("add new in the collection here","ultimate_vc")."</a>.",
								"group" => "Typography"
							),
							array(
								"type" => "ultimate_google_fonts_style",
								"heading" 		=>	__("Font Style", "ultimate_vc"),
								"param_name"	=>	"strings_font_style",
								"group" => "Typography"
							),
							array(
								"type" => "number",
								"class" => "font-size",
								"heading" => __("Font Size", "ultimate_vc"),
								"param_name" => "strings_font_size",
								"min" => 10,
								"suffix" => "px",
								"group" => "Typography"
							),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Line Height", "ultimate_vc"),
								"param_name" => "strings_line_height",
								"value" => "",
								"suffix" => "px",
								"group" => "Typography"
							),
							array(
								"type" => "colorpicker",
								"heading" => __("Fancy Text Color","ultimate_vc"),
								"param_name" => "fancytext_color",
								"group" => "Advanced Settings",
								"group" => "Typography",
								"dependency" => array("element" => "fancytext_effect", "value" => array("typewriter","ticker","ticker-down"))
							),
							array(
								"type" => "colorpicker",
								"heading" => __("Fancy Text Background","ultimate_vc"),
								"param_name" => "ticker_background",
								"group" => "Advanced Settings",
								"group" => "Typography",
								"dependency" => array("element" => "fancytext_effect", "value" => array("typewriter","ticker","ticker-down"))
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Prefix & Suffix Text Color", "ultimate_vc"),
								"param_name" => "strings_color",
								"value" => "",
								"group" => "Typography"
							),
							array(
								"type" => "colorpicker",
								"heading" => __("Cursor Color","ultimate_vc"),
								"param_name" => "typewriter_cursor_color",
								"group" => "Advanced Settings",
								"group" => "Typography",
								"dependency" => array("element" => "fancytext_effect", "value" => array("typewriter"))
							),
							array(
								"type" => "dropdown",
								"heading" => __("Markup","ultimate_vc"),
								"param_name" => "fancytext_tag",
								"value" => array(
									__("div","ultimate_vc") => "div",
									__("H1","ultimate_vc") => "h1",
									__("H2","ultimate_vc") => "h2",
									__("H3","ultimate_vc") => "h3",
									__("H4","ultimate_vc") => "h4",
									__("H5","ultimate_vc") => "h5",
									__("H6","ultimate_vc") => "h6",
								),
								"group" => "Typography",
							),
							array(
								"type" => "heading",
								"sub_heading" => "<span style='display: block;'><a href='http://bsf.io/t5ir4' target='_blank'>".__("Watch Video Tutorial","ultimate_vc")." &nbsp; <span class='dashicons dashicons-video-alt3' style='font-size:30px;vertical-align: middle;color: #e52d27;'></span></a></span>",
								"param_name" => "notification",
								'edit_field_class' => 'ult-param-important-wrapper ult-dashicon ult-align-right ult-bold-font ult-blue-font vc_column vc_col-sm-12',
							),
						)
					)
				);
			}
		}
		function ultimate_fancytext_shortcode($atts, $content = null){
			$output = $fancytext_strings = $fancytext_prefix = $fancytext_suffix = $fancytext_effect = $strings_textspeed = $strings_tickerspeed = $typewriter_cursor = $typewriter_cursor_text = $typewriter_loop = $fancytext_align = $strings_font_family = $strings_font_style = $strings_font_size = $strings_color = $strings_line_height = $strings_startdelay = $strings_backspeed = $strings_backdelay = $ticker_wait_time = $ticker_show_items = $ticker_hover_pause = $ex_class = '';
			
			$id = uniqid(rand());
			
			extract(shortcode_atts(array(
				'fancytext_strings' => '',
				'fancytext_prefix' => '',
				'fancytext_suffix' => '',
				'fancytext_effect' => '',
				'strings_textspeed' => '35',
				'strings_tickerspeed' => '200',
				'typewriter_loop' => 'false',
				'typewriter_cursor_color' => '',
				'fancytext_tag' => 'div',
				'fancytext_align' => 'center',
				'strings_font_family' => '',
				'strings_font_style' => '',
				'strings_font_size' => '',
				'strings_color' => '',
				'strings_line_height' => '',
				'strings_startdelay' => '200',
				'strings_backspeed' => '0',
				'strings_backdelay' => '1500',
				'typewriter_cursor' => 'true',
				'typewriter_cursor_text' => '|',
				'ticker_wait_time' => '3000',
				'ticker_show_items' => '1',
				'ticker_hover_pause' => 'true',
				'ticker_background' => '',
				'fancytext_color' => '',
				'ex_class' => ''
			),$atts));
			
			$string_inline_style = $vticker_inline = $valign = '';
			
			if($strings_font_family != '')
			{
				$font_family = get_ultimate_font_family($strings_font_family);
				$string_inline_style .= 'font-family:\''.$font_family.'\';';
			}
			
			$string_inline_style .= get_ultimate_font_style($strings_font_style);
			
			if($strings_font_size != '')
				$string_inline_style .= 'font-size:'.$strings_font_size.'px;';
			
			if($strings_color != '')
				$string_inline_style .= 'color:'.$strings_color.';';
				
			if($strings_line_height != '')
				$string_inline_style .= 'line-height:'.$strings_line_height.'px;';
				
			if($fancytext_align != '')
				$string_inline_style .= 'text-align:'.$fancytext_align.';';
			
			// Order of replacement
			$order   = array("\r\n", "\n", "\r", "<br/>", "<br>");
			$replace = '|';
			
			// Processes \r\n's first so they aren't converted twice.
			$str = str_replace($order, $replace, $fancytext_strings);
			
			$lines = explode("|", $str);
			
			$count_lines = count($lines);
			
			$ex_class .= ' uvc-type-align-'.$fancytext_align.' ';
			if($fancytext_prefix == '')
				$ex_class .= 'uvc-type-no-prefix';
				
			if($fancytext_color != '')
				$vticker_inline .= 'color:'.$fancytext_color.';';
			if($ticker_background != '')
			{
				$vticker_inline .= 'background:'.$ticker_background.';';
				if($fancytext_effect == 'typewriter')
					$valign = 'fancytext-typewriter-background-enabled';
				else
					$valign = 'fancytext-background-enabled';
			}
			
			$ultimate_js = get_option('ultimate_js');
			
			$output = '<'.$fancytext_tag.' class="uvc-type-wrap '.$ex_class.' uvc-wrap-'.$id.'" style="'.$string_inline_style.'">';
				if(trim($fancytext_prefix) != '')
				{
					$output .= '<span class="ultimate-'.$fancytext_effect.'-prefix">'.ltrim($fancytext_prefix).'</span>';
				}
				if($fancytext_effect == 'ticker' || $fancytext_effect == 'ticker-down')
				{
					if($ultimate_js != 'enable')
						wp_enqueue_script('ultimate-vticker-js');
					if($strings_font_size != '')
						$inherit_font_size = 'ultimate-fancy-text-inherit';
					else
						$inherit_font_size = '';
					if($ticker_hover_pause != 'true')
						$ticker_hover_pause = 'false';
					if($fancytext_effect == 'ticker-down')
						$direction = "down";
					else
						$direction = "up";
					$output .= '<div id="vticker-'.$id.'" class="ultimate-vticker '.$fancytext_effect.' '.$valign.' '.$inherit_font_size.'" style="'.$vticker_inline.'"><ul>';
						foreach($lines as $line)
						{
							$output .= '<li>'.strip_tags($line).'</li>';
						}
					$output .= '</ul></div>'; 
				}
				else
				{
					if($ultimate_js != 'enable')
						wp_enqueue_script('ultimate-typed-js');
					if($typewriter_loop != 'true')
						$typewriter_loop = 'false';			
					if($typewriter_cursor != 'true')
						$typewriter_cursor = 'false';						
					$strings = '['; 
						foreach($lines as $key => $line)  
						{ 
							$strings .= '"'.__(trim(strip_tags($line)),'js_composer').'"';
							if($key != ($count_lines-1))
								$strings .= ','; 
						} 
					$strings .= ']';
					$output .= '<span id="typed-'.$id.'" class="ultimate-typed-main '.$valign.'" style="'.$vticker_inline.'"></span>';
				}
				if(trim($fancytext_suffix) != '')
				{
					$output .= '<span class="ultimate-'.$fancytext_effect.'-suffix">'.rtrim($fancytext_suffix).'</span>';
				}
				if($fancytext_effect == 'ticker' || $fancytext_effect == 'ticker-down')
				{
					$output .= '<script type="text/javascript">
						jQuery(function($){
							$("#vticker-'.$id.'")
									.vTicker(
									{
										speed: '.$strings_tickerspeed.',
										showItems: '.$ticker_show_items.',
										pause: '.$ticker_wait_time.',
										mousePause : '.$ticker_hover_pause.',
										direction: "'.$direction.'",
									}
								);
						});
					</script>';
				}
				else
				{
					$output .= '<script type="text/javascript"> jQuery(function($){ $("#typed-'.$id.'").typed({ 
								strings: '.$strings.',
								typeSpeed: '.$strings_textspeed.',
								backSpeed: '.$strings_backspeed.',
								startDelay: '.$strings_startdelay.',
								backDelay: '.$strings_backdelay.',
								loop: '.$typewriter_loop.',
								loopCount: false,
								showCursor: '.$typewriter_cursor.',
								cursorChar: "'.$typewriter_cursor_text.'",
								attr: null
							});
						});
					</script>';
					if($typewriter_cursor_color != '')
					{
						$output .= '<style>
							.uvc-wrap-'.$id.' .typed-cursor {
								color:'.$typewriter_cursor_color.';
							}
						</style>';
					}
				}
			$output .= '</'.$fancytext_tag.'>';
			
			/*$args = array(
				$strings_font_family
			);
			enquque_ultimate_google_fonts($args);*/
			
			return $output;
		}
	} // end class
	new Ultimate_FancyText;
}