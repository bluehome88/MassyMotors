<?php
/**
 * Jax File MimeTypes
 * Provides some mimetype translations.
 *
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_File_MimeTypes {
	protected static $_mimeList = array(
		"docx"=>"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
		"docm"=>"application/vnd.ms-word.document.macroEnabled.12",
		"dotx"=>"application/vnd.openxmlformats-officedocument.wordprocessingml.template",
		"dotm"=>"application/vnd.ms-word.template.macroEnabled.12",
		"xlsx"=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
		"xlsm"=>"application/vnd.ms-excel.sheet.macroEnabled.12",
		"xltx"=>"application/vnd.openxmlformats-officedocument.spreadsheetml.template",
		"xltm"=>"application/vnd.ms-excel.template.macroEnabled.12",
		"xlsb"=>"application/vnd.ms-excel.sheet.binary.macroEnabled.12",
		"xlam"=>"application/vnd.ms-excel.addin.macroEnabled.12",
		"pptx"=>"application/vnd.openxmlformats-officedocument.presentationml.presentation",
		"pptm"=>"application/vnd.ms-powerpoint.presentation.macroEnabled.12",
		"ppsx"=>"application/vnd.openxmlformats-officedocument.presentationml.slideshow",
		"ppsm"=>"application/vnd.ms-powerpoint.slideshow.macroEnabled.12",
		"potx"=>"application/vnd.openxmlformats-officedocument.presentationml.template",
		"potm"=>"application/vnd.ms-powerpoint.template.macroEnabled.12",
		"ppam"=>"application/vnd.ms-powerpoint.addin.macroEnabled.12",
		"sldx"=>"application/vnd.openxmlformats-officedocument.presentationml.slide",
		"sldm"=>"application/vnd.ms-powerpoint.slide.macroEnabled.12",
		"one"=>"application/msonenote",
		"onetoc2"=>"application/msonenote",
		"onetmp"=>"application/msonenote",
		"onepkg"=>"application/msonenote",
		"thmx"=>"application/vnd.ms-officetheme"
	);
	
	public static function extensionToMime($ext){
		if(array_key_exists($ext, self::$_mimeList)){
			return self::$_mimeList[$ext];
		}
		return null;
	}
}