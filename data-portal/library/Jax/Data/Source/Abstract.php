<?php
/**
 * Abstract Data Source Class. All data source classes must extend this class.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
abstract class Jax_Data_Source_Abstract
{
	/**
	 * Indicator to notify the concrete data source class if to refresh the request from the
	 * source. Default is to read from the cache (if the cache is valid)
	 * 
	 * @var boolean
	 */
	protected $_forceRefresh;
	
	/**
	 * forceRefresh indicator setter. Used to set the _forceRefresh flag to true.
	 * 
	 * @return Jax_Data_Source_Abstract
	 */
	public function forceRefresh(){
		$this->_forceRefresh = true;
		
		return $this;
	}
	
	/**
	 * Stub method. Concrete classes should implement.
	 * Used retrieve user data from concrete data source classes.
	 * 
	 * @param userId - The ACL role id of the user to be retrieved.
	 * @return array|boolean false
	 */
	protected function getUserData($userId){return array();}
	
	/**
	 * Stub method. Concrete classes should implement.
	 * Used retrieve roles for ACL from data source classes.
	 * Returned array elements should be string or instance of Zend_Acl_Role
	 * 
	 * @return array
	 */
	protected function getRoles(){return array();}
	
	/**
	 * Stub method. Concrete classes should implement.
	 * Used retrieve modules (resources) for ACL from data source classes.
	 * Returned array elements should be string or instance of Zend_Acl_Resource
	 * 
	 * @return array
	 */
	protected function getModuleList(){return array();}
	
	/**
	 * Stub method. Concrete classes should implement.
	 * Used retrieve rules for ACL from data source classes.
	 * Returned array elements should be an instance of Jax_Acl_Rule
	 * 
	 * @return array
	 */
	protected function getAclRoleRules(){return array();}
	
	protected function getRoleRules(){return array();}
	
	protected function getUserRules(){return array();}
	
	/**
	 * Stub method. Concrete classes should implement.
	 * Used to perform CRUD functions for a user and role.
	 * 
	 * @return boolean
	 */
	protected function manageUserRole($verb,$user,$role){}
	
	/**
	 * Stub method. Concrete classes should implement.
	 * Used to perform CRUD functions for user permissions.
	 * 
	 * @return boolean
	 */
	protected function manageUserPerms($verb,$role,$resource,$access,$atype,$group = false){}
	
	/**
	 * Stub method. Concrete classes should implement.
	 * Used to retrieve list of access types from data storage.
	 */
	protected function getACLAccessTypes(){return array();}
	
	/**
	 * Magic method used to dynamically call data source methods.
	 * Main use is for the Jax_Data_Source_Soap class where class methods are not
	 * defined but depend on the wsdl. Can also be used by other data source classes to allow dynamic behaviour.
	 * 
	 * @param string $method - The requested method
	 * @param array $args - Array of parameters to pass to the method
	 */
	abstract public function __call($method,$args);
}