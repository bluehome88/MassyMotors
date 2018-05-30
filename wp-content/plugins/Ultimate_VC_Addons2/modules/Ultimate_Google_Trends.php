<?php
/*
* Add-on Name: Ultimate Google Trends
* Add-on URI: https://www.brainstormforce.com
*/
if(!class_exists("Ultimate_Google_Trends")){
	class Ultimate_Google_Trends{
		function __construct(){
			add_action("admin_init",array($this,"google_trends_init"));
			add_shortcode("ultimate_google_trends",array($this,"display_ultimate_trends"));
		}
		function google_trends_init(){
			if ( function_exists('vc_map'))
			{
				vc_map( array(
					"name" => __("Google Trends", "ultimate_vc"),
					"base" => "ultimate_google_trends",
					"class" => "vc_google_trends",
					"controls" => "full",
					"show_settings_on_create" => true,
					"icon" => "vc_google_trends",
					"description" => __("Display Google Trends to show insights.", "ultimate_vc"),
					"category" => "Ultimate VC Addons",
					"params" => array(
						/*array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Compare", "smile"),
							"param_name" => "search_by",
							"admin_label" => true,
							"value" => array(
								__("Multiple Search Terms", "smile") => "q",
								//__("Search Term at Location", "smile") => "geo",
								//__("Search Term over Time Range", "smile") => "date"
							)
						),*/
						array(
							"type" => "textarea",
							"class" => "",
							"heading" => __("Comparison Terms", "ultimate_vc"),
							"param_name" => "gtrend_query",
							"value" => "",
							"description" => __("Enter maximum 5 terms separated by comma. Example:","ultimate_vc")." <em>".__("Google, Facebook, LinkedIn","ultimate_vc")."</em>",	
							"dependency" => Array("element" => "search_by","value" => array("q")),				
						),
						/*array(
							"type" => "textfield",
							"class" => "",
							"heading" => __("Comparison Term", "smile"),
							"param_name" => "gtrend_query_2",
							"value" => "",
							"description" => __("Enter single term. Example: <em>Love</em>", "smile"),	
							"dependency" => Array("element" => "search_by","value" => array("geo","date")),				
						),*/
						array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Location", "ultimate_vc"),
							"param_name" => "location_by",
							"admin_label" => true,
							"value" => array(
								__("Worldwide", "ultimate_vc") => "", 
								__("Argentina", "ultimate_vc") => "", 
								__("Australia", "ultimate_vc") => "",
								__("Austria", "ultimate_vc") => "", 
								__("Bangladesh", "ultimate_vc") => "",
								__("Belgium", "ultimate_vc") => "", 
								__("Brazil", "ultimate_vc") => "",
								__("Bulgaria", "ultimate_vc") => "", 
								__("Canada", "ultimate_vc") => "",
								__("Chile", "ultimate_vc") => "", 
								__("China", "ultimate_vc") => "",
								__("Colombia", "ultimate_vc") => "", 
								__("Costa Rica", "ultimate_vc") => "",
								__("Croatia", "ultimate_vc") => "", 
								__("Czech Republic", "ultimate_vc") => "",
								__("Denmark", "ultimate_vc") => "", 
								__("Dominican Republic", "ultimate_vc") => "",
								__("Ecuador", "ultimate_vc") => "", 
								__("Egypt", "ultimate_vc") => "",
								__("El Salvador", "ultimate_vc") => "", 
								__("Estonia", "ultimate_vc") => "",
								__("Finland", "ultimate_vc") => "", 
								__("France", "ultimate_vc") => "",
								__("Germany", "ultimate_vc") => "", 
								__("Ghana", "ultimate_vc") => "",
								__("Guatemala", "ultimate_vc") => "", 
								__("Honduras", "ultimate_vc") => "",
								__("Hong Kong", "ultimate_vc") => "", 
								__("Hungary", "ultimate_vc") => "",
								__("India", "ultimate_vc") => "IN", 
								__("Indonesia", "ultimate_vc") => "", 
								__("Ireland", "ultimate_vc") => "",
								__("Israel", "ultimate_vc") => "", 
								__("Italy", "ultimate_vc") => "",
								__("Japan", "ultimate_vc") => "", 
								__("Kenya", "ultimate_vc") => "",
								__("Latvia", "ultimate_vc") => "", 
								__("Lithuania", "ultimate_vc") => "",
								__("Malaysia", "ultimate_vc") => "", 
								__("Mexico", "ultimate_vc") => "",
								__("Netherlands", "ultimate_vc") => "", 
								__("New Zealand", "ultimate_vc") => "",
								__("Nigeria", "ultimate_vc") => "", 
								__("Norway", "ultimate_vc") => "",
								__("Pakistan", "ultimate_vc") => "", 
								__("Panama", "ultimate_vc") => "",
								__("Peru", "ultimate_vc") => "", 
								__("Philippines", "ultimate_vc") => "",
								__("Poland", "ultimate_vc") => "", 
								__("Portugal", "ultimate_vc") => "",
								__("Puerto Rico", "ultimate_vc") => "", 
								__("Romania", "ultimate_vc") => "",
								__("Russia", "ultimate_vc") => "", 
								__("Saudi Arabia", "ultimate_vc") => "",
								__("Senegal", "ultimate_vc") => "", 
								__("Serbia", "ultimate_vc") => "",
								__("Singapore", "ultimate_vc") => "", 
								__("Slovakia", "ultimate_vc") => "",
								__("Slovenia", "ultimate_vc") => "", 
								__("South Africa", "ultimate_vc") => "",
								__("South Korea", "ultimate_vc") => "", 
								__("Spain", "ultimate_vc") => "",
								__("Sweden", "ultimate_vc") => "", 
								__("Switzerland", "ultimate_vc") => "",
								__("Taiwan", "ultimate_vc") => "", 
								__("Thailand", "ultimate_vc") => "",
								__("Turkey", "ultimate_vc") => "", 
								__("Uganda", "ultimate_vc") => "",
								__("Ukraine", "ultimate_vc") => "", 
								__("United Arab Emirates", "ultimate_vc") => "",
								__("United Kingdom", "ultimate_vc") => "", 
								__("United States", "ultimate_vc") => "",
								__("Uruguay", "ultimate_vc") => "",
								__("Venezuela", "ultimate_vc") => "",
								__("Vietnam", "ultimate_vc") => "",
							)
						),
						array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Graph type", "ultimate_vc"),
							"param_name" => "graph_type",
							"admin_label" => true,
							"value" => array(__("Interest over time", "ultimate_vc") => "TIMESERIES_GRAPH_0", __("Interest over time with average", "ultimate_vc") => "TIMESERIES_GRAPH_AVERAGES_CHART", __("Regional interest in map", "ultimate_vc") => "GEO_MAP_0_0", __("Regional interest in table", "ultimate_vc") => "GEO_TABLE_0_0", __("Related searches (Topics)", "ultimate_vc") => "TOP_ENTITIES_0_0", __("Related searches (Queries)", "ultimate_vc") => "TOP_QUERIES_0_0"),
							"dependency" => Array("element" => "search_by","value" => array("q"))
						),
						/*array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Graph type", "smile"),
							"param_name" => "graph_type_2",
							"admin_label" => true,
							"value" => array(__("Top Entities", "smile") => "TOP_ENTITIES_0_0", __("Top Queries", "smile") => "TOP_QUERIES_0_0"),
							"dependency" => Array("element" => "search_by","value" => array("geo", "date"))
						),*/
						array(
							"type" => "textfield",
							"class" => "",
							"heading" => __("Frame Width (optional)", "ultimate_vc"),
							"param_name" => "gtrend_width",
							"value" => "",
							"description" => __("For Example: 500", "ultimate_vc")
						),
						array(
							"type" => "textfield",
							"class" => "",
							"heading" => __("Frame Height (optional)", "ultimate_vc"),
							"param_name" => "gtrend_height",
							"value" => "",
							"description" => __("For Example: 350", "ultimate_vc")
						),
						array(
								"type" => "textfield",
								"heading" => __("Extra class name", "ultimate_vc"),
								"param_name" => "el_class",
								"description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "ultimate_vc")
						),
						array(
							"type" => "heading",
							"sub_heading" => "<span style='display: block;'><a href='http://bsf.io/skwuz' target='_blank'>".__("Watch Video Tutorial","ultimate_vc")." &nbsp; <span class='dashicons dashicons-video-alt3' style='font-size:30px;vertical-align: middle;color: #e52d27;'></span></a></span>",
							"param_name" => "notification",
							'edit_field_class' => 'ult-param-important-wrapper ult-dashicon ult-align-right ult-bold-font ult-blue-font vc_column vc_col-sm-12',
						),
					)
				));
			}
		}
		function display_ultimate_trends($atts,$content = null){
			$width = $height = $graph_type = $graph_type_2 = $search_by = $location_by = $gtrend_query = $gtrend_query_2 = $el_class = '';
			extract(shortcode_atts(array(
				//"id" => "map",
				"gtrend_width" => "",
				"gtrend_height" => "",
				"graph_type" => "TIMESERIES_GRAPH_0",
				"graph_type_2" => "",
				"search_by" => "q",
				"location_by" => "",
				"gtrend_query" => "",
				"gtrend_query_2" => "",
				"el_class" => ""
			), $atts));
			if($search_by === 'q')
			{
				$graph_type_new = $graph_type;
				$gtrend_query_new = $gtrend_query;
			}
			else
			{
				$graph_type_new = $graph_type_2;
				$gtrend_query_new = $gtrend_query_2;
			}
			if($gtrend_width != '')
			{
				$width = $gtrend_width;
				$width = '&amp;w='.$width;
			}
			if($gtrend_height != '')
			{
				$height = $gtrend_height;
				$height = '&amp;h='.$height;
			}
			$id = uniqid('vc-trends-');
			$output = '<div id="'.$id.'" class="ultimate-google-trends '.$el_class.'">
				<script type="text/javascript" src="//www.google.com/trends/embed.js?hl=en-US&amp;q='.$gtrend_query_new.'&cmpt='.$search_by.'&amp;geo='.$location_by.'&amp;content=1&amp;cid='.$graph_type_new.'&amp;export=5'.$width.$height.'"></script>
			</div>';
			return $output;
		}
	}
	new Ultimate_Google_Trends;
}