<?php
/*
* Add-on Name: Pricing Tables for Visual Composer
* Add-on URI: http://dev.brainstormforce.com
*/
if(!class_exists("Ultimate_Pricing_Table")){
	class Ultimate_Pricing_Table{
		function __construct(){
			add_action("admin_init",array($this,"ultimate_pricing_init"));
			add_shortcode("ultimate_pricing",array($this,"ultimate_pricing_shortcode"));
		}
		function ultimate_pricing_init(){
			if(function_exists("vc_map")){
				vc_map(
				array(
				   "name" => __("Price Box","ultimate_vc"),
				   "base" => "ultimate_pricing",
				   "class" => "vc_ultimate_pricing",
				   "icon" => "vc_ultimate_pricing",
				   "category" => "Ultimate VC Addons",
				   "description" => __("Create nice looking pricing tables.","ultimate_vc"),
				   "params" => array(
						array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Select Design Style", "ultimate_vc"),
							"param_name" => "design_style",
							"value" => array(
								__("Design 01","ultimate_vc") => "design01",
								__("Design 02","ultimate_vc") => "design02",
								__("Design 03","ultimate_vc") => "design03",
								__("Design 04","ultimate_vc") => "design04",
								__("Design 05","ultimate_vc") => "design05",
								__("Design 06","ultimate_vc") => "design06",
							),
							"description" => __("Select Pricing table design you would like to use", "ultimate_vc")
						),
						array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Select Color Scheme", "ultimate_vc"),
							"param_name" => "color_scheme",
							"value" => array(
								__("Black","ultimate_vc") => "black",
								__("Red","ultimate_vc") => "red",
								__("Blue","ultimate_vc") => "blue",
								__("Yellow","ultimate_vc") => "yellow",
								__("Green","ultimate_vc") => "green",
								__("Gray","ultimate_vc") => "gray",
								__("Design Your Own","ultimate_vc") => "custom",
							),
							"description" => __("Which color scheme would like to use?", "ultimate_vc")
						),
						array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Main background Color", "ultimate_vc"),
							"param_name" => "color_bg_main",
							"value" => "",
							"description" => __("Select normal background color.", "ultimate_vc"),
							"dependency" => Array("element" => "color_scheme","value" => array("custom")),
						),
						array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Main text Color", "ultimate_vc"),
							"param_name" => "color_txt_main",
							"value" => "",
							"description" => __("Select normal background color.", "ultimate_vc"),
							"dependency" => Array("element" => "color_scheme","value" => array("custom")),
						),
						array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Highlight background Color", "ultimate_vc"),
							"param_name" => "color_bg_highlight",
							"value" => "",
							"description" => __("Select highlight background color.", "ultimate_vc"),
							"dependency" => Array("element" => "color_scheme","value" => array("custom")),
						),
						array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Highlight text Color", "ultimate_vc"),
							"param_name" => "color_txt_highlight",
							"value" => "",
							"description" => __("Select highlight background color.", "ultimate_vc"),
							"dependency" => Array("element" => "color_scheme","value" => array("custom")),
						),
						array(
							"type" => "textfield",
							"class" => "",
							"heading" => __("Package Name / Title", "ultimate_vc"),
							"param_name" => "package_heading",
							"admin_label" => true,
							"value" => "",
							"description" => __("Enter the package name or table heading", "ultimate_vc"),
						),
						array(
							"type" => "textfield",
							"class" => "",
							"heading" => __("Sub Heading", "ultimate_vc"),
							"param_name" => "package_sub_heading",
							"value" => "",
							"description" => __("Enter short description for this package", "ultimate_vc"),
						),
						array(
							"type" => "textfield",
							"class" => "",
							"heading" => __("Package Price", "ultimate_vc"),
							"param_name" => "package_price",
							"value" => "",
							"description" => __("Enter the price for this package. e.g. $157", "ultimate_vc"),
						),
						array(
							"type" => "textfield",
							"class" => "",
							"heading" => __("Price Unit", "ultimate_vc"),
							"param_name" => "package_unit",
							"value" => "",
							"description" => __("Enter the price unit for this package. e.g. per month", "ultimate_vc"),
						),
						array(
							"type" => "textarea_html",
							"class" => "",
							"heading" => __("Features", "ultimate_vc"),
							"param_name" => "content",
							"value" => "",
							"description" => __("Create the features list using un-ordered list elements.", "ultimate_vc"),
						),
						array(
							"type" => "textfield",
							"class" => "",
							"heading" => __("Button Text", "ultimate_vc"),
							"param_name" => "package_btn_text",
							"value" => "",
							"description" => __("Enter call to action button text", "ultimate_vc"),
						),
						array(
							"type" => "vc_link",
							"class" => "",
							"heading" => __("Button Link", "smile"),
							"param_name" => "package_link",
							"value" => "",
							"description" => __("Select / enter the link for call to action button", "ultimate_vc"),
						),
						array(
							"type" => "checkbox",
							"class" => "",
							"heading" => "",
							"param_name" => "package_featured",
							"value" => array("Make this pricing box as featured" => "enable"),
						),
						array(
								"type" => "textfield",
								"heading" => __("Extra class name", "js_composer"),
								"param_name" => "el_class",
								"description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "ultimate_vc")
						),
						/* typoraphy - package */
						array(
							"type" => "ult_param_heading",
							"text" => __("Package Name/Title Settings","ultimate_vc"),
							"param_name" => "package_typograpy",
							"group" => "Typography",
							"class" => "ult-param-heading",
							'edit_field_class' => 'ult-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
						),
						array(
							"type" => "ultimate_google_fonts",
							"heading" => __("Font Family", "ultimate_vc"),
							"param_name" => "package_name_font_family",
							"description" => __("Select the font of your choice.","ultimate_vc")." ".__("You can","ultimate_vc")." <a target='_blank' href='".admin_url('admin.php?page=ultimate-font-manager')."'>".__("add new in the collection here","ultimate_vc")."</a>.",
							"group" => "Typography"
						),
						array(
							"type" => "ultimate_google_fonts_style",
							"heading" 		=>	__("Font Style", "ultimate_vc"),
							"param_name"	=>	"package_name_font_style",
							"group" => "Typography"
						),
						array(
							"type" => "number",
							"class" => "font-size",
							"heading" => __("Font Size", "ultimate_vc"),
							"param_name" => "package_name_font_size",
							"min" => 10,
							"suffix" => "px",
							"group" => "Typography"
						),
						array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Font Color", "ultimate_vc"),
							"param_name" => "package_name_font_color",
							"value" => "",
							"group" => "Typography"
						),
						array(
							"type" => "number",
							"class" => "",
							"heading" => __("Line Height", "ultimate_vc"),
							"param_name" => "package_name_line_height",
							"value" => "",
							"suffix" => "px",
							"group" => "Typography"
						),
						/* typoraphy - sub heading */
						array(
							"type" => "ult_param_heading",
							"text" => __("Sub-Heading Settings","ultimate_vc"),
							"param_name" => "subheading_typograpy",
							"group" => "Typography",
							"class" => "ult-param-heading",
							'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
						),
						array(
							"type" => "ultimate_google_fonts",
							"heading" => __("Font Family", "ultimate_vc"),
							"param_name" => "subheading_font_family",
							"description" => __("Select the font of your choice.","ultimate_vc")." ".__("You can","ultimate_vc")." <a target='_blank' href='".admin_url('admin.php?page=ultimate-font-manager')."'>".__("add new in the collection here","ultimate_vc")."</a>.",
							"group" => "Typography"
						),
						array(
							"type" => "ultimate_google_fonts_style",
							"heading" 		=>	__("Font Style", "ultimate_vc"),
							"param_name"	=>	"subheading_font_style",
							"group" => "Typography"
						),
						array(
							"type" => "number",
							"class" => "font-size",
							"heading" => __("Font Size", "ultimate_vc"),
							"param_name" => "subheading_font_size",
							"min" => 10,
							"suffix" => "px",
							"group" => "Typography"
						),
						array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Font Color", "ultimate_vc"),
							"param_name" => "subheading_font_color",
							"value" => "",
							"group" => "Typography"
						),
						array(
							"type" => "number",
							"class" => "",
							"heading" => __("Line Height", "ultimate_vc"),
							"param_name" => "subheading_line_height",
							"value" => "",
							"suffix" => "px",
							"group" => "Typography"
						),
						/* typoraphy - price */
						array(
							"type" => "ult_param_heading",
							"text" => __("Price Settings","ultimate_vc"),
							"param_name" => "price_typograpy",
							"group" => "Typography",
							"class" => "ult-param-heading",
							'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
						),
						array(
							"type" => "ultimate_google_fonts",
							"heading" => __("Font Family", "ultimate_vc"),
							"param_name" => "price_font_family",
							"description" => __("Select the font of your choice.","ultimate_vc")." ".__("You can","ultimate_vc")." <a target='_blank' href='".admin_url('admin.php?page=ultimate-font-manager')."'>".__("add new in the collection here","ultimate_vc")."</a>.",
							"group" => "Typography"
						),
						array(
							"type" => "ultimate_google_fonts_style",
							"heading" 		=>	__("Font Style", "ultimate_vc"),
							"param_name"	=>	"price_font_style",
							"group" => "Typography"
						),
						array(
							"type" => "number",
							"class" => "font-size",
							"heading" => __("Font Size", "ultimate_vc"),
							"param_name" => "price_font_size",
							"min" => 10,
							"suffix" => "px",
							"group" => "Typography"
						),
						array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Font Color", "ultimate_vc"),
							"param_name" => "price_font_color",
							"value" => "",
							"group" => "Typography"
						),
						array(
							"type" => "number",
							"class" => "",
							"heading" => __("Line Height", "ultimate_vc"),
							"param_name" => "price_line_height",
							"value" => "",
							"suffix" => "px",
							"group" => "Typography"
						),
						/* typoraphy - price unit*/
						array(
							"type" => "ult_param_heading",
							"text" => __("Price Unit Settings","ultimate_vc"),
							"param_name" => "price_unit_typograpy",
							"group" => "Typography",
							"class" => "ult-param-heading",
							'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
						),
						array(
							"type" => "ultimate_google_fonts",
							"heading" => __("Font Family", "smile"),
							"param_name" => "price_unit_font_family",
							"description" => __("Select the font of your choice.","ultimate_vc")." ".__("You can","ultimate_vc")." <a target='_blank' href='".admin_url('admin.php?page=ultimate-font-manager')."'>".__("add new in the collection here","ultimate_vc")."</a>.",
							"group" => "Typography"
						),
						array(
							"type" => "ultimate_google_fonts_style",
							"heading" 		=>	__("Font Style", "ultimate_vc"),
							"param_name"	=>	"price_unit_font_style",
							"group" => "Typography"
						),
						array(
							"type" => "number",
							"class" => "font-size",
							"heading" => __("Font Size", "ultimate_vc"),
							"param_name" => "price_unit_font_size",
							"min" => 10,
							"suffix" => "px",
							"group" => "Typography"
						),
						array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Font Color", "ultimate_vc"),
							"param_name" => "price_unit_font_color",
							"value" => "",
							"group" => "Typography"
						),
						array(
							"type" => "number",
							"class" => "",
							"heading" => __("Line Height", "ultimate_vc"),
							"param_name" => "price_unit_line_height",
							"value" => "",
							"suffix" => "px",
							"group" => "Typography"
						),
						/* typoraphy - feature*/
						array(
							"type" => "ult_param_heading",
							"text" => __("Features Settings","ultimate_vc"),
							"param_name" => "features_typograpy",
							"group" => "Typography",
							"class" => "ult-param-heading",
							'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
						),
						array(
							"type" => "ultimate_google_fonts",
							"heading" => __("Font Family", "ultimate_vc"),
							"param_name" => "features_font_family",
							"description" => __("Select the font of your choice.","ultimate_vc")." ".__("You can","ultimate_vc")." <a target='_blank' href='".admin_url('admin.php?page=ultimate-font-manager')."'>".__("add new in the collection here","ultimate_vc")."</a>.",
							"group" => "Typography"
						),
						array(
							"type" => "ultimate_google_fonts_style",
							"heading" 		=>	__("Font Style", "ultimate_vc"),
							"param_name"	=>	"features_font_style",
							"group" => "Typography"
						),
						array(
							"type" => "number",
							"class" => "font-size",
							"heading" => __("Font Size", "ultimate_vc"),
							"param_name" => "features_font_size",
							"min" => 10,
							"suffix" => "px",
							"group" => "Typography"
						),
						array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Font Color", "ultimate_vc"),
							"param_name" => "features_font_color",
							"value" => "",
							"group" => "Typography"
						),
						array(
							"type" => "number",
							"class" => "",
							"heading" => __("Line Height", "ultimate_vc"),
							"param_name" => "features_line_height",
							"value" => "",
							"suffix" => "px",
							"group" => "Typography"
						),
						/* typoraphy - button */
						array(
							"type" => "ult_param_heading",
							"text" => __("Button Settings","ultimate_vc"),
							"param_name" => "button_typograpy",
							"group" => "Typography",
							"class" => "ult-param-heading",
							'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
						),
						array(
							"type" => "ultimate_google_fonts",
							"heading" => __("Font Family", "ultimate_vc"),
							"param_name" => "button_font_family",
							"description" => __("Select the font of your choice.","ultimate_vc")." ".__("You can","ultimate_vc")." <a target='_blank' href='".admin_url('admin.php?page=ultimate-font-manager')."'>".__("add new in the collection here","ultimate_vc")."</a>.",
							"group" => "Typography"
						),
						array(
							"type" => "ultimate_google_fonts_style",
							"heading" 		=>	__("Font Style", "ultimate_vc"),
							"param_name"	=>	"button_font_style",
							"group" => "Typography"
						),
						array(
							"type" => "number",
							"class" => "font-size",
							"heading" => __("Font Size", "ultimate_vc"),
							"param_name" => "button_font_size",
							"min" => 10,
							"suffix" => "px",
							"group" => "Typography"
						),
						array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Font Color", "ultimate_vc"),
							"param_name" => "button_font_color",
							"value" => "",
							"group" => "Typography"
						),
						array(
							"type" => "number",
							"class" => "",
							"heading" => __("Line Height", "ultimate_vc"),
							"param_name" => "button_line_height",
							"value" => "",
							"suffix" => "px",
							"group" => "Typography"
						),
					)// params
				));// vc_map
			}
		}
		function ultimate_pricing_shortcode($atts,$content = null){
			$design_style = '';
			extract(shortcode_atts(array(
				"design_style" => "",
			),$atts));
			$output = '';
			require_once(__ULTIMATE_ROOT__.'/templates/pricing/pricing-'.$design_style.'.php');
			$design_func = 'generate_'.$design_style;
			$design_cls = 'Pricing_'.ucfirst($design_style);
			$class = new $design_cls;
			$output .= $class->generate_design($atts,$content);
			return $output;
		}
	} // class Ultimate_Pricing_Table
	new Ultimate_Pricing_Table;
}