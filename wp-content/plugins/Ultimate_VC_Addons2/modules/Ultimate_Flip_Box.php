<?php
/*
* Add-on Name: Flip Box for Visual Composer
* Add-on URI: http://dev.brainstormforce.com
*/
if(!class_exists('AIO_Flip_Box'))
{
	class AIO_Flip_Box
	{
		function __construct()
		{
			add_action('admin_init',array($this,'block_init'));
			add_shortcode('icon_counter',array($this,'block_shortcode'));
		}
		function block_init()
		{
			if(function_exists('vc_map'))
			{
				vc_map(
					array(
					   "name" => __("Flip Box","ultimate_vc"),
					   "base" => "icon_counter",
					   "class" => "vc_flip_box",
					   "icon" => "vc_icon_block",
					   "category" => "Ultimate VC Addons",
					   "description" => __("Icon, some info &amp; CTA. Flips on hover.","ultimate_vc"),
					   "params" => array(
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Icon to display:", "ultimate_vc"),
								"param_name" => "icon_type",
								"value" => array(
									"Font Icon Manager" => "selector",
									"Custom Image Icon" => "custom",
								),
								"description" => __("Use an existing font icon or upload a custom image.", "ultimate_vc")
							),
							array(
								"type" => "icon_manager",
								"class" => "",
								"heading" => __("Select Icon ","ultimate_vc"),
								"param_name" => "icon",
								"value" => "",
								"description" => __("Click and select icon of your choice. If you can't find the one that suits for your purpose, you can","ultimate_vc")." <a href='admin.php?page=font-icon-Manager' target='_blank'>".__('add new here','ultimate_vc')."</a>.",
								"dependency" => Array("element" => "icon_type","value" => array("selector")),
							),
							array(
								"type" => "attach_image",
								"class" => "",
								"heading" => __("Upload Image Icon:", "ultimate_vc"),
								"param_name" => "icon_img",
								"value" => "",
								"description" => __("Upload the custom image icon.", "ultimate_vc"),
								"dependency" => Array("element" => "icon_type","value" => array("custom")),
							),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Image Width", "ultimate_vc"),
								"param_name" => "img_width",
								"value" => 48,
								"min" => 16,
								"max" => 512,
								"suffix" => "px",
								"description" => __("Provide image width", "ultimate_vc"),
								"dependency" => Array("element" => "icon_type","value" => array("custom")),
							),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Size of Icon", "smile"),
								"param_name" => "icon_size",
								"value" => 32,
								"min" => 12,
								"max" => 72,
								"suffix" => "px",
								"description" => __("How big would you like it?", "ultimate_vc"),
								"dependency" => Array("element" => "icon_type","value" => array("selector")),
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Color", "ultimate_vc"),
								"param_name" => "icon_color",
								"value" => "#333333",
								"description" => __("Give it a nice paint!", "ultimate_vc"),
								"dependency" => Array("element" => "icon_type","value" => array("selector")),						
							),
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Icon Style", "ultimate_vc"),
								"param_name" => "icon_style",
								"value" => array(
									__("Simple","ultimate_vc") => "none",
									__("Circle Background","ultimate_vc") => "circle",
									__("Square Background","ultimate_vc") => "square",
									__("Design your own","ultimate_vc") => "advanced",
								),
								"description" => __("We have given three quick preset if you are in a hurry. Otherwise, create your own with various options.", "ultimate_vc"),
								"dependency" => Array("element" => "icon_type","value" => array("selector")),
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Background Color", "ultimate_vc"),
								"param_name" => "icon_color_bg",
								"value" => "#ffffff",
								"description" => __("Select background color for icon.", "ultimate_vc"),	
								"dependency" => Array("element" => "icon_style", "value" => array("circle","square","advanced")),
							),
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Icon Border Style", "ultimate_vc"),
								"param_name" => "icon_border_style",
								"value" => array(
									"None" => "",
									"Solid" => "solid",
									"Dashed" => "dashed",
									"Dotted" => "dotted",
									"Double" => "double",
									"Inset" => "inset",
									"Outset" => "outset",
								),
								"description" => __("Select the border style for icon.","ultimate_vc"),
								"dependency" => Array("element" => "icon_style", "value" => array("advanced")),
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Border Color", "ultimate_vc"),
								"param_name" => "icon_color_border",
								"value" => "#333333",
								"description" => __("Select border color for icon.", "ultimate_vc"),	
								"dependency" => Array("element" => "icon_border_style", "not_empty" => true),
							),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Border Width", "ultimate_vc"),
								"param_name" => "icon_border_size",
								"value" => 1,
								"min" => 1,
								"max" => 10,
								"suffix" => "px",
								"description" => __("Thickness of the border.", "ultimate_vc"),
								"dependency" => Array("element" => "icon_border_style", "not_empty" => true),
							),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Border Radius", "ultimate_vc"),
								"param_name" => "icon_border_radius",
								"value" => 500,
								"min" => 1,
								"max" => 500,
								"suffix" => "px",
								"description" => __("0 pixel value will create a square border. As you increase the value, the shape convert in circle slowly. (e.g 500 pixels).", "ultimate_vc"),
								"dependency" => Array("element" => "icon_border_style", "not_empty" => true),
							),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Background Size", "smile"),
								"param_name" => "icon_border_spacing",
								"value" => 50,
								"min" => 30,
								"max" => 500,
								"suffix" => "px",
								"description" => __("Spacing from center of the icon till the boundary of border / background", "ultimate_vc"),
								"dependency" => Array("element" => "icon_style", "value" => array("advanced")),
							),
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Flip Box Style", "ultimate_vc"),
								"param_name" => "flip_box_style",
								"value" => array(
									"Simple" => "simple",
									"Advanced" => "advanced",
								),
								"description" => __("Select the border style for icon.","ultimate_vc"),
							),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Size of Box Border", "ultimate_vc"),
								"param_name" => "border_size",
								"value" => 2,
								"min" => 1,
								"max" => 10,
								"suffix" => "px",
								"description" => __("Enter value in pixels.", "ultimate_vc"),
								"dependency" => Array("element" => "flip_box_style", "value" => array("simple")),
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Border Color", "ultimate_vc"),
								"param_name" => "border_color",
								"value" => "#A4A4A4",
								"description" => __("Select the color for border on front.", "ultimate_vc"),
								"dependency" => Array("element" => "flip_box_style", "value" => array("simple")),
							),
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Box Border Style", "ultimate_vc"),
								"param_name" => "box_border_style",
								"value" => array(
									"None"=> "none",
									"Solid"=> "solid",
									"Dashed" => "dashed",
									"Dotted" => "dotted",
									"Double" => "double",
									"Inset" => "inset",
									"Outset" => "outset",
								),
								"description" => __("Select the border style for box.","ultimate_vc"),
								"dependency" => Array("element" => "flip_box_style", "value" => array("advanced")),
							),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Size of Box Border", "ultimate_vc"),
								"param_name" => "box_border_size",
								"value" => 2,
								"min" => 1,
								"max" => 10,
								"suffix" => "px",
								"description" => __("Enter value in pixels.", "ultimate_vc"),
								"dependency" => Array("element" => "box_border_style", "value" => array("solid","dashed","dotted","double","inset","outset")),
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Front Side Border Color", "ultimate_vc"),
								"param_name" => "box_border_color",
								"value" => "#A4A4A4",
								"description" => __("Select the color for border on front.", "ultimate_vc"),
								"dependency" => Array("element" => "box_border_style", "value" => array("solid","dashed","dotted","double","inset","outset")),
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Back Side Border Color", "ultimate_vc"),
								"param_name" => "box_border_color_back",
								"value" => "#A4A4A4",
								"description" => __("Select the color for border on back.", "ultimate_vc"),
								"dependency" => Array("element" => "box_border_style", "value" => array("solid","dashed","dotted","double","inset","outset")),
							),
							array(
								 "type" => "textfield",
								 "class" => "",
								 "heading" => __("Title on Front","ultimate_vc"),
								 "param_name" => "block_title_front",
								 "admin_label" => true,
								 "value" => "",
								 "description" => __("Perhaps, this is the most highlighted text.","ultimate_vc")
							),						  
							array(
								 "type" => "textarea",
								 "class" => "",
								 "heading" => __("Description on Front ","ultimate_vc"),
								 "param_name" => "block_desc_front",
								 "value" => "",
								 "description" => __("Keep it short and simple!","ultimate_vc")
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Text Color", "ultimate_vc"),
								"param_name" => "text_color",
								"value" => "#333333",
								"description" => __("Color of title & description text.", "ultimate_vc"),	
								"dependency" => Array("element" => "flip_box_style", "value" => array("simple")),
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Background Color", "ultimate_vc"),
								"param_name" => "bg_color",
								"value" => "#efefef",
								"description" => __("Light colors look better for background.", "ultimate_vc"),
								"dependency" => Array("element" => "flip_box_style", "value" => array("simple")),
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Front Side Text Color", "ultimate_vc"),
								"param_name" => "block_text_color",
								"value" => "#333333",
								"description" => __("Color of front side title & description text.", "ultimate_vc"),	
								"dependency" => Array("element" => "flip_box_style", "value" => array("advanced")),							
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Front Side Background Color", "ultimate_vc"),
								"param_name" => "block_front_color",
								"value" => "#efefef",
								"description" => __("Light colors look better on front.", "ultimate_vc"),
								"dependency" => Array("element" => "flip_box_style", "value" => array("advanced")),								
							),
							array(
								 "type" => "textfield",
								 "class" => "",
								 "heading" => __("Title on Back ","ultimate_vc"),
								 "param_name" => "block_title_back",
								 "admin_label" => true,
								 "value" => "",
								 "description" => __("Some nice heading for the back side of the flip.","ultimate_vc")
							),
							array(
								 "type" => "textarea",
								 "class" => "",
								 "heading" => __("Description on Back","ultimate_vc"),
								 "param_name" => "block_desc_back",
								 "value" => "",
								 "description" => __("Text here will be followed by a button. So make it catchy!","ultimate_vc")
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Back Side Text Color", "ultimate_vc"),
								"param_name" => "block_back_text_color",
								"value" => "#333333",
								"description" => __("Color of back side title & description text.", "ultimate_vc"),
								"dependency" => Array("element" => "flip_box_style", "value" => array("advanced")),							
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Back Side Background Color", "ultimate_vc"),
								"param_name" => "block_back_color",
								"value" => "#efefef",
								"description" => __("Select the background color for back .", "ultimate_vc"),
								"dependency" => Array("element" => "flip_box_style", "value" => array("advanced")),							
							),
							array(
								 "type" => "dropdown",
								 "class" => "",
								 "heading" => __("Link","ultimate_vc"),
								 "param_name" => "custom_link",
								 "value" => array(
										"No Link" => "",
										"Add custom link with button" => "1",
										),
								 "description" => __("You can add / remove custom link","ultimate_vc")
							),
							array(
								 "type" => "vc_link",
								 "class" => "",
								 "heading" => __("Link ","ultimate_vc"),
								 "param_name" => "button_link",
								 "value" => "",
								 "description" => __("You can link or remove the existing link on the button from here.","ultimate_vc"),
								 "dependency" => Array("element" => "custom_link", "not_empty" => true, "value" => array("1")),
							),
							array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Button Text","ultimate_vc"),
								"param_name" => "button_text",
								"value" => "",
								"description" => __("The \"call to action\" text","ultimate_vc"),
								"dependency" => Array("element" => "custom_link", "not_empty" => true, "value" => array("1")),
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Button background color", "ultimate_vc"),
								"param_name" => "button_bg",
								"value" => "#333333",
								"description" => __("Color of the button. Make sure it'll match with Back Side Box Color.", "ultimate_vc"),
								"dependency" => Array("element" => "custom_link", "not_empty" => true, "value" => array("1")),
							),
							array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Button Text Color", "ultimate_vc"),
								"param_name" => "button_txt",
								"value" => "#FFFFFF",
								"description" => __("Select the color for button text.", "ultimate_vc"),
								"dependency" => Array("element" => "custom_link", "not_empty" => true, "value" => array("1")),
							),
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Flip Type ","ultimate_vc"),
								"param_name" => "flip_type",
								"value" => array(
									__("Flip Horizontally From Left","ultimate_vc") => "horizontal_flip_left",
									__("Flip Horizontally From Right","ultimate_vc") => "horizontal_flip_right",
									__("Flip Vertically From Top","ultimate_vc") => "vertical_flip_top",
									__("Flip Vertically From Bottom","ultimate_vc") => "vertical_flip_bottom",
									__("Vertical Door Flip","ultimate_vc") => "vertical_door_flip",
									__("Reverse Vertical Door Flip","ultimate_vc") => "reverse_vertical_door_flip",
									__("Horizontal Door Flip","ultimate_vc") => "horizontal_door_flip",
									__("Reverse Horizontal Door Flip","ultimate_vc") => "reverse_horizontal_door_flip",
									__("Book Flip (Beta)","ultimate_vc") => "style_9",
									__("Flip From Left (Beta)","ultimate_vc") => "flip_left",
									__("Flip From Right (Beta)","ultimate_vc") => "flip_right",
									__("Flip From Top (Beta)","ultimate_vc") => "flip_top",
									__("Flip From Bottom (Beta)","ultimate_vc") => "flip_bottom",
								),
								"description" => __("Select Flip type for this flip box.","ultimate_vc")
							),
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Set Box Height","ultimate_vc"),
								"param_name" => "height_type",
								"value" => array(
									__("Display full the content and adjust height of the box accordingly","ultimate_vc")=>"ifb-jq-height",
									__("Hide extra content that doesn't fit in height of the box","ultimate_vc") => "ifb-auto-height",								
									__("Give a custom height of your choice to the box","ultimate_vc") => "ifb-custom-height",								
								),
								"description" => __("Select height option for this box.","ultimate_vc")
							),
							array(
								"type" => "number",
								"class" => "",
								"heading" => __("Box Height", "ultimate_vc"),
								"param_name" => "box_height",
								"value" => 300,
								"min" => 200,
								"max" => 1200,
								"suffix" => "px",
								"description" => __("Provide box height", "ultimate_vc"),
								"dependency" => Array("element" => "height_type","value" => array("ifb-custom-height")),
							),
							array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Extra Class", "ultimate_vc"),
								"param_name" => "el_class",
								"value" => "",
								"description" => __("Add extra class name that will be applied to the icon process, and you can use this class for your customizations.", "ultimate_vc"),
							),
						array(
								"type" => "text",
								"param_name" => "ult_param_heading",
								"heading" => __("Title settings","ultimate_vc"),
								"value" => "",
								"class" => "ult-param-heading",
								'edit_field_class' => 'ult-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
								"group" => "Typography"
							),
							array(
								"type" => "ultimate_google_fonts",
								"heading" => __("Font Family","ultimate_vc"),
								"param_name" => "title_font",
								"value" => "",
								"group" => "Typography"
							),
							array(
								"type" => "ultimate_google_fonts_style",
								"heading" => __("Font Style","ultimate_vc"),
								"param_name" => "title_font_style",
								"value" => "",
								"group" => "Typography"
							),
							array(
								"type" => "number",
								"param_name" => "title_font_size",
								"heading" => __("Font size","ultimate_vc"),
								"value" => "",
								"suffix" => "px",
								"min" => 10,
								"group" => "Typography"
							),
							array(
								"type" => "number",
								"param_name" => "title_font_line_height",
								"heading" => __("Font Line Height","ultimate_vc"),
								"value" => "",
								"suffix" => "px",
								"min" => 10,
								"group" => "Typography"
							),
							array(
								"type" => "ult_param_heading",
								"param_name" => "desc_text_typography",
								"heading" => __("Description settings","ultimate_vc"),
								"value" => "",
								"class" => "ult-param-heading",
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
								"group" => "Typography"
							),
							array(
								"type" => "ultimate_google_fonts",
								"heading" => __("Font Family","ultimate_vc"),
								"param_name" => "desc_font",
								"value" => "",
								"group" => "Typography"
							),
							array(
								"type" => "ultimate_google_fonts_style",
								"heading" => __("Font Style","ultimate_vc"),
								"param_name" => "desc_font_style",
								"value" => "",
								"group" => "Typography"
							),
							array(
								"type" => "number",
								"param_name" => "desc_font_size",
								"heading" => __("Font size","ultimate_vc"),
								"value" => "",
								"suffix" => "px",
								"min" => 10,
								"group" => "Typography"
							),
							array(
								"type" => "number",
								"param_name" => "desc_font_line_height",
								"heading" => __("Font Line Height","ultimate_vc"),
								"value" => "",
								"suffix" => "px",
								"min" => 10,
								"group" => "Typography"
							),
							array(
								"type" => "heading",
								"sub_heading" => "<span style='display: block;'><a href='http://bsf.io/1qnl6' target='_blank'>".__("Watch Video Tutorial","ultimate_vc")." &nbsp; <span class='dashicons dashicons-video-alt3' style='font-size:30px;vertical-align: middle;color: #e52d27;'></span></a></span>",
								"param_name" => "notification",
								'edit_field_class' => 'ult-param-important-wrapper ult-dashicon ult-align-right ult-bold-font ult-blue-font vc_column vc_col-sm-12',
							),
						),
					)
				);
			}
		}
		// Shortcode handler function for  icon block
		function block_shortcode($atts)
		{
			$icon_type = $icon_img = $img_width = $icon = $icon_color = $icon_color_bg = $icon_size = $icon_style = $icon_border_style = $icon_border_radius = $icon_color_border = $icon_border_size = $icon_border_spacing = $icon_link = $el_class = $icon_animation = $block_title_front = $block_desc_front = $block_title_back = $block_desc_back = $button_text = $button_link = $block_text_color = $block_front_color = $block_back_color = $block_back_text_color = $animation = $font_size_icon = $box_border_style = $box_border_size = $box_border_color = $border_size = $border_color = $box_border_color_back = $custom_link = $button_bg = $button_txt = $height_type = $box_height = $flip_type = $flip_box_style = $text_color = $bg_color = $front_text = $back_text = '';
			$desc_font_line_height = $title_font_line_height = $title_font=$title_font_style=$title_font_size=$desc_font = $desc_font_style = $desc_font_size = '';
			extract(shortcode_atts( array(
				'icon_type' => '',
				'icon' => '',
				'icon_img' => '',
				'img_width' => '',
				'icon_size' => '',				
				'icon_color' => '',
				'icon_style' => '',
				'icon_color_bg' => '',
				'icon_color_border' => '',			
				'icon_border_style' => '',
				'icon_border_size' => '',
				'icon_border_radius' => '',
				'icon_border_spacing' => '',
				'icon_link' => '',
				'icon_animation' => '',
				'block_title_front' => '',
				'block_desc_front' => '',
				'block_title_back' => '',
				'block_desc_back' =>'',
				'custom_link' => '',
				'button_text' =>'',
				'button_link' =>'',
				'button_bg' => '',
				'button_txt' => '',
				'flip_type' =>'',
				'text_color' => '',
				'bg_color' => '',
				'block_text_color' =>'',
				'block_front_color' =>'',
				'block_back_color' =>'',
				'el_class' =>'',
				'block_back_text_color' =>'',
				'border_size' => '', 
				'border_color' => '', 
				'box_border_style' => '', 
				'box_border_size' => '', 
				'box_border_color' => '', 
				'box_border_color_back' => '',
				'height_type' => '',
				'box_height' => '',
				'flip_box_style' => '',
				'title_font' => '',
				'title_font_style' => '',
				'title_font_size' => '',
				'title_font_line_height'=> '',
				'desc_font' => '',
				'desc_font_style' => '',
				'desc_font_size' => '',
				'desc_font_line_height'=> '',
			),$atts));	
			$output = $f_style = $b_style = $ico_color = $box_border = $icon_border = $link_style = $height = $link_sufix = $link_prefix = $link_style = '';
			$title_style = $desc_style = '';
			//$font_args = array();
			if($icon_type == 'custom'){
				$icon_style = 'none';
			}
			$flip_icon = do_shortcode('[just_icon icon_type="'.$icon_type.'" icon="'.$icon.'" icon_img="'.$icon_img.'" img_width="'.$img_width.'" icon_size="'.$icon_size.'" icon_color="'.$icon_color.'" icon_style="'.$icon_style.'" icon_color_bg="'.$icon_color_bg.'" icon_color_border="'.$icon_color_border.'"  icon_border_style="'.$icon_border_style.'" icon_border_size="'.$icon_border_size.'" icon_border_radius="'.$icon_border_radius.'" icon_border_spacing="'.$icon_border_spacing.'" icon_link="'.$icon_link.'" icon_animation="'.$icon_animation.'"]');
			$css_trans = $icon_border = $box_border = '';
			$height = $target = '';
			
			/* title */
			if($title_font != '')
			{
				$font_family = get_ultimate_font_family($title_font);
				$title_style .= 'font-family:\''.$font_family.'\';';
				//array_push($font_args, $title_font);
			}
			if($title_font_style != '')
				$title_style .= get_ultimate_font_style($title_font_style);
			if($title_font_size != '')
				$title_style .= 'font-size:'.$title_font_size.'px;';
			if($title_font_line_height != '')
				$title_style .= 'line-height:'.$title_font_line_height.'px;';
				
			/* description */
			if($desc_font != '')
			{
				$font_family = get_ultimate_font_family($desc_font);
				$desc_style .= 'font-family:\''.$font_family.'\';';
				//array_push($font_args, $desc_font);
			}
			if($desc_font_style != '')
				$desc_style .= get_ultimate_font_style($desc_font_style);
			if($desc_font_size != '')
				$desc_style .= 'font-size:'.$desc_font_size.'px;';
			if($desc_font_line_height != '')
				$desc_style .= 'line-height:'.$desc_font_line_height.'px;';
			//enquque_ultimate_google_fonts($font_args);
			
			if($icon_border_style !== 'none')
			{
				$icon_border .= 'border-style: '.$icon_border_style.';';
				$icon_border .= 'border-width: '.$icon_border_size.'px;';
			}
			if($height_type == "ifb-custom-height"){
				$height = 'height:'.$box_height.'px;';
				$flip_type .= ' flip-box-custom-height';
			}
			if($flip_box_style !== 'simple'){
				$border_front =  'border-color:'.$box_border_color.';';
				$border_back =  'border-color:'.$box_border_color_back.';';
				if($box_border_style !== 'none')
				{
					$box_border .= 'border-style: '.$box_border_style.';';
					$box_border .= 'border-width: '.$box_border_size.'px;';
				}
				if($animation !== 'none')
				{
					$css_trans = 'data-animation="'.$animation.'" data-animation-delay="03"';
				}
				if($block_text_color != ''){
					$f_style .='color:'.$block_text_color.';';
					$front_text .='color:'.$block_text_color.';';
				}
				if($block_front_color != '')
					$f_style .= 'background:'.$block_front_color.';';
				if($block_back_text_color != ''){
					$b_style .='color:'.$block_back_text_color.';';
					$back_text .='color:'.$block_back_text_color.';';
				}
				if($block_back_color != '')
					$b_style .= 'background:'.$block_back_color.';';
			} else {
				if($text_color != ''){
					$f_style .='color:'.$text_color.';';
					$b_style .='color:'.$text_color.';';
					$front_text = $back_text = 'color:'.$text_color.';';
				}
				if($bg_color != '')
				{
					$f_style .= 'background:'.$bg_color.';';
					$b_style .= 'background:'.$bg_color.';';
				}
				if($border_color != ''){
					$border_front =  'border-color:'.$border_color.';';
					$border_back =  'border-color:'.$border_color.';';
					$box_border = 'border-width: '.$border_size.'px;';
					$box_border .= 'border-style: solid;';
				}
			}
			$output .= '<div class="flip-box-wrap">';
			$output .= '<div class="flip-box '.$height_type.' '.$el_class.' '. $flip_type .'" '.$css_trans.' style="'.$height.'">';
			$output .= '<div class="ifb-flip-box">';
				$output .= '<div class="ifb-face ifb-front" style="'.$f_style.' '.$box_border.' '.$border_front.'">';
						if($icon !== '' || $icon_img !== '')
								$output.='<div class="flip-box-icon">'.$flip_icon.'</div>';
						if($block_title_front!='')
							$output.='<h3 style="'.$front_text.' '.$title_style.'">'.$block_title_front.'</h3>';
						if($block_desc_front!='')
							$output.='<p style="'.$desc_style.'">'.$block_desc_front.'</p>';
					$output.='</div><!-- END .front -->
						<div class="ifb-face ifb-back" style="'.$b_style.' '.$box_border.' '.$border_back.'">';
							if($block_title_back!='')
								$output.='<h3 style="'.$back_text.' '.$title_style.'">'.$block_title_back.'</h3>';
							if($block_desc_back!=''){
								if($button_link !== ''){
									$output .= '<div class="ifb-desc-back">';
								}
								$output.='<p style="'.$desc_style.'" >'.$block_desc_back.'</p>';
								if($button_link !== ''){
									$output .= '</div>';
								}
							}
							if($button_text!== '' && $custom_link){
								$link_prefix = '<div class="flip_link">';
								if($button_bg !== '' && $button_txt !== '')
									$link_style = 'style="background:'.$button_bg.'; color:'.$button_txt.';"';
								if($button_link!== ''){								
									$href = vc_build_link($button_link);
									if(isset($href['target']) && $href['target'] != ''){
										$target = 'target="'.$href['target'].'"';
									}
									$link_prefix .= '<a href = "'.$href['url'].'" '.$target.' '.$link_style.'>';
									$link_sufix .= '</a>';
								}
								$link_sufix .= '</div>';
								$output.=$link_prefix.$button_text.$link_sufix;
							}
						$output.='</div><!-- END .back -->';
					$output .= '</div> <!-- ifb-flip-box -->';
				$output .= '</div> <!-- flip-box -->';
			$output .='</div><!-- End icon block -->';
			return $output;		
		}
	}
	//instantiate the class
	new AIO_Flip_Box;
}