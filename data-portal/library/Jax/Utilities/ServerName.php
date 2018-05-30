<?php
/**
 * Utility Function
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Utilities_ServerName implements Jax_Utilities_Interface
{
	public static function run()
	{
		$port = self::getServerPort();
		return (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$port : "http://".$_SERVER['SERVER_NAME'].$port;
	}
	
	public static function getServerPort()
	{
		return !(empty($_SERVER['SERVER_PORT'])) ? ":".$_SERVER['SERVER_PORT'] : "";
	}
}