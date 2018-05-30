<?php
/**
 * Jax Cache. Provides cache support for Jax resources. Uses Zend_Cache internally.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */

class Jax_Cache
{
	/**
	 * Returns an instance of Zend_Cache
	 * 
	 * @return Zend_Cache $cache
	 */
	public static function getCache()
	{
		$session = new Zend_Session_Namespace(APPNAMESPACE);
		return $session->{Jax_System_Constants::SYSTEM_SESSIONKEY_CACHEMANAGER}->getCache(Jax_System_Constants::CONFIG_CACHENAME);
	}
	
	/**
	 * Create a cache id for a cacheable resource.
	 * 
	 * @param array $params
	 * @return string
	 */
	public static function makeCacheId(Array $params){
		return md5(serialize($params));
	}
}