<?php
/**
 * Abstract ACL config class.
 * Used by project specific ACL config classes to build the ACL.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
abstract class Jax_Acl_Config
{	
	/**
	 * Class constants used internally to configure the ACL
	 * 
	 * @var string
	 */
	const CONFIG_KEY_RESOURCES = 'resources';
	const CONFIG_KEY_ROLES = 'roles';
	const CONFIG_KEY_ACCESS_LEVEL = 'accessLevels';
	const CONFIG_KEY_ACCESS_RULE = 'accessRules';
	
	/**
	 * Hard coded access level. This access level is assigned to resources that should not be rendered visually by the client.
	 * 
	 * @var string
	 */
	const ACL_ACCESS_LEVEL_SERVICE = 'Service';
	
	/**
	 * Acl configuration object
	 * 
	 * @var Zend_Config
	 */
	protected $_config;
	
	/**
	 * Acl configuration data. This is populated by addeding roles, resources and creating access rules.
	 * 
	 * @var array
	 */
	protected $_configData = array(self::CONFIG_KEY_ROLES=>array(),
									self::CONFIG_KEY_RESOURCES=>array(),
									self::CONFIG_KEY_ACCESS_RULE=>array());
	
	/**
	 * Must be implemented by concrete classes. Used to create and add roles, resources and access rules to the ACL.
	 */
	public abstract function __construct();
	
	/**
	 * Wraps the user configured Acl into a Zend_Config object
	 * 
	 * @return Zend_Config
	 */
	public final function init()
	{
		$this->_config = new Zend_Config($this->_configData);
		return $this->_config;
	}
	
	/**
	 * Adds a resource to the ACL
	 * 
	 * @param Jax_Acl_Resource $resource
	 * @param Jax_Acl_Resource $parent
	 * @return Jax_Acl_Config
	 */
	public final function addResource(Zend_Acl_Resource $resource, Zend_Acl_Resource $parent = NULL)
	{
		if (!is_null($parent)){
			if ($this->hasResource($parent)){
				$parent = $this->getResource($parent);
			} else {
				$this->addResource($parent);
			}
		}
		$this->_configData[self::CONFIG_KEY_RESOURCES][(string) $resource] = array($resource,$parent);
		return $this;
	}
	
	/**
	 * Adds a role to the ACL
	 * 
	 * @param Zend_Acl_Role $role
	 * @param Zend_Acl_Role $parent
	 * @return Jax_Acl_Config
	 */
	public final function addRole($role, $parent = NULL)
	{
		$existingParents = null;
		$parentRoles = array();
		
		if ($this->hasRole($role)){
			$roleDef = $this->getRoleDefinition($role);
			$existingParents = $roleDef[1];
		}
		
		if (!is_null($parent)){
			if (is_array($parent)){
				foreach ($parent as $pResource){
					if(!$this->hasRole($pResource)){
						$this->addRole($pResource);
					}
					$parentRoles[] = $this->getRole($pResource);
				}
			} else {
				if (!$this->hasRole($parent)) {
					$this->addRole($parent);
				}
				$parentRoles[] = $this->getRole($parent);
			}
		}
		
		if(!is_null($existingParents)){
			if (is_array($existingParents)){
				$parentRoles = array_merge($existingParents,$parentRoles);
			} else {
				$parentRoles[] = $existingParents;
			}
		}
		
		if (count($parentRoles) == 0) $parentRoles = null;
		
		$this->_configData[self::CONFIG_KEY_ROLES][(string) $role] = array($role,$parentRoles);
		return $this;
	}

	/**
	 * Creates and access rule for the ACL.
	 * 
	 * @param Zend_Acl_Role $role - The role the rule applies to.
	 * @param Zend_Acl_Resource $resource - The resource the rule applies to.
	 * @param string $accessLevel - The access level for the rule.
	 * @param string $allowDeny - The rule either allows or denies access by the specific role to the resource
	 * @return Jax_Acl_Config
	 */
	public final function createAccessRule(Zend_Acl_Role $role = null,$resource = null,$accessLevel = null,$allowDeny = 'allow')
	{		
		$this->_configData[self::CONFIG_KEY_ACCESS_RULE][] = array($role,$resource,$accessLevel,$allowDeny);
		return $this;
	}
	
	/**
	 * Check if resource exists in the configuration
	 * 
	 * @param string $resourceId
	 */
	public final function hasResource($resourceId){
		if(array_key_exists((string) $resourceId, $this->_configData[self::CONFIG_KEY_RESOURCES])) return true;
		return false;
	}
	
	public final function hasRole($roleId){
		if(array_key_exists((string) $roleId, $this->_configData[self::CONFIG_KEY_ROLES])) return true;
		return false;
	}
	
	/**
	 * Retrieve an existing resource
	 * 
	 * @return mixed
	 */
	public final function getResource($resourceId){
		if($this->hasResource($resourceId)) return $this->_configData[self::CONFIG_KEY_RESOURCES][(string) $resourceId][0];
		return null;
	}

	public final function getRole($roleId){
		if($this->hasRole($roleId)) return $this->_configData[self::CONFIG_KEY_ROLES][(string) $roleId][0];
		return null;
	}
	
	public final function getRoleDefinition($roleId){
		if($this->hasRole($roleId)) return $this->_configData[self::CONFIG_KEY_ROLES][(string) $roleId];
		return null;
	}
	
	protected final function _buildClassName($resourceId,$className = ""){
		if (!$this->hasResource($resourceId)){
			if (strlen($className) > 1) return $className;
			return $resourceId;
		} else {
			$resource = $this->_configData[self::CONFIG_KEY_RESOURCES][(string) $resourceId];
			
			if (isset($resource[1])){
				$pId = (string) $resource[1];
				
				if (strlen($className) > 1){
					$className = $pId."_".$className;
				} else {
					$className = $pId."_".$resourceId;
				}

				return $this->_buildClassName($pId,$className);
			} else {
				if (strlen($className) > 1){
					return $className;
				}
				return $resourceId;
			}
		}
	}
}