<?php
/**
 * Utility Function - Used to verify ACL access within resources
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Utilities_ResourceAccessChecker implements Jax_Utilities_Interface
{	
	public static function run()
	{
		$params = func_get_args();
		$resourceId = $params[0];
		$access = $params[1];
		
		if (!Jax_Acl::getInstance()->acl()
				->isAllowed(
						Jax_Auth::getAuthId(),
						$resourceId,
						$access)
		){
			throw new Exception("RAC - No Access ($resourceId)");
		}
	}
}