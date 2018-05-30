<?php
/**
 * Used during startup to define some environment constants based on configuration.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_System_Constants
{
	/**
	 * The key that Jax looks for in the ini file for setting up Zend options.
	 * Unchangeable.
	 * 
	 * @var string
	 */
	const CONFIG_PARENT_NAMESPACE = 'app';// DO NOT CHANGE
	
	/**
	 * Name Jax uses internally for the cache.
	 * 
	 * @var string
	 */
	const CONFIG_CACHENAME = 'datacache';
	
	/**
	 * Used internally by Jax to identify system options.
	 * 
	 * @var string
	 */
	const SYSTEM_SESSIONKEY_OPTIONS = 'JaxSystemOptions';
	
	/**
	 * Used internally by Jax to identify the Zend Cache Manager
	 * 
	 * @var string
	 */
	const SYSTEM_SESSIONKEY_CACHEMANAGER = 'JaxSessionCacheManager';
	
	/**
	 * Used internally to determine if the last request received to validate the Jax admin was successful.
	 * 
	 * @var string
	 */
	const SYSTEM_SESSIONKEY_VALIDJAXADMIN = 'ValidJaxAdminSessionKey';
	
	/**
	 * Key used to store the ACL for a valid authenticated session.
	 * @var string
	 */
	const SYSTEM_SESSIONKEY_APPACL = 'AuthSessionACL';
	
	/**
	 * Key used in Zend_Registry to store the application configuration ini file
	 * Set in Bootstrap
	 * @var string
	 */
	const SYSTEM_REGKEY_APPCFG = "AppCFG";
	
	/**
	 * Key used in Zend_Registry to store the application options
	 * 
	 * @var string
	 */
	const SYSTEM_REGKEY_APPOPTS = "AppOptions";
	
	/**
	 * Key used in Zend_Registry to store the bootstrap object
	 * 
	 * @var string
	 */
	 const SYSTEM_REGKEY_BOOTSTRAP = "ZendBootstrapObject";
	
	/**
	 * Initializes system wide named constants.
	 * 
	 * @param Zend_Application $application
	 * @param Jax_Config $config
	 */
	public static function init(Zend_Application $application, Jax_Config $config)
	{	
		$options = $config->getConfig();

		define('APPNAMESPACE', $application->getBootstrap()->getAppNamespace());
		
		$registry = Zend_Registry::getInstance();
		
		$registry->set(self::SYSTEM_REGKEY_BOOTSTRAP, $application->getBootstrap());
		$registry->set(self::SYSTEM_REGKEY_APPOPTS,$application->getOptions());
				
		$session = new Zend_Session_Namespace(APPNAMESPACE);
		
		if (!isset($session->initialized)) {
		    @Zend_Session::regenerateId();
		    $session->initialized = true;
		}
		
		$session->setExpirationSeconds(1800);
		$session->{Jax_System_Constants::SYSTEM_SESSIONKEY_OPTIONS} = $options;

		foreach($options[self::CONFIG_PARENT_NAMESPACE] as $option=>$value)
		{
			define('JAX_OPTIONS_'.strtoupper(self::CONFIG_PARENT_NAMESPACE).'_'.strtoupper($option), $option);
		}
		
		define('JAX_USER_IP', $_SERVER['REMOTE_ADDR']);
	}
	
	/**
	 * Returns the resolved path to layout scripts within a module
	 * 
	 * @return string
	 */
	public static function getApplicationLayoutPath(){
		return APPLICATION_PATH.'/../library/'.APPNAMESPACE.'/_layouts';
	}
}