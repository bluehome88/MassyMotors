<?php
/**
 * Jax Access Control List Class
 * Provides functionality to build and manage an ACL based on Zend_Acl
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Acl
{
	/**
	 * The ACL instance 
	 * 
	 * @var Jax_Acl
	 */
	protected static $_instance;
	
	/**
	 * Underlying Zend_Acl instance
	 * 
	 * @var Zend_Acl
	 */
	protected $_ACL;
	
	/**
	 * The ACL configuration
	 * 
	 * @var Jax_Acl_Config
	 */
	protected $_config;
	
	/**
	 * ACL resources parent > child relationship structure
	 * 
	 * @var Array
	 */
	protected $_resourceHierarchy = array();
	
	protected $_pendingResources = array();
	
	/**
	 * ACL roles parent > child relationship structure
	 * 
	 * @var Array
	 */
	protected $_roleHierarchy = array();
	
	const ACCESS_ALLOW = 'allow';
	const ACCESS_DENY = 'deny';
	
	/**
	 * Default constructor (Protected)
	 * 
	 * Singleton pattern.
	 * 
	 * Create Zend_Acl instance.
	 */
	protected function __construct()
	{
		$this->_ACL = new Zend_Acl();
	}
	
	/**
	 * Singleton pattern.
	 * 
	 * @return Jax_Acl
	 */
	public static function getInstance()
	{
		if (!self::$_instance)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public static function reset(){
		self::$_instance = null;
	}
	
	/**
	 * Sets a config to build into the ACL
	 * 
	 * @param Jax_Acl_Config $config
	 * @return Jax_Acl
	 */
	public function setConfig(Jax_Acl_Config $config)
	{
		$this->_config = $config->init();
		$this->_init();
		return $this;
	}
	
	/**
	 * Proxy to Zend_Acl Object
	 * 
	 * @return Zend_Acl
	 */
	public function acl()
	{
		return $this->_ACL;
	}
	
	/**
	 * Retrieve the role hierarchy
	 * 
	 * @return Array
	 */
	public function getRoleHierarchy($role = null)
	{
		if (!is_null($role)){
			if (array_key_exists($role, $this->_roleHierarchy)) return $this->_roleHierarchy[$role];
		}
		return $this->_roleHierarchy;
	}
	
	/**
	 * Retrieve the resource hierarchy
	 * 
	 * @return Array
	 */
	public function getResourceHierarchy($resourceId = null,$returnPath = false)
	{
		if (is_null($resourceId))
			return $this->_resourceHierarchy;
			
		$path = Jax_Utilities_ArraySearchRecursive::run($resourceId,$this->_resourceHierarchy,true,array(),true);
		
		if ($returnPath === true) return $path;
		
		$sub = $this->_resourceHierarchy;
		foreach ($path as $key){
			$sub = $sub[$key];
		}
		
		return $sub;
	}
	
	public function getSubResources($resourceId,$subResource = array(),$withParents = false){
		$resourceTree = $this->getResourceHierarchy($resourceId);

		if (array_key_exists("children", $resourceTree)){
			foreach ($resourceTree['children'] as $r=>$rtree){
				if($withParents === true){
					$subResource[$r] = $resourceId;
				} else {
					$subResource[] = $r;
				}
				$subResource = $this->getSubResources($r,$subResource,$withParents);
			}
		}
		return $subResource;
	}
	
	/**
	 * Returns an array representing all resolved Role, Modules and access permissions for a user based on their assigned role.
	 * @param string $userId
	 * @return array
	 */
	public function getACLByUserRole($userId){
		$Role = $this->getRoleHierarchy($userId);
		
		$rolePerms = array();
		$roleMods = Jax_Data_Source::getInstance()->getRoleRules();
		$RoleHierarchy = $Role;
		$count = 0;
		while ($count < count($RoleHierarchy)){
			$k = array_search($RoleHierarchy[$count], $RoleHierarchy);
			
			if (!is_int($k) || $k >= $count){
				$RoleHierarchy = array_merge($RoleHierarchy,Jax_Acl::getInstance()->getRoleHierarchy($RoleHierarchy[$count]));
			} else {
				unset($RoleHierarchy[$count]);
			}
			
			$count++;
		}
		
		foreach ($roleMods as $roleRule){
			if(in_array($roleRule['role'], $RoleHierarchy)){
				//$rolePerms[$roleRule['role']][$roleRule['module']][] = array($roleRule['access']=>$roleRule['allow']);
				$rolePerms[$roleRule['module']][$roleRule['access']] = $roleRule['allow'];
				
				/*$subResources = $this->getSubResources($roleRule['module']);
				foreach ($subResources as $resource){
					$rolePerms[$resource][$roleRule['access']] = $roleRule['allow'];
				}*/
			}
		}
		
		return $rolePerms;
	}
	
	public function getACLByUser($userId){
		$userMods = Jax_Data_Source::getInstance()->getUserRules();
		$rolePerms = array();
		
		foreach ($userMods as $uRule){
			if ($uRule['role'] == $userId){
				$rolePerms[$uRule['module']][$uRule['access']] = $uRule['allow'];
			}
		}
		
		return $rolePerms;
	}
	
	public function getACLByGroup($group){
		$roleMods = Jax_Data_Source::getInstance()->getRoleRules();
		$rolePerms = array();
		
		foreach ($roleMods as $rRule){
			if($rRule['role'] == $group){
				$rolePerms[$rRule['module']][$rRule['access']] = $rRule['allow'];
			}
		}
		
		return $rolePerms;
	}
	
	/**
	 * Initializes the Jax_Acl class.
	 * 
	 * @return null
	 */
	protected function _init()
	{
		$this->_ACL->removeAll()->removeRoleAll();
		$this->_setResources();
		$this->_processPendingResources();
		$this->_setRoles();
		$this->_setAccess();
	}
	
	/**
	 * Reads condifuration and sets ACL resources.
	 * 
	 * @return Jax_Acl
	 */
	protected function _setResources()
	{
		$resourceList = $this->_config->{Jax_Acl_Config::CONFIG_KEY_RESOURCES}->toArray();
		
		foreach ($resourceList as $resourceData)
		{
			$resource = $resourceData[0];
			$parent = $resourceData[1];
			if (!is_null($parent))
				if(!$this->_ACL->has($parent)) $this->_ACL->addResource($parent);

			if(!$this->_ACL->has($resource))
				$this->_ACL->addResource($resource,$parent);

			$this->_addToHierarchy($this->_resourceHierarchy,$resource,$parent);
		}
		
		return $this;
	}
	
	/**
	 * Read configuration and sets ACL roles.
	 * 
	 * @return Jax_Acl
	 */
	protected function _setRoles()
	{
		$rolesList = $this->_config->{Jax_Acl_Config::CONFIG_KEY_ROLES}->toArray();
		
		foreach ($rolesList as $roleId=>$roleData)
		{
			$role = $roleData[0];
			$parents = $roleData[1];
			
			$resolvedParents = array();
			
			if (!is_null($parents))
			{
				if(is_array($parents)){
					foreach ($parents as $parent) {
						
						if(!$this->_ACL->hasRole($parent)){
							$this->_resolveParents($parent);
						} 
						
						try {
							$aclParent = $this->_ACL->getRole($parent);
							$resolvedParents[] = $aclParent;
						} catch (Exception $e){}
					}
				} else {
					if(!$this->_ACL->hasRole($parents)){
						$this->_resolveParents($parents);
					}
					
					try {
						$aclParent = $this->_ACL->getRole($parents);
						$resolvedParents[] = $aclParent;
					} catch (Exception $e){}
				}
				
				if (count($resolvedParents) == 0) $resolvedParents = null;
				
				if(!$this->_ACL->hasRole($role))
					$this->_ACL->addRole($role,$resolvedParents);
			} else {
				if(!$this->_ACL->hasRole($role))
					$this->_ACL->addRole($role);
			}
			
			// Added 2013 01 16
			foreach ($resolvedParents as &$p){
				$p = (string) $p;
			}
			$this->_roleHierarchy[$roleId] = $resolvedParents;
			//
		}
		
		return $this;
	}
	
	/**
	 * Recursively resolves parent roles
	 * 
	 * @param Zend_Acl_Role | String $role
	 */
	protected function _resolveParents($role){
		$rolesList = $this->_config->{Jax_Acl_Config::CONFIG_KEY_ROLES}->toArray();
		if (array_key_exists((string) $role, $rolesList)){
			$roleConfig = $rolesList[(string) $role];
			
			$roleParents = $roleConfig[1];
			
			if(!is_null($roleParents)){
				if (is_array($roleParents)){
					foreach ($roleParents as $pRole){
						$this->_resolveParents($pRole);
					}
				} else {
					$this->_resolveParents($roleParents);
				}
			}
			if (!$this->_ACL->hasRole($role))
				$this->_ACL->addRole($role,$roleParents);
		}
	}
	
	/**
	 * Read configuration and sets resource access rules.
	 * 
	 * @return null
	 */
	protected function _setAccess()
	{
		$this->_ACL->deny();
		
		$Rules = $this->_config->{Jax_Acl_Config::CONFIG_KEY_ACCESS_RULE}->toArray();
		
		foreach ($Rules as $rule)
		{		
			$Access = $rule[2];
			
			if (is_array($Access))
			{
				foreach ($Access as $level)
				{
					$this->_createRule($rule,$level);
				}
			} else {
				$this->_createRule($rule,$Access);
			}
		}
	}
	
	/**
	 * Creates an access rule
	 * 
	 * @param array $rule
	 * @param string $access
	 */
	protected function _createRule($rule,$access)
	{
		$Role = $rule[0];
		$Resource = $rule[1];
		$allowDeny = $rule[3];
		
		switch ($allowDeny){
			case self::ACCESS_ALLOW:
				$this->_ACL->allow($Role,$Resource,$access);
				break;
				
			case self::ACCESS_DENY:
				$this->_ACL->deny($Role,$Resource,$access);
				break;
		}
	}
	
	/**
	 * Adds an entry to the resource hierarchy 
	 * 
	 * @param array $hierarchy
	 * @param Jax_Acl_Resource $resource
	 * @param mixed $parent
	 */
	protected function _addToHierarchy(&$hierarchy,$resource,$parent)
	{
		$r = $resource;
		
		try {
			$cfg = $r->getConfig();
		} catch (Exception $e){
			$cfg = array();
		}
		
		$resource = (string) $resource;
		$parent = (string) $parent;
		
		if (is_null($parent) || $parent == '')
		{
			$hierarchy[$resource] = array('config'=>$cfg);
		} else {
			$path = Jax_Utilities_ArraySearchRecursive::run($parent,$hierarchy,false,array(),true);
			if (is_array($path) && count($path) > 0) {
				$location = &$hierarchy;
				foreach ($path as $key)
				{
					$location = &$location[$key];
				}
				$location['children'][$resource] = array('config'=>$cfg);
			} else {
				$this->_pendingResources[$parent][] = $r;
			}
		}
	}
	
	protected function _processPendingResources(){
		foreach ($this->_pendingResources as $p=>$r){
			foreach ($r as $resource){
				$this->_addToHierarchy($this->_resourceHierarchy, $resource, $p);
			}
		}
	}
	
	/**
	 * Restores the stored ACL
	 * 
	 * @return Jax_Acl
	 */
	public static function restore(){
		$session = new Zend_Session_Namespace(APPNAMESPACE);
		if(isset($session->{Jax_System_Constants::SYSTEM_SESSIONKEY_APPACL})){
			$CAcl = unserialize($session->{Jax_System_Constants::SYSTEM_SESSIONKEY_APPACL});
			self::$_instance = $CAcl;
		}
		return self::$_instance;
	}	
}