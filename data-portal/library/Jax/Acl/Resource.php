<?php
/**
 * Abstract Jax ACL Resource Class
 * 
 * All project specific resources should extend this class.
 * Provides the capability for RESTful access to concrete resource data.
 * Provides ability to invoke the incoming request on the underlying Jax_Data_Source_Abstract object
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
abstract class Jax_Acl_Resource extends Zend_Acl_Resource
{
	/**
	 * The resource configuration
	 * 
	 * @var array
	 */
	protected $_config = array();
	
	/**
	 * The data source object (if needed)
	 * 
	 * @var Jax_Data_Source
	 */
	protected $_dataAccessObject;
	
	/**
	 * Instance of Zend_Session_Namespace for the application
	 * 
	 * @var Zend_Session_Namespace
	 */
	protected $_sess;
	
	/**
	 * User role
	 * 
	 * @var string
	 */
	protected $_userContext;
	
	/**
	 * Class constants used internally
	 * 
	 * @var string
	 */
	const DISPLAY_NAME = 'display_name';
	const CONFIG_DATA = 'data';
	const DATA_NOTES = 'resource_note';
	const ICON = 'icon';
	
	/**
	 * Default constructor
	 * 
	 * @param string $resourceId
	 */
	public function __construct($resourceId = NULL)
	{
		$this->_sess = new Zend_Session_Namespace(APPNAMESPACE);
	}
	
	/**
	 * Handles or passes incoming requests to the resource.
	 * 
	 * @param Zend_Controller_Request_Abstract $request
	 * @return Jax_Response
	 */
	public function handleRequest(Zend_Controller_Request_Abstract $request){
		
		// User role
		$this->_userContext = Jax_Auth::getAuthLevel();
		
		// Get request params
		$params = $request->getParams();
		if (!isset($params[Jax_Acl_Access::REQUEST_KEY_ACCESS]))
		{
			return Jax_Response::Error('No access specified');
		} 
		
		// Parse request
		else {
			$access = $params[Jax_Acl_Access::REQUEST_KEY_ACCESS];
			
			$ACL = Jax_Acl::getInstance()->acl();
			
			if ($ACL->isAllowed(Jax_Auth::getAuthId(),$this,$access)) {

				if (isset($params[Jax_Acl_Access::REQUEST_KEY_GET]))
				{
					$getRequest = $params[Jax_Acl_Access::REQUEST_KEY_GET];
					
					switch ($getRequest)
					{
						// Returns the resource configuration
						case Jax_Acl_Access::GET_CONFIG:
							return $this->_objectAccess($request,$this->_config);
							break;
							
						// Default behaviour is to have the concrete class handle the request.
						default:
							return $this->_handleRequest($request);
							break;
					}
					
				} 
								
				else {
					// Delegate to concrete class
					return $this->_handleRequest($request);
				}
				
			} else {
				return Jax_Response::Error('No access ('.__CLASS__.')');
			}
		}
		
	}
	
	/**
	 * Sets the class for data access
	 * 
	 * @param Jax_Data_Source $dataSourceObject
	 * @return Jax_Acl_Resource
	 */
	public final function setDataAccessObject(Jax_Data_Source $dataSourceObject)
	{
		$this->_dataAccessObject = $dataSourceObject;
		return $this;
	}
	
	/**
	 * Getter method - Retrieves the resource configuration
	 * 
	 * @return array
	 */
	public function getConfig()
	{
		return $this->_config;
	}
	
	/**
	 * Setter method - Sets the resource config
	 * 
	 * @param array $config
	 * @return Jax_Acl_Resource
	 */
	public final function setConfig($config = array())
	{
		$this->_config = $config;
		return $this;
	}
	
	/**
	 * Provides the ability to attached extra data to resources.
	 * An example usage is attaching Notes to resources in the AkiSIS project.
	 * 
	 * @param String $key
	 * @param mixed $data
	 * @throws Exception
	 * @return Jax_Acl_Resource
	 */
	public final function addResourceDataItem($key,$data = null)
	{
		if ($key == NULL) throw new Exception('Missing Parameter $key');

		$this->_config[self::CONFIG_DATA][$key] = $data;
		
		return $this;
	}
	
	/**
	 * Delegate the request to the underlying data object class.
	 * The data class should either implement a method matching the requested method name
	 * or dynamically handle the request. (magic method __call)
	 * 
	 * @param Zend_Controller_Request_Abstract $request
	 * @return Jax_Response
	 */
	protected function _invokeDataObject(Zend_Controller_Request_Abstract $request)
	{		
		// No data object set.
		if (!$this->_dataAccessObject)
		{
			return Jax_Response::Valid(NULL);
		}
		
		$params = $request->getParams();
		$method = $params[Jax_Acl_Access::REQUEST_KEY_METHOD];
		
		$args = $this->_buildMethodArgs($request);
		
		$response = call_user_func_array(array($this->_dataAccessObject,$method), $args);

		if ($response instanceof Exception)
		{
			return Jax_Response::Error($response->getMessage());
		} else {
			return $this->_objectAccess($request, (array) $response);
		}
	}
	
	/**
	 * Provides RESTful access capablity to resouces data.
	 * 
	 * @param Zend_Controller_Request_Abstract $request
	 * @param array $object
	 * @return Jax_Response
	 */
	protected function _objectAccess($request,$object)
	{
		$object = (array) $object;
		$request = $request->getParams();
		$URI = $_SERVER['REQUEST_URI'];
		
		$bUri = BASEURL.$request['module'].'/'
				.$request['controller'].'/'
				.$request['action'].'/'
				.Jax_Acl_Access::REQUEST_KEY_ACCESS.'/'
				.$request[Jax_Acl_Access::REQUEST_KEY_ACCESS].'/'
				.@Jax_Acl_Access::REQUEST_KEY_GET.'/'
				.@$request[Jax_Acl_Access::REQUEST_KEY_GET];
		
		$URI = str_replace($bUri, '', $URI);
		
		$accessPath = explode('/', $URI);
		
		// Allows user to disable object access via URL
		if($accessPath[0] == 'disable') return Jax_Response::Valid($object);
		
		foreach($accessPath as $objectKey)
		{
			if(is_array($object))
				if (array_key_exists($objectKey, $object))
				{
					$object = $object[$objectKey];
				}
		}
		return Jax_Response::Valid($object);
	}

	/**
	 * Should return an array of arguments to pass to the requested method of the data object.
	 * 
	 * @param Zend_Controller_Request_Abstract $request
	 * @return array
	 */
	protected function _buildMethodArgs(Zend_Controller_Request_Abstract $request){
		$params = $request->getParams();
		ksort($params);
		
		$args = array();
		
		foreach ($params as $key=>$value)
		{
			$key = strtolower($key);
			if (substr($key, 0,3) == 'arg'){
				$args[] = $value;
			}
		}
		
		return $args;
	}
	
	/**
	 * Default is to forward the request to the underlying data object (if any)
	 * Concrete classes can override to provide custom handlers and/or use this as a fallback handler.
	 * 
	 * @param Zend_Controller_Request_Abstract $request
	 * @return Jax_Response
	 */	
	protected function _handleRequest(Zend_Controller_Request_Abstract $request){
		$params = $request->getParams();
		
		if (isset($params[Jax_Acl_Access::REQUEST_KEY_METHOD])){
		
			// Checks for a Service level access, else denies access to data object
			if($params[Jax_Acl_Access::REQUEST_KEY_ACCESS] !== Jax_Acl_Config::ACL_ACCESS_LEVEL_SERVICE)
				return Jax_Response::Error('No access!');
			
			// Forwards the request to the underlying data access object for handling
			if (isset($params[Jax_Acl_Access::REQUEST_KEY_METHOD]))
			{
				$response = $this->_invokeDataObject($request);
				if (array_key_exists(Jax_Response::KEY_RESPONSE, $response) && is_array($response[Jax_Response::KEY_RESPONSE])){
					return $this->_objectAccess($request, $response[Jax_Response::KEY_RESPONSE]);
				} else {
					return $response;
				}
			}
			
		} 
		else {
			if (isset($params[Jax_Acl_Access::REQUEST_KEY_GET]))
			{
				$method = $params[Jax_Acl_Access::REQUEST_KEY_GET];
				
				if(!method_exists($this, $method.'Proc')) return Jax_Response::Error('Invalid Request. Unable to invoke Resource Method. ('.__LINE__.')');
				
				$response = @call_user_func_array(array($this,$method.'Proc'), array($request));
				
				if (@array_key_exists(Jax_Response::KEY_RESPONSE, $response) && is_array($response[Jax_Response::KEY_RESPONSE])){
					return $this->_objectAccess($request, $response[Jax_Response::KEY_RESPONSE]);
				} else {
					if($response)
					return $response;
				}
				
				return Jax_Response::Error('Invalid Request. Unable to invoke Resource Method. ('.__LINE__.')');
			}
		}
	}
	
	/**
	 * Returns the requested parameter from the config array (if exists)
	 * @param string $name
	 * @return mixed - The returned config data.
	 */
	public function __get($name)
	{
		if (array_key_exists($name, $this->_config)) return $this->_config[$name];
		return null;
	}
}