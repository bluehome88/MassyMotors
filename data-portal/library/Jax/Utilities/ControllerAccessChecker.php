<?php
/**
 * Utility Function - Used to verify ACL access within Controllers
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Utilities_ControllerAccessChecker implements Jax_Utilities_Interface
{	
	public static function run()
	{
		$params = func_get_args();
		$resourceId = $params[0];
		$access = $params[1];
		$handler = new Jax_Response_Handler();
		$handler->setResponseType(Jax_Response_Handler::JSON);
		
		if (!Jax_Acl::getInstance()->acl()
				->isAllowed(
						Jax_Auth::getAuthId(),
						$resourceId,
						$access)
		){
			$handler->setResponseData(Jax_Response::Error("No Access (".__CLASS__.")"))->send();
			die();
		}
	}
}