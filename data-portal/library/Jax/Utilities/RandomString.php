<?php
/**
 * Utility Function - Generate a random string
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Utilities_RandomString implements Jax_Utilities_Interface
{	
	public static function run()
	{
		$params = func_get_args();
		$len = $params[0];
		
		if(intval($len) < 1) $len = 7;
		$str = "";
		
		$chars = array_merge(range(0,9), range('a', 'z'));
		while(strlen($str) < $len){
			$str .= $chars[array_rand($chars)];
		}
		
		return $str;
	}
}