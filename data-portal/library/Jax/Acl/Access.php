<?php
/**
 * Used to query the ACL and return a list of resources based on the requested access type.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Acl_Access
{
	/**
	 * Instance of the ACL class
	 * 
	 * @var Jax_Acl
	 */
	private $_JaxAcl;
	
	/**
	 * Instance of Zend_Session_Namespace for the application
	 * 
	 * @var Zend_Session_Namespace
	 */
	private $_session;
	
	/**
	 * Resource hierarchy for all resources in the ACL.
	 * This is automatically generated based on parent->child dependencies between resources as defined in the *_Acl_Config class.
	 * 
	 * @var array
	 */
	private $_resourceHierarchy;
	
	/**
	 * Resolved list of allowed resources.
	 * 
	 * @var array
	 */
	private $_allowed = array();
	
	/**
	 * This key represents the requested access type in a resource request.
	 * 
	 * @var string
	 */
	const REQUEST_KEY_ACCESS = 'access';
	
	/**
	 * This key indicates to resolve the request on the resource class. Its value would be the resource method to call.
	 * 
	 * @var string
	 */
	const REQUEST_KEY_GET = 'get';
	
	/**
	 * This key indicates to resolve the request by forwarding to an underlying data access class.
	 * Its value would be the method to call on the data class.
	 * 
	 * @var string
	 */
	const REQUEST_KEY_METHOD = 'method';
	
	/**
	 * This key value represents the key from the resource config object to retrieve.
	 * 
	 * @var string
	 */
	const REQUEST_KEY_CFGKEY = 'key';
	
	const GET_CONFIG = 'config';
	
	/**
	 * The list of modules is return under this key in the response object
	 * 
	 * @var string 
	 */
	const RESPONSE_KEY_RESOURCES = 'modules';
	
	/**
	 * Default constructor. Instantiates and assigns Acl and Session classes and resolves the resource hierarchy.
	 */
	public function __construct()
	{
		$this->_JaxAcl = Jax_Acl::getInstance();
		$this->_session = new Zend_Session_Namespace(APPNAMESPACE);
		$this->_resourceHierarchy = $this->_JaxAcl->getResourceHierarchy();
	}
	
	/**
	 * Recursive method used to resolve a list of allowed resources in the Acl.
	 * 
	 * @param string $Access - The type of access requested. (Valid types are defined in the Acl config)
	 * @param array $branch - Reference pointer (used during resolution of allowed resources)
	 * @return array
	 */
	public function getAllowedResources($Access = null,&$branch = null)
	{
		if (!is_array($branch)) $branch = &$this->_resourceHierarchy;
		
    	foreach ($branch as $key=>&$value)
    	{
    		if (array_key_exists('children',$value))
    		{
    			$this->getAllowedResources($Access,$value['children']);
    		}
    		
    		/*
    		 * Added 1211160125 
    		 * Refreshes the stored resource config before returning.
    		 * If resource classes override getConfig() method, this returns correct data since
    		 * the resource config may change based on the state of the application.
    		 */ 
    		$branch[$key]['config'] = (object) Jax_Acl::getInstance()->acl()->get($key)->getConfig();
    		//
    		
    		/*
    		 * Changed verifying acl from using role to using id of user
    		 * 
    		 * OLD: if (!$this->_JaxAcl->acl()->isAllowed($this->_session->{Jax_Auth_Constants::SESS_KEY_AUTH_LEVEL},$key,$Access))
    		 * NEW: if (!$this->_JaxAcl->acl()->isAllowed(Jax_Auth::getAuthId(),$key,$Access))
    		 */
    		if($this->_JaxAcl->acl()->hasRole(Jax_Auth::getAuthId())){
    			$authUsing = Jax_Auth::getAuthId();
    		} else {
    			$authUsing = Jax_Auth::getAuthLevel();
    		}
    		
	    	if (!$this->_JaxAcl->acl()->isAllowed($authUsing,$key,$Access))
	    	{
	    		// Added 130606-0036
	    		// Before removing branch, move children to main hierarchy. Children may be allowed if permission is explicitly set.
	    		if(@$branch[$key]['children']) {
	    			foreach($branch[$key]['children'] as $k=>&$sb){
	    				$this->_resourceHierarchy[$k] = $sb;
	    			}
	    		}
	    		// end 130606-0036
	    		unset($branch[$key]);
	    	}
    		
    	}
    	return $this->_resourceHierarchy;
	}
}