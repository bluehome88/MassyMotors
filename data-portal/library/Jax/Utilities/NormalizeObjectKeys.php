<?php
/**
 * Utility Function - Normalizes the keys of an object
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Utilities_NormalizeObjectKeys implements Jax_Utilities_Interface
{
	public static function run()
	{
		$params = func_get_args();
		$Object = $params[0];
		$fcn = $params[1];
		
		if (is_object($Object)) $Object = (Array) $Object;
		
		$normObject = array();
		
		array_walk($Object, function($value,$key,$params){
			$normObject = &$params[0];
			$fcn = $params[1];
			$normObject[$fcn(strtolower($key))] = $value;
		},array(&$normObject,$fcn));
		
		return (Object) $normObject;
	}
}