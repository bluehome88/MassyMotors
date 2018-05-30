<?php

if(!class_exists('Ultimate_Border'))
{
  class Ultimate_Border
  {
    function __construct()
    {
      add_action( 'admin_enqueue_scripts', array( $this, 'ultimate_border_param_scripts' ) );
     
      if(function_exists('add_shortcode_param'))
      {
        add_shortcode_param('ultimate_border', array($this, 'ultimate_border_callback'), plugins_url('../admin/vc_extend/js/ultimate-border.js',__FILE__));
      }
    }
    
    function ultimate_border_callback($settings, $value)
    {
        $dependency = vc_generate_dependencies_attributes($settings);
        $positions = $settings['positions'];
        $enable_radius = isset($settings['enable_radius']) ? $settings['enable_radius'] : true ;
        $label = isset($settings['label_border']) ? $settings['label_border'] : 'Border Style';
        $unit = isset($settings['unit']) ? $settings['unit'] : 'px';
        
        $uid = 'ultimate-border-'. rand(1000, 9999); //$settings['param_name'];
        //$uid = uniqid('ultimate-border-'. $settings['param_name'] .'-'. rand());
          $html  = '<div class="ultimate-border" id="'.$uid.'" data-unit="'.$unit.'" >';

          //  BORDER - WIDTH
          $html .= '<div class="ultimate-four-input-section" >';
            foreach($positions as $key => $default_value) {
              switch ($key) {
                case 'Top':
                            $id = 'border-' .strtolower($key). '-width';
                            $dashicon = 'dashicons dashicons-arrow-up-alt';
                            $html .= $this->ultimate_border_param_item($dashicon, /*$mode,*/ $unit, /*$default_value,*/$default_value, $key, $id);
                  break;
                case 'Right':
                            $id = 'border-' .strtolower($key). '-width';
                            $dashicon = 'dashicons dashicons-arrow-right-alt';
                            $html .= $this->ultimate_border_param_item($dashicon, /*$mode,*/ $unit, /*$default_value,*/$default_value, $key, $id);
                  break;
                case 'Bottom':
                            $id = 'border-' .strtolower($key). '-width';
                            $dashicon = 'dashicons dashicons-arrow-down-alt';
                            $html .= $this->ultimate_border_param_item($dashicon, /*$mode,*/ $unit, /*$default_value,*/$default_value, $key, $id);
                  break;
                case 'Left':
                            $id = 'border-' .strtolower($key). '-width';
                            $dashicon = 'dashicons dashicons-arrow-left-alt';
                            $html .= $this->ultimate_border_param_item($dashicon, /*$mode,*/ $unit, /*$default_value,*/$default_value, $key, $id);
                  break;
              }
          }
          $html .= $this->get_units($unit);
          $html .= '</div><!-- .ultimate-four-input-section -->';


          //  BORDER - STYLE
          $html .= '<div class="ultimate-border-style-section">';
          $html .= '    <div class="label">';
          $html .=        $label;
          $html .= '    </div>';
          $html .= '    <div class="ultimate-border-select-block">';
          $html .= '        <select data-placeholder="Border Style" class="ultimate-border-style-selector" >';
          $html .= '            <option value="none">'.__('None','ultimate_vc').'</option>';
          $html .= '            <option value="solid">'.__('Solid','ultimate_vc').'</option>';
          $html .= '            <option value="dashed">'.__('Dashed','ultimate_vc').'</option>';
          $html .= '            <option value="dotted">'.__('Dotted','ultimate_vc').'</option>';
          $html .= '            <option value="double">'.__('Double','ultimate_vc').'</option>';
          $html .= '            <option value="inset">'.__('Inset','ultimate_vc').'</option>';
          $html .= '            <option value="outset">'.__('Outset','ultimate_vc').'</option></select>';
          $html .= '        </select>';
          $html .= '    </div>';
          $html .= '</div>';
                  
        //  Border Radius
        if($enable_radius) :
          $label = "Border Radius";
          if(isset($settings['label_radius']) && $settings['label_radius']!='' ) { $label = $settings['label_radius']; }
          $html .= '<div class="ultimate-border-radius-block" >';
          $html .= '    <div class="label">';
          $html .=        $label;
          $html .= '    </div>';
          
          $radius = $settings['radius'];
            foreach($radius as $key => $default_value) {

              switch ($key) {
                case 'Top Left':      $key = 'top-left-radius';
                                      $dashicon = 'dashicons dashicons-arrow-up-alt';
                                      $placeholder = 'T. Left';
                                      $html .= $this->ultimate_border_radius_item($dashicon, /*$mode,*/ $unit, $default_value, /*$default_value,*//*$default_value,*/ $key, $placeholder);
                        break;
                case 'Top Right':     $key = 'top-right-radius';
                                      $dashicon = 'dashicons dashicons-arrow-right-alt';
                                      $placeholder = 'T. Right';
                                      $html .= $this->ultimate_border_radius_item($dashicon, /*$mode,*/ $unit, $default_value, /*$default_value,*//*$default_value,*/ $key, $placeholder);
                        break;
                case 'Bottom Right':  $key = 'bottom-right-radius';
                                      $dashicon = 'dashicons dashicons-arrow-down-alt';
                                      $placeholder = 'B. Right';
                                      $html .= $this->ultimate_border_radius_item($dashicon, /*$mode,*/ $unit, $default_value, /*$default_value,*//*$default_value,*/ $key, $placeholder);
                        break;
                case 'Bottom Left':   $key = 'bottom-left-radius';
                                      $dashicon = 'dashicons dashicons-arrow-left-alt';
                                      $placeholder = 'B. Left';
                                      $html .= $this->ultimate_border_radius_item($dashicon, /*$mode,*/ $unit, $default_value, /*$default_value,*//*$default_value,*/ $key, $placeholder);
                        break;
              }
            }
          $html .= $this->get_units($unit);
          $html .= '</div>';
        endif;

        //  add color picker
        $label = "Border Color";
        if(isset($settings['label_color']) && $settings['label_color']!='' ) { $label = $settings['label_color']; }
        $html .= '  <div class="ultimate-colorpicker-section">';
        $html .= '    <div class="label">';
        $html .=        $label;
        $html .= '    </div>';
        $html .= '    <div class="ultimate-colorpicker-block">';
        $html .= '      <input name="" class="ultimate-colorpicker vc_color-control" data-id="border-color" type="text" value="" />';
        $html .= '    </div>';
        $html .= '  </div>';
        
        $html .= '  <input type="hidden" data-unit="'.$unit.'" name="'.$settings['param_name'].'" class="wpb_vc_param_value ultimate-border-value '.$settings['param_name'].' '.$settings['type'].'_field" value="'.$value.'" '.$dependency.' />';
        $html .= '</div>';
      return $html;
    }
    function ultimate_border_radius_item($dashicon, /*$mode,*/ $unit,/* $default_value,*/$default_value, $key, $placeholder) {
        $html  = '  <div class="ultimate-border-radius">';
        $html .= '    <span class="ultimate-border-icon">';
        $html .= '      <i class="'.$dashicon.'"></i>';
        $html .= '    </span>';
        $html .= '    <input type="text" class="ultimate-border-inputs ultimate-border-input" data-unit="'.$unit.'" data-default="'.$default_value.'" data-id="border-'.strtolower($key).'" placeholder="'.$placeholder.'" />';
        $html .= '  </div>';
        return $html;
    }
    function ultimate_border_param_item($dashicon, /*$mode,*/ $unit,/* $default_value,*/$default_value, $key, $id) {
        $html  = '  <div class="ultimate-border-input-block">';
        $html .= '    <span class="ultimate-border-icon">';
        $html .= '      <i class="'.$dashicon.'"></i>';
        $html .= '    </span>';
        $html .= '    <input type="text" class="ultimate-border-inputs ultimate-border-input" data-unit="'.$unit.'" data-default="'.$default_value.'" data-id="' .$id. '" placeholder="'.$key.'" />';
        $html .= '  </div>';
        return $html;
    }
    function get_units($unit) {
      //  set units - px, em, %
      $html  = '<div class="ultimate-unit-section">';
      $html .= '  <label>'.$unit.'</label>';
      /*$html .= '  <select data-placeholder="Spacing Unit" class="ultimate-units-selector" >';
      switch($unit) {
            case "px":  $html .= '    <option selected>px</option>';  break;
            case "em":  $html .= '    <option selected>em</option>';  break;
            case "%":   $html .= '    <option selected>%</option>';   break;
      }
      $html .= '      <option value="px">px</option>';
      $html .= '      <option value="em">em</option>';
      $html .= '      <option value="%">%</option>';
      $html .= '  </select>';*/
      $html .= '</div>';
      return $html;
    }
    function ultimate_border_param_scripts() {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style( 'ultimate_border_param_css', plugins_url('../admin/vc_extend/css/ultimate_border.css', __FILE__ ));
            //wp_enqueue_script('ultimate_border_param_js',plugins_url( '../admin/vc_extend/js/param-ultimate-border.js', __FILE__ ));

            wp_enqueue_style( 'ultimate_border_param_choosen_css', plugins_url('../admin/vc_extend/css/chosen.css', __FILE__ ));
            wp_enqueue_script('ultimate_border_param_choosen_js',plugins_url( '../admin/vc_extend/js/chosen.js', __FILE__ ));

    }
  }
}
if(class_exists('Ultimate_Border'))
{
  $Ultimate_Border = new Ultimate_Border();
}