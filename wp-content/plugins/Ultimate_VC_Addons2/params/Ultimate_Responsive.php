<?php
/*

# Param Use - 
	
	array(
		"type" => "ultimate_responsive",
		"unit"  => "px",                                  // use '%' or 'px'
		"media" => array(
			"Large Screen"      => '',
			"Desktop"           => '28',                  // Here '28' is default value set for 'Desktop'
			"Tablet"            => '',
			"Tablet Portrait"   => '',
			"Mobile Landscape"  => '',
			"Mobile"            => '',
		),
	),


# Module implementation - 

	1]  Create Data List -
		$args = array(
        	'target'      =>  '#id .ult-ih-heading',  // set targeted element e.g. unique class/id etc.
           	'media_sizes' => array(
				// set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
               'font-size' => $title_responsive_font_size,
			   'line-height' => $title_responsive_line_height
            ), 
       	);
		$data_list = get_ultimate_vc_responsive_media_css($args);
		
	2] Set responsive class and data list
		<div class='ult-ih-heading ult-responsive' '.$data_list.'  >     // add $data_list to targeted element and "ult-responsive" class
        	....
      	</div>

Note - Without .ult-responsive class on target resposive param will not work

*/

if(!class_exists('Ultimate_Responsive'))
{
	class Ultimate_Responsive
	{
		function __construct()
		{
			add_action( 'admin_enqueue_scripts', array( $this, 'ultimate_admin_responsive_param_scripts' ) );
	
			if(function_exists('add_shortcode_param'))
			{
				add_shortcode_param('ultimate_responsive', array($this, 'ultimate_responsive_callback'), plugins_url('../admin/vc_extend/js/ultimate-responsive.min.js',__FILE__));
			}
		}
	
		function ultimate_responsive_callback($settings, $value)
		{
			$dependency = vc_generate_dependencies_attributes($settings);
			$unit = $settings['unit'];
			$medias = $settings['media'];
			
			$uid = 'ultimate-responsive-'. rand(1000, 9999);
			
			$html  = '<div class="ultimate-responsive-wrapper" id="'.$uid.'" >';
				$html .= '  <div class="ultimate-responsive-items" >';
				foreach($medias as $key => $default_value ) {
					switch ($key) {
						/*case 'Large Screen':  
							$data_id  = strtolower((preg_replace('/\s+/', '_', $key)));
							$class = 'required';
							$dashicon = "<i class='dashicons dashicons-welcome-view-site'></i>";
							$html .= $this->ultimate_responsive_param_media($class, $dashicon, $key, $default_value ,$unit, $data_id);
						break;*/
						case 'Desktop':       
							$class = 'required';
							$data_id  = strtolower((preg_replace('/\s+/', '_', $key)));
							$dashicon = "<i class='dashicons dashicons-desktop'></i>";
							$html .= $this->ultimate_responsive_param_media($class, $dashicon, $key, $default_value ,$unit, $data_id);
							$html .= "<div class='simplify'>
										<div class='ult-tooltip simplify-options'>".__("Responsive Options","ultimate_vc")."</div>
										<i class='simplify-icon dashicons dashicons-arrow-right-alt2'></i>
									  </div>";
						break;
						case 'Tablet':        
							$class = 'optional';
							$data_id  = strtolower((preg_replace('/\s+/', '_', $key)));
							$dashicon = "<i class='dashicons dashicons-tablet' style='transform: rotate(90deg);'></i>";
							$html .= $this->ultimate_responsive_param_media($class, $dashicon, $key, $default_value ,$unit, $data_id);
						break;
						case 'Tablet Portrait':       
							$class = 'optional';
							$data_id  = strtolower((preg_replace('/\s+/', '_', $key)));
							$dashicon = "<i class='dashicons dashicons-tablet'></i>";
							$html .= $this->ultimate_responsive_param_media($class, $dashicon, $key, $default_value ,$unit, $data_id);
						break;
						case 'Mobile Landscape':        
							$class = 'optional';
							$data_id  = strtolower((preg_replace('/\s+/', '_', $key)));
							$dashicon = "<i class='dashicons dashicons-smartphone' style='transform: rotate(90deg);'></i>";
							$html .= $this->ultimate_responsive_param_media($class, $dashicon, $key, $default_value ,$unit, $data_id);
						break;
						case 'Mobile':        
							$class = 'optional';
							$data_id  = strtolower((preg_replace('/\s+/', '_', $key)));
							$dashicon = "<i class='dashicons dashicons-smartphone'></i>";
							$html .= $this->ultimate_responsive_param_media($class, $dashicon, $key, $default_value ,$unit, $data_id);
						break;
					}
				}
			$html .= '  </div>';
			$html .= $this->get_units($unit);
			$html .= '  <input type="hidden" data-unit="'.$unit.'"  name="'.$settings['param_name'].'" class="wpb_vc_param_value ultimate-responsive-value '.$settings['param_name'].' '.$settings['type'].'_field" value="'.$value.'" '.$dependency.' />';
	
			$html .= '</div>';
		
			return $html;
		}
		function ultimate_responsive_param_media($class, $dashicon, $key, $default_value, $unit, $data_id) {
			$tooltipVal  = str_replace('_', ' ', $data_id);
			$html  = '  <div class="ult-responsive-item '.$class.' '.$data_id.' ">';
        	$html .= '    <div class="ult-tooltip '.$class.' '.$data_id.'">'.ucwords($tooltipVal).'</div>';
			$html .= '    <span class="ult-icon">';
			$html .=          $dashicon;
			$html .= '     </span>';
			$html .= '    <input type="text" class="ult-responsive-input" data-default="'.$default_value.'" data-unit="'.$unit.'" data-id="'.$data_id.'" />';
			$html .= '  </div>';
			return $html;
		}
		function get_units($unit) {
			//  set units - px, em, %
			$html  = '<div class="ultimate-unit-section">';
			$html .= '  <label>'.$unit.'</label>';
			$html .= '</div>';
			return $html;
		}
		// admin scripts
		function ultimate_admin_responsive_param_scripts() {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'ultimate_responsive_param_css', plugins_url('../admin/vc_extend/css/ultimate_responsive.min.css', __FILE__ ));
		}
	}
}

if(class_exists('Ultimate_Responsive'))
{
	$Ultimate_Responsive = new Ultimate_Responsive();
}

// return responsive data 
function get_ultimate_vc_responsive_media_css($args) {
	$content = '';
	if(isset($args) && is_array($args)) {
		//  get targeted css class/id from array
		if (array_key_exists('target',$args)) {
			if(!empty($args['target'])) {
				$content .=  " data-ultimate-target='".$args['target']."' ";
			}
		}
	
		//  get media sizes
		if (array_key_exists('media_sizes',$args)) {
			if(!empty($args['media_sizes'])) {
				$content .=  " data-responsive-json-new='".json_encode($args['media_sizes'])."' ";
			}
		}
	}
	return $content;
}
