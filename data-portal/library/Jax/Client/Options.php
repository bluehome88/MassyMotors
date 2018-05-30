<?php
/**
 * Returns configuration options from the project specific ini file and config.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Client_Options
{
	public static function getOptions()
	{
		$session = new Zend_Session_Namespace(APPNAMESPACE);
		$app = $session->{Jax_System_Constants::SYSTEM_SESSIONKEY_OPTIONS}[Jax_System_Constants::CONFIG_PARENT_NAMESPACE];
		
		$options = array(
			Jax_Client_Constants::OPTIONS_BASEURL => BASEURL,
			Jax_Client_Constants::OPTIONS_APPNAME => $app[JAX_OPTIONS_APP_FULLNAME],
			Jax_Client_Constants::OPTIONS_COPYRIGHTS => JAX_VIEW_COPYRIGHTS,
			Jax_Client_Constants::OPTIONS_LOGO => $app[JAX_OPTIONS_APP_LOGO],
			Jax_Client_Constants::OPTIONS_IMAGEPATH => $app[JAX_OPTIONS_APP_IMAGEPATH],
			Jax_Client_Constants::OPTIONS_JSPATH => $app[JAX_OPTIONS_APP_JSPATH],
			Jax_Client_Constants::OPTIONS_CSSPATH => $app[JAX_OPTIONS_APP_CSSPATH],
			
		);
		
		return Jax_Response::Valid($options);
	}
}