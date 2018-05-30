<?php
/**
 * Abstract User Object Class
 * Implements common routines for all user objects
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
abstract class Jax_User_Abstract
{
	/**
	 * The current user id
	 * 
	 * @var string
	 */
	protected $_UserId;
	
	/**
	 * An instance of the cache
	 * 
	 * @var Jax_Cache
	 */
	protected $_Cache;
	
	/**
	 * An instance of the application session namespace
	 * 
	 * @var Zend_Session_Namespace
	 */
	protected $_sess;
	
	/**
	 * The current user object 
	 * 
	 * @var Jax_User_Abstract
	 */
	protected $_userObject;
	
	/**
	 * Stub method.
	 * Modifies the user object returned with other data.
	 * This is necessary to add additional properties to the object before returning to the client.
	 * Concrete classes should override as necessary.
	 * 
	 * @return null
	 */
	protected function _injectUserProperties()
	{
		if (is_object($this->_userObject)){
			// Inject properties here, example:
			//$this->_userObject->Role = Jax_Auth::getAuthLevel();
		}
	}
	
	/**
	 * Can be overridden by concrete classes to load the user object from storage.
	 * 
	 * @return boolean - Indicating if the object was loaded or not.
	 */
	protected function _loadUserObject(){
		$userObject = Jax_User::getInstance()->setUser($this->_UserId)->getUserObject(true,true);

		$this->_userObject = $userObject;
		
		$this->_injectUserProperties();
		
		return true;
	}
	
	/**
	 * Default constructor.
	 * 
	 * Sets up class parameters.
	 */
	public function __construct($userId = false)
	{
		$this->_sess = new Zend_Session_Namespace(APPNAMESPACE);
		
		if($userId == false){
			$this->_UserId = Jax_Auth::getAuthId();
		} else {
			$this->_UserId = (string) $userId;
		}
		
		$this->_Cache = Jax_Cache::getCache();
	}
	
	public function setUserId($userid){
		$this->_UserId = (string) $userid;
		
		return $this;
	}
	
	/**
	 * Loads and modifies (if necessary) the user object.
	 * 
	 * @return Jax_Response
	 */
	public final function getUserObject()
	{
		if(($loadResult = $this->_loadUserObject()) !== true) return $loadResult;
		
		$this->_injectJaxCustomProperties();
		
		return Jax_Response::Valid($this->_userObject);
	}
	
	/**
	 * Renders a user avatar if defined, or default avatar if not.
	 * 
	 * @return null
	 */
	public function getUserAvatar()
	{
		$UserAvatar = new Jax_User_Avatar($this);
		$UserAvatar->render();
	}
	
	/**
	 * Appends data (modifies) the current user object.
	 * 
	 * @return null
	 */
	protected final function _injectJaxCustomProperties()
	{
		$this->_userObject->Theme = Jax_System_Themes::getUserTheme();
	}
	
	/**
	 * Public method for accepting the $request object which is used to
	 * determine what parts of the user object is being requested.
	 * 
	 * @param Zend_Controller_Request_Abstract $request
	 */
	public function handleRequest(Zend_Controller_Request_Abstract $request)
	{
		$this->_loadUserObject();
		$this->_injectJaxCustomProperties();
		
		$UserObject = (Array) $this->_userObject;
		
		return $this->_objectAccess($request, $UserObject);
	}
	
	/**
	 * Analyses the URL and returns the appropriate JSON object parts.
	 * 
	 * @param $request
	 * @param array $object
	 * @return Jax_Response
	 */
	protected function _objectAccess($request,Array $object)
	{
		$request = $request->getParams();
		$URI = $_SERVER['REQUEST_URI'];
		$URI = str_replace(BASEURL.$request['module'].'/'.$request['controller'].'/', '', $URI);
		$URI = str_replace('?'.$_SERVER['QUERY_STRING'], "", $URI);
		$accessPath = explode('/', strtolower($URI));
		
		$s = 0;
		foreach($accessPath as $objectKey)
		{
			if(is_array($object)){
				
				$object = (Array) Jax_Utilities_NormalizeObjectKeys::run($object,"strtolower");
				
				if (array_key_exists($objectKey, $object))
				{
					$object = $object[$objectKey];
					$s++;
				} else {
					break;
				}
			}
		}
		
		if ($s == count($accessPath)) return Jax_Response::Valid($object);
		
		$prop = $this->{$objectKey};
		if(is_null($prop)) return Jax_Response::Valid(null);
	}
	
	/**
	 * Retrieves a property from the user object.
	 * 
	 * @param string $property
	 * @return mixed
	 */
	public function __get($property)
	{
		switch (strtolower($property)) {
			case 'picture':
				$this->getUserAvatar();
				return 1;
				
			default:
				if (is_object($this->_userObject) && @$this->_userObject->$property) 
					return $this->_userObject->$property;
				
				return null;
			break;
		}
	}
}