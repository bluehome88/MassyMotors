<?php
if(!class_exists('Ultimate_Switch_Param'))
{
	class Ultimate_Switch_Param
	{
		function __construct()
		{	
			if(function_exists('add_shortcode_param'))
			{
				add_shortcode_param('ult_switch' , array($this, 'checkbox_param'));
			}
		}
	
		function checkbox_param($settings, $value){
			$dependency = vc_generate_dependencies_attributes($settings);
			$param_name = isset($settings['param_name']) ? $settings['param_name'] : '';
			$type = isset($settings['type']) ? $settings['type'] : '';
			$options = isset($settings['options']) ? $settings['options'] : '';
			$class = isset($settings['class']) ? $settings['class'] : '';
			$output = $checked = '';
			$un = uniqid('ultswitch-'.rand());
			if(is_array($options) && !empty($options)){
				foreach($options as $key => $opts){
					if($value == $key){
						$checked = "checked";
					} else {
						$checked = "";
					}
					$uid = uniqid('ultswitchparam-'.rand());
					$output .= '<div class="onoffswitch">
							<input type="checkbox" name="'.$param_name.'" value="'.$value.'" '.$dependency.' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . ' '.$dependency.' onoffswitch-checkbox chk-switch-'.$un.'" id="switch'.$uid.'" '.$checked.'>
							<label class="onoffswitch-label" for="switch'.$uid.'">
								<div class="onoffswitch-inner">
									<div class="onoffswitch-active">
										<div class="onoffswitch-switch">'.$opts['on'].'</div>
									</div>
									<div class="onoffswitch-inactive">
										<div class="onoffswitch-switch">'.$opts['off'].'</div>
									</div>
								</div>
							</label>
						</div>';
						if(isset($opts['label']))
							$lbl = $opts['label'];
						else
							$lbl = '';
					$output .= '<div class="chk-label">'.$lbl.'</div><br/>';
				}
			}
			
			//$output .= '<input type="hidden" id="chk-switch-'.$un.'" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="'.$value.'" />';
			$output .= '<script type="text/javascript">
				jQuery("#switch'.$uid.'").change(function(){
					 
					 if(jQuery("#switch'.$uid.'").is(":checked")){
						jQuery("#switch'.$uid.'").val("'.$key.'");
						jQuery("#switch'.$uid.'").attr("checked","checked");
					 } else {
						jQuery("#switch'.$uid.'").val("off");
						jQuery("#switch'.$uid.'").removeAttr("checked");
					 }
					
				});
			</script>';
			
			return $output;
		}
		
	}
}

if(class_exists('Ultimate_Switch_Param'))
{
	$Ultimate_Switch_Param = new Ultimate_Switch_Param();
}
