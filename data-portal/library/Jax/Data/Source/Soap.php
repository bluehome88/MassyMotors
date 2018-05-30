<?php
/**
 * SOAP data source class.
 * Developers can extend this class to tweak how the soap client is created. (if needed)
 * (See Aki_Data_Source_Soap, if available)
 *  
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Data_Source_Soap extends Jax_Data_Source_Abstract
{
	/**
	 * Creates a soap client.
	 * 
	 * @param string $wsdl
	 * @throws Exception - if no wsdl location is passed
	 */
	protected function _createSoapClient($wsdl = null){
		if (!is_null($wsdl))
		{
			// Create and return the SOAP client
			return $client = new Zend_Soap_Client($wsdl);
		}
		
		throw new Exception('No SOAP interface passed!');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Jax_Data_Source_Abstract::__call()
	 */
	public function __call($name,$args)
	{		
		if(@$args[1] === true)
		{
			// Load from cach if indicated. If cache is invalid, data is loaded from remote automatically.
			return $this->_loadFromCache($name,$args);
		} else {
			// Load from remote
			return $this->_loadFromRemote($name, $args);
		}
	}
	
	/**
	 * Load data from the cache.
	 * 
	 * @param string $name - Requested method
	 * @param array $args - Array of parameters to pass to the method
	 */
	protected function _loadFromCache($name,$args)
	{
		// Generates the cache id
		$cacheId = $this->_genCacheId($name, $args);
		if ($cacheId == null) return null;

		// Fetches the cache class
		$cache = Jax_Cache::getCache();
		
		// Invalidate the cacheId, forcing data to be fetched from remote
		if ($this->_forceRefresh === true){
			$cache->remove($cacheId);
		}
		
		// If the cache is invalid, load from remote (re-validates the cache)
		if(!$cache->test($cacheId))
		{
			$result = $this->_loadFromRemote($name, $args);
			if ((!$result instanceof Exception)) $cache->save($result,$cacheId);
			return $result;
		} else {
			
			// Returns data from the cache
			return $cache->load($cacheId);
		}
	}
	
	/**
	 * Load data from remote web service
	 * 
	 * @param string $name - Requested method
	 * @param array $args - Array of parameters to pass to the method
	 */
	protected function _loadFromRemote($name,$args)
	{
		$wsdl = @$args[0];
		$soapArgs = array();
		
		for ($a = 2;$a < count($args);$a++)
		{
			$soapArgs[] = $args[$a];
		}
		
		// Create the SOAP client
		$soapClient = $this->_createSoapClient($wsdl);
		if (!($soapClient instanceof Zend_Soap_Client)) return $soapClient;

		// Fetch remote data.
		return @call_user_func_array(array($soapClient,$name), $soapArgs);
	}
	
	protected function _genCacheId($name,$args){
		return $name.'_'.md5(serialize($args));
	}
}