<?php
/*
* Module - Buttons
*/
if(!class_exists("Ultimate_Buttons")){
	class Ultimate_Buttons{
		function __construct(){
			add_action( 'admin_init', array($this, 'init_buttons') );
			add_shortcode( 'ult_buttons',array($this,'ult_buttons_shortcode'));
			add_action( 'admin_enqueue_scripts', array( $this, 'button_admin_scripts') );
		}
		function button_admin_scripts($hook){
			if($hook == "post.php" || $hook == "post-new.php"){
				wp_enqueue_style( 'ult-button', plugins_url('../assets/css/btn-min.css', __FILE__) );
			}
		}
		function ult_buttons_shortcode($atts){
			
			$output = $btn_title = $btn_link = $btn_size = $btn_width = $btn_height = $btn_hover = $btn_bg_color = $btn_radius = $btn_shadow = '';
			$btn_shadow_color = $btn_bg_color_hover = $btn_border_style = $btn_color_border = $btn_border_size = $btn_shadow_size = $el_class = '';
			$btn_font_family = $btn_font_style = $btn_title_color = $btn_font_size = $icon = $icon_size = $icon_color = $btn_icon_pos = $btn_anim_effect = '';
			$btn_padding_left = $btn_padding_top = $button_bg_img = $btn_title_color_hover = $btn_align = $btn_color_border_hover = $btn_shadow_color_hover = '';
			$btn_shadow_click = $enable_tooltip = $tooltip_text = $tooltip_pos = '';
			extract(shortcode_atts(array(
				'btn_title' => '',
				'btn_link' => '',
				'btn_size' => '',
				'btn_width' => '',
				'btn_height' => '',
				'btn_padding_left' => '',
				'btn_padding_top' => '',
				'btn_hover' => '',
				'btn_bg_color' => '',
				'btn_radius' => '',
				'btn_shadow' => '',
				'btn_shadow_color' => '',
				'btn_shadow_size' => '',
				'btn_bg_color_hover' => '',
				'btn_title_color_hover' => '',
				'btn_border_style' => '',
				'btn_color_border' => '',
				'btn_color_border_hover' => '',
				'btn_border_size' => '',
				'btn_font_family' => '',
				'btn_font_style' => '',
				'btn_title_color' => '',
				'btn_font_size' => '',
				'icon' => '',
				'icon_size' => '',
				'icon_color' => '',
				'btn_icon_pos' => '',
				'btn_anim_effect' => '',
				'button_bg_img' => '',
				'btn_align' => '',
				'btn_shadow_color_hover' => '',
				'btn_shadow_click' => '',
				'enable_tooltip' => '',
				'tooltip_text' => '',
				'tooltip_pos' => '',
				'el_class' => '',
			),$atts));
			
			$style = $hover_style = $btn_style_inline = $link_sufix = $link_prefix = $img = $shadow_hover = $shadow_click = $shadow_color = $box_shadow = '';
			$tooltip = $tooltip_class = '';
			$el_class .= ' '.$btn_anim_effect.' ';
			$uniqid = uniqid();
			$tooltip_class = 'tooltip-'.$uniqid;
			
			if($enable_tooltip == "yes"){
				wp_enqueue_script('aio-tooltip',plugins_url('../assets/min-js/',__FILE__).'tooltip.min.js',array('jquery'));
				wp_enqueue_style('aio-tooltip',plugins_url('../assets/min-css/',__FILE__).'tooltip.min.css');
				$tooltip .= 'data-toggle="tooltip" data-placement="'.$tooltip_pos.'" title="'.$tooltip_text.'"';
				$tooltip_class .= " ubtn-tooltip ".$tooltip_pos;
			}
			
			if($btn_shadow_click !== "enable"){
				$shadow_click = 'none';
			}
			if($btn_shadow_color_hover == "")
				$shadow_color = $btn_shadow_color;
			else
				$shadow_color = $btn_shadow_color_hover;
			
			if($button_bg_img !== ''){
				$img = wp_get_attachment_image_src( $button_bg_img, 'large');
				$img = $img[0];
			}
			if($btn_link !== ''){
				$href = vc_build_link($btn_link);
				if($href['url'] !== ""){
					$target = (isset($href['target'])) ? "target='".$href['target']."'" : '';
					if($btn_size == "ubtn-block"){
						$tooltip_class .= ' ubtn-block';
					}
					$link_prefix .= '<a class="ubtn-link '.$btn_align.' '.$tooltip_class.'" '.$tooltip.' href = "'.$href['url'].'" '.$target.'>';
					$link_sufix .= '</a>';
				}
			} else {
				if($enable_tooltip !== ""){
					$link_prefix .= '<span class="'.$btn_align.' '.$tooltip_class.'" '.$tooltip.'>';
					$link_sufix .= '</span>';
				}
			}
			if($btn_icon_pos !== '' && $icon !== 'none' && $icon !== '')
				$el_class .= ' ubtn-sep-icon '.$btn_icon_pos.' ';
			
			if($btn_font_family != '')
			{
				$mhfont_family = get_ultimate_font_family($btn_font_family);
				$btn_style_inline .= 'font-family:\''.$mhfont_family.'\';';
				
				//enqueue google font
				/*$args = array(
					$mhfont_family
				);
				enquque_ultimate_google_fonts($args);*/
			}
			$btn_style_inline .= get_ultimate_font_style($btn_font_style);
			if($btn_font_size !== ''){
				$btn_style_inline .= 'font-size:'.$btn_font_size.'px;';
			}
			$style .= $btn_style_inline;
			if($btn_size == 'ubtn-custom'){
				$style .= 'width:'.$btn_width.'px;';
				$style .= 'min-height:'.$btn_height.'px;';
				$style .= 'padding:'.$btn_padding_top.'px '.$btn_padding_left.'px;';
			}
			if($btn_border_style !== ''){
				$style .= 'border-radius:'.$btn_radius.'px;';
				$style .= 'border-width:'.$btn_border_size.'px;';
				$style .= 'border-color:'.$btn_color_border.';';
				$style .= 'border-style:'.$btn_border_style.';';
			} else {
				$style .= 'border:none;';
			}
			if($btn_shadow !== ''){
				switch($btn_shadow){
					case 'shd-top':
						$style .= 'box-shadow: 0 -'.$btn_shadow_size.'px '.$btn_shadow_color.';';
						// $style .= 'bottom: '.($btn_shadow_size-3).'px;';
						$box_shadow .= '0 -'.$btn_shadow_size.'px '.$btn_shadow_color.';';
						if($shadow_click !== "none")
							$shadow_hover .= '0 -3px '.$shadow_color.';';
						else
							$shadow_hover .= '0 -'.$btn_shadow_size.'px '.$shadow_color.';';
						break;
					case 'shd-bottom':
						$style .= 'box-shadow: 0 '.$btn_shadow_size.'px '.$btn_shadow_color.';';
						// $style .= 'top: '.($btn_shadow_size-3).'px;';
						$box_shadow .= '0 '.$btn_shadow_size.'px '.$btn_shadow_color.';';
						if($shadow_click !== "none")
							$shadow_hover .= '0 3px '.$shadow_color.';';	
						else
							$shadow_hover .= '0 '.$btn_shadow_size.'px '.$shadow_color.';';
						break;
					case 'shd-left':
						$style .= 'box-shadow: -'.$btn_shadow_size.'px 0 '.$btn_shadow_color.';';
						// $style .= 'right: '.($btn_shadow_size-3).'px;';
						$box_shadow .= '-'.$btn_shadow_size.'px 0 '.$btn_shadow_color.';';
						if($shadow_click !== "none")
							$shadow_hover .= '-3px 0 '.$shadow_color.';';
						else
							$shadow_hover .= '-'.$btn_shadow_size.'px 0 '.$shadow_color.';';	
						break;
					case 'shd-right':
						$style .= 'box-shadow: '.$btn_shadow_size.'px 0 '.$btn_shadow_color.';';
						// $style .= 'left: '.($btn_shadow_size-3).'px;';
						$box_shadow .= $btn_shadow_size.'px 0 '.$btn_shadow_color.';';
						if($shadow_click !== "none")
							$shadow_hover .= '3px 0 '.$shadow_color.';';
						else
							$shadow_hover .= $btn_shadow_size.'px 0 '.$shadow_color.';';
						break;
				}
			}
			if($btn_bg_color !== ''){
				$style .= 'background: '.$btn_bg_color.';';
			}
			if($btn_title_color !== ''){
				$style .= 'color: '.$btn_title_color.';';
			}
			
			if($btn_shadow){
				$el_class .= ' ubtn-shd ';
			}
			if($btn_align){
				$el_class .= ' '.$btn_align.' ';
			}
			if($btn_title == "" && $icon !== ""){
				$el_class .= ' ubtn-only-icon ';
			}
			$output .= '<button type="button" class="ubtn '.$btn_size.' '.$btn_hover.' '.$el_class.' '.$btn_shadow.'" data-hover="'.$btn_title_color_hover.'" data-border-color="'.$btn_color_border.'" data-hover-bg="'.$btn_bg_color_hover.'" data-border-hover="'.$btn_color_border_hover.'" data-shadow-hover="'.$shadow_hover.'" data-shadow-click="'.$shadow_click.'" data-shadow="'.$box_shadow.'" data-shd-shadow="'.$btn_shadow_size.'" style="'.$style.'">';
			if($icon !== ''){
				$output .= '<span class="ubtn-data ubtn-icon"><i class="'.$icon.'" style="font-size:'.$icon_size.'px;color:'.$icon_color.';"></i></span>';
			}
			$output .= '<span class="ubtn-hover"></span>';
			$output .= '<span class="ubtn-data ubtn-text">'.$btn_title.'</span>';
			$output .= '</button>';
			
			$output = $link_prefix.$output.$link_sufix;
			
			if($btn_align == "ubtn-center"){
				$output = '<div class="ubtn-ctn-center">'.$output.'</div>';
			}
			if($img !== ''){
				$html = '<div class="ubtn-img-container">';
				$html .= '<img src="'.$img.'"/>';
				$html .= $output;
				$html .= '</div>';
				$output = $html;
			}
			
			if($enable_tooltip !== ""){
				$output .= '<script>
					jQuery(function () {
						jQuery(".tooltip-'.$uniqid.'").bsf_tooltip();
					})
				</script>';
			}
			return $output;
		}
		function init_buttons(){
			if(function_exists("vc_map"))
			{
				$json = ultimate_get_icon_position_json();
				vc_map(
					array(
						"name" => __("Advanced Button", "ultimate_vc"),
						"base" => "ult_buttons",
						"icon" => "ult_buttons",
						"class" => "ult_buttons",
						"content_element" => true,
						"controls" => "full",
						"category" => "Ultimate VC Addons",
						"description" => __("Create creative buttons.","ultimate_vc"),
						"params" => array(
							array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Button Title","ultimate_vc"),
								"param_name" => "btn_title",
								"value" => "",
								"description" => "",
								"group" => "General",
								"admin_label" => true
						  	),
							array(
								"type" => "vc_link",
								"class" => "",
								"heading" => __("Button Link","ultimate_vc"),
								"param_name" => "btn_link",
								"value" => "",
								"description" => "",
								"group" => "General"
						  	),
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Button Alignment","ultimate_vc"),
								"param_name" => "btn_align",
								"value" => array(
										"Left Align" => "ubtn-left",
										"Center Align" => "ubtn-center",
										"Right Align" => "ubtn-right",
									),
								"description" => "",
								"group" => "General"
						  	),
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Button Size","ultimate_vc"),
								"param_name" => "btn_size",
								"value" => array(
										__("Normal Button","ultimate_vc") => "ubtn-normal",
										__("Mini Button","ultimate_vc") => "ubtn-mini",
										__("Small Button","ultimate_vc") => "ubtn-small",
										__("Large Button","ultimate_vc") => "ubtn-large",
										__("Button Block","ultimate_vc") => "ubtn-block",
										__("Custom Size","ultimate_vc") => "ubtn-custom",
									),
								"description" => "",
								"group" => "General"
						  	),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Button Width","ultimate_vc"),
								"param_name" => "btn_width",
								"value" => "",
								"min" => 10,
								"max" => 1000,
								"suffix" => "px",
								"description" => "",
								"dependency" => Array("element" => "btn_size", "value" => "ubtn-custom" ),
								"group" => "General"
						  	),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Button Height","ultimate_vc"),
								"param_name" => "btn_height",
								"value" => "",
								"min" => 10,
								"max" => 1000,
								"suffix" => "px",
								"description" => "",
								"dependency" => Array("element" => "btn_size", "value" => "ubtn-custom" ),
								"group" => "General"
						  	),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Button Left / Right Padding","ultimate_vc"),
								"param_name" => "btn_padding_left",
								"value" => "",
								"min" => 10,
								"max" => 1000,
								"suffix" => "px",
								"description" => "",
								"dependency" => Array("element" => "btn_size", "value" => "ubtn-custom" ),
								"group" => "General"
						  	),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Button Top / Bottom Padding","ultimate_vc"),
								"param_name" => "btn_padding_top",
								"value" => "",
								"min" => 10,
								"max" => 1000,
								"suffix" => "px",
								"description" => "",
								"dependency" => Array("element" => "btn_size", "value" => "ubtn-custom" ),
								"group" => "General"
						  	),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Button Title Color","ultimate_vc"),
								"param_name" => "btn_title_color",
								"value" => "#000000",
								"description" => "",
								"group" => "General"
						  	),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Background Color","ultimate_vc"),
								"param_name" => "btn_bg_color",
								"value" => "#e0e0e0",
								"description" => "",
								"group" => "General"
						  	),
							array(
								"type" => "textfield",
								"heading" => __("Extra class name", "ultimate_vc"),
								"param_name" => "el_class",
								"description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "ultimate_vc"),
								"group" => "General"
							),
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Button Hover Background Effect","ultimate_vc"),
								"param_name" => "btn_hover",
								"value" => array(
										__("No Effect","ultimate_vc") => "ubtn-no-hover-bg",
										__("Fade Background","ultimate_vc") => "ubtn-fade-bg",
										__("Fill Background from Top","ultimate_vc") => "ubtn-top-bg",
										__("Fill Background from Bottom","ultimate_vc") => "ubtn-bottom-bg",
										__("Fill Background from Left","ultimate_vc") => "ubtn-left-bg",
										__("Fill Background from Right","ultimate_vc") => "ubtn-right-bg",
										__("Fill Background from Center Horizontally","ultimate_vc") => "ubtn-center-hz-bg",
										__("Fill Background from Center Vertically","ultimate_vc") => "ubtn-center-vt-bg",
										__("Fill Background from Center Diagonal","ultimate_vc") => "ubtn-center-dg-bg",
									),
								"description" => "",
								"group" => "Background"
						  	),
							array(
								"type" => "dropdown",
								"class" => "no-ult-effect",
								'edit_field_class' => 'ult-no-effect vc_column vc_col-sm-12',
								"heading" => __("Button Hover Animation Effects","ultimate_vc"),
								"param_name" => "btn_anim_effect",
								"value" => array(
										"No Effect" 			   => "none",
										"Grow" 					=> "ulta-grow",
										"Shrink" 			  	  => "ulta-shrink",
										"Pulse" 			   	   => "ulta-pulse",
										"Pulse Grow" 		  	  => "ulta-pulse-grow",
										"Pulse Shrink" 			=> "ulta-pulse-shrink",
										"Push" 					=> "ulta-push",
										"Pop" 				 	 => "ulta-pop",
										"Rotate" 			  	  => "ulta-rotate",
										"Grow Rotate" 		 	 => "ulta-grow-rotate",
										"Float" 			   	   => "ulta-float",
										"Sink" 					=> "ulta-sink",
										"Hover" 			   	   => "ulta-hover",
										"Hang" 					=> "ulta-hang",
										"Skew" 					=> "ulta-skew",
										"Skew Forward" 			=> "ulta-skew-forward",
										"Skew Backward" 	   	   => "ulta-skew-backward",
										"Wobble Horizontal"   	   => "ulta-wobble-horizontal",
										"Wobble Vertical" 	 	 => "ulta-wobble-vertical",
										"Wobble to Bottom Right"  => "ulta-wobble-to-bottom-right",
										"Wobble to Top Right" 	 => "ulta-wobble-to-top-right",
										"Wobble Top" 		  	  => "ulta-wobble-top",
										"Wobble Bottom" 	   	   => "ulta-wobble-bottom",
										"Wobble Skew" 		 	 => "ulta-wobble-skew",
										"Buzz" 					=> "ulta-buzz",
										"Buzz Out" 				=> "ulta-buzz-out",
									),
								"description" => "",
								"group" => "Background"
						  	),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Hover Background Color","ultimate_vc"),
								"param_name" => "btn_bg_color_hover",
								"value" => "",
								"description" => "",
								"group" => "Background"
						  	),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Hover Text Color","ultimate_vc"),
								"param_name" => "btn_title_color_hover",
								"value" => "",
								"description" => "",
								"group" => "Background"
						  	),
							array(
								"type" => "attach_image",
								"class" => "",
								"heading" => __("Button Background Image","ultimate_vc"),
								"param_name" => "button_bg_img",
								"value" => "",
								"description" => __("Upload the image on which you want to place the button.","ultimate_vc"),
								"group" => "Background"
							),
							array(
								"type" => "icon_manager",
								"class" => "",
								"heading" => __("Select Icon ","ultimate_vc"),
								"param_name" => "icon",
								"value" => "",
								"description" => __("Click and select icon of your choice. If you can't find the one that suits for your purpose, you can","ultimate_vc")." <a href='admin.php?page=font-icon-Manager' target='_blank'>".__('add new here','ultimate_vc')."</a>.",
								"group" => "Icon"
							),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Size of Icon", "ultimate_vc"),
								"param_name" => "icon_size",
								"value" => 32,
								"min" => 12,
								"max" => 72,
								"suffix" => "px",
								"description" => __("How big would you like it?", "ultimate_vc"),
								"group" => "Icon"
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Color", "ultimate_vc"),
								"param_name" => "icon_color",
								"value" => "",
								"description" => __("Give it a nice paint!", "ultimate_vc"),
								"group" => "Icon"
							),
							/*
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Icon Position", "smile"),
								"param_name" => "btn_icon_pos",
								"value" => array(
									"Icon pull from left" => "ubtn-sep-icon-left",
									"Icon push to left" => "ubtn-sep-icon-left-rev",
									"Icon pull from right" => "ubtn-sep-icon-right",
									"Icon push to right" => "ubtn-sep-icon-right-rev",
									"Icon push from top" => "ubtn-sep-icon-top-push",
									"Icon push from bottom" => "ubtn-sep-icon-bottom-push",
									"Icon push from left" => "ubtn-sep-icon-left-push",
									"Icon push from right" => "ubtn-sep-icon-right-push",
								),
								"description" => "",
								"group" => "Icon"
							),
							*/
							array(
								"type" => "ult_button",
								"class" => "",
								"heading" => __("Icon Position ","ultimate_vc"),
								"param_name" => "btn_icon_pos",
								"value" => "",
								"json" => $json,
								"description" => "",
								"group" => "Icon"
							),
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Button Border Style", "ultimate_vc"),
								"param_name" => "btn_border_style",
								"value" => array(
									"None"=> "",
									"Solid"=> "solid",
									"Dashed" => "dashed",
									"Dotted" => "dotted",
									"Double" => "double",
									"Inset" => "inset",
									"Outset" => "outset",
								),
								"description" => "",
								"group" => "Border"
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Border Color", "ultimate_vc"),
								"param_name" => "btn_color_border",
								"value" => "",
								"description" => "",
								"dependency" => Array("element" => "btn_border_style", "not_empty" => true),
								"group" => "Border"
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Border Color on Hover", "ultimate_vc"),
								"param_name" => "btn_color_border_hover",
								"value" => "",
								"description" => "",
								"dependency" => Array("element" => "btn_border_style", "not_empty" => true),
								"group" => "Border"
							),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Border Width", "ultimate_vc"),
								"param_name" => "btn_border_size",
								"value" => 1,
								"min" => 1,
								"max" => 10,
								"suffix" => "px",
								"description" => "",
								"dependency" => Array("element" => "btn_border_style", "not_empty" => true),
								"group" => "Border"
							),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Border Radius","ultimate_vc"),
								"param_name" => "btn_radius",
								"value" => 3,
								"min" => 0,
								"max" => 500,
								"suffix" => "px",
								"description" => "",
								"dependency" => Array("element" => "btn_border_style", "not_empty" => true),
								"group" => "Border"
						  	),
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Button Shadow", "ultimate_vc"),
								"param_name" => "btn_shadow",
								"value" => array(
										__('No Shadow','ultimate_vc') => '',
										__('Shadow at Top','ultimate_vc') => 'shd-top',
										__('Shadow at Bottom','ultimate_vc') => 'shd-bottom',
										__('Shadow at Left','ultimate_vc') => 'shd-left',
										__('Shadow at Right','ultimate_vc') => 'shd-right',
									),
								//"description" => __("", "smile"),
								"group" => "Shadow"
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Shadow Color","ultimate_vc"),
								"param_name" => "btn_shadow_color",
								"value" => "",
								"description" => "",
								"dependency" => Array("element" => "btn_shadow", "not_empty" => true),
								"group" => "Shadow"
						  	),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Shadow Color on Hover","ultimate_vc"),
								"param_name" => "btn_shadow_color_hover",
								"value" => "",
								"description" => "",
								"dependency" => Array("element" => "btn_shadow", "not_empty" => true),
								"group" => "Shadow"
						  	),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Shadow Size","ultimate_vc"),
								"param_name" => "btn_shadow_size",
								"value" => 5,
								"min" => 0,
								"max" => 100,
								"suffix" => "px",
								"description" => "",
								"dependency" => Array("element" => "btn_shadow", "not_empty" => true),
								"group" => "Shadow"
						  	),
							array(
								"type" => "ult_switch",
								"class" => "",
								"heading" => __("Button Click Effect", "ultimate_vc"),
								"param_name" => "btn_shadow_click",
								"value" => "",
								"options" => array(
										"enable" => array(
											"label" => "",
											"on" => "Yes",
											"off" => "No",
										)
									),
								"description" => __("Enable Click effect on hover", "ultimate_vc"),
								"dependency" => Array("element" => "btn_shadow", "not_empty" => true),
								"group" => "Shadow"
						),
							array(
								"type" => "ultimate_google_fonts",
								"heading" => __("Font Family", "ultimate_vc"),
								"param_name" => "btn_font_family",
								"description" => __("Select the font of your choice.","ultimate_vc")." ".__("You can","ultimate_vc")." <a target='_blank' href='".admin_url('admin.php?page=ultimate-font-manager')."'>".__("add new in the collection here","ultimate_vc")."</a>.",
								"group" => "Typography"
							),
							array(
								"type" => "ultimate_google_fonts_style",
								"heading" 		=>	__("Font Style", "ultimate_vc"),
								"param_name"	=>	"btn_font_style",
								"group" => "Typography"
							),
							array(
								"type" => "number",
								"class" => "font-size",
								"heading" => __("Font Size", "ultimate_vc"),
								"param_name" => "btn_font_size",
								"min" => 14,
								"suffix" => "px",
								"group" => "Typography"
							),
							array(
								"type" => "checkbox",
								"class" => "",
								"heading" => __("Tooltip Options", "ultimate_vc"),
								"param_name" => "enable_tooltip",
								"value" => array("Enable tooltip on button" => "yes"),
								"group" => "Tooltip"
							),
							array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Text", "ultimate_vc"),
								"param_name" => "tooltip_text",
								"value" => "",
								"dependency" => Array("element" => "enable_tooltip", "value" => "yes"),
								"group" => "Tooltip",
							),
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Position", "ultimate_vc"),
								"param_name" => "tooltip_pos",
								"value" => array(
									__("Tooltip from Left","ultimate_vc") => "left",
									__("Tooltip from Right","ultimate_vc") => "right",
									__("Tooltip from Top","ultimate_vc") => "top",
									__("Tooltip from Bottom","ultimate_vc") => "bottom",
								),
								"description" => __("Select the tooltip position","ultimate_vc"),
								"dependency" => Array("element" => "enable_tooltip", "value" => "yes"),
								"group" => "Tooltip",
							),
							array(
								"type" => "heading",
								"sub_heading" => "<span style='display: block;'><a href='http://bsf.io/0n-7p' target='_blank'>".__("Watch Video Tutorial","ultimate_vc")." &nbsp; <span class='dashicons dashicons-video-alt3' style='font-size:30px;vertical-align: middle;color: #e52d27;'></span></a></span>",
								"param_name" => "notification",
								'edit_field_class' => 'ult-param-important-wrapper ult-dashicon ult-align-right ult-bold-font ult-blue-font vc_column vc_col-sm-12',
								"group" => "General"
							),
						)
					)
				);
			}
		}
	}
	new Ultimate_Buttons;
}