<?php
if(!class_exists('Ultimate_DateTime_Picker_Param'))
{
	class Ultimate_DateTime_Picker_Param
	{
		function __construct()
		{	
			if(function_exists('add_shortcode_param'))
			{
				add_shortcode_param('datetimepicker' , array($this, 'datetimepicker'));
			}
		}
	
		function datetimepicker($settings, $value)
		{
			$dependency = vc_generate_dependencies_attributes($settings);
			$param_name = isset($settings['param_name']) ? $settings['param_name'] : '';
			$type = isset($settings['type']) ? $settings['type'] : '';
			$class = isset($settings['class']) ? $settings['class'] : '';
			$uni = uniqid('datetimepicker-'.rand());
			$output = '<div id="ult-date-time'.$uni.'" class="ult-datetime"><input data-format="yyyy/MM/dd hh:mm:ss" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" style="width:258px;" value="'.$value.'" '.$dependency.'/><div class="add-on" >  <i data-time-icon="Defaults-time" data-date-icon="Defaults-time"></i></div></div>';
			$output .= '<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery("#ult-date-time'.$uni.'").datetimepicker({
						language: "pt-BR"
					});
				})
				</script>';
			return $output;
		}
		
	}
}

if(class_exists('Ultimate_DateTime_Picker_Param'))
{
	$Ultimate_DateTime_Picker_Param = new Ultimate_DateTime_Picker_Param();
}
