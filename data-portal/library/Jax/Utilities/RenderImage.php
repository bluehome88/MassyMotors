<?php
/**
 * Utility Function - Renders a PNG image. Data passed must be the raw image data.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Utilities_RenderImage implements Jax_Utilities_Interface
{

	public static function run() {
		$im = func_get_arg(0);
		$resize = @func_get_arg(1);
		
		$resizer = false;
		if(is_array($resize)){
			$width = $resize[0];
			$height = $resize[1];
			$resizer = true;
		}
	
		if (is_resource($im))
		{
			header('Content-Type: image/png');
				
			$SimpleImage = new Jax_SimpleImage();
			$SimpleImage->image = $im;
			
			if (isset($_GET['resizeToWidth']))
				$SimpleImage->resizeToWidth(intval($_GET['resizeToWidth']));
				
				
			if($resizer)
				$SimpleImage->resize($width, $height);
				
			$SimpleImage->output(IMAGETYPE_PNG);
		}
		
	}

}