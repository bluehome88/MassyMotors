<?php
/**
 * ACL Config default class.
 * Default implementation of Jax_Acl_Config.
 * Can be used in conjunction with Active Directory, the provided MySQL Schema or any other 
 * data source that conforms to Jax_Data_Source_Abstract.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Acl_Config_Default extends Jax_Acl_Config
{
	public function __construct()
	{
		$args = func_get_args();
		if(isset($args[0])){
			$userId = $args[0];
		} else {
			$userId = null;
		}
		
		// Active Directory Option
		if (Jax_App_Options::getInstance()->active_directory){
			// Load Roles
			$SecGroups = Jax_Utilities_ActiveDirectory::getAllSecurityGroups();
			
			// Add AD roles (AD Security Groups)
			foreach ($SecGroups as $Group=>$GroupDesc){
				if (Jax_Utilities_ActiveDirectory::isACLGroup($Group)){
					$GroupName = Jax_Utilities_ActiveDirectory::parseOutACL($Group);
					
					$role = new Jax_Acl_Role_Group($GroupName);
					
					$this->addRole($role);
					
					$members = Jax_Utilities_ActiveDirectory::getGroupMembers($Group);
					
					// Add user roles from AD
					for ($m = 0; $m < $members['count']; $m++){
						$user = $members[$m];
						$data = explode(",", $user);
						
						$fullName = "";
						
						foreach ($data as $val){
							if (substr($val, 0,2) == "CN"){
								$fullName = trim(substr($val, 3));
							}
						}
						
						if(strlen($fullName) > 0){
							$users = Jax_Utilities_ActiveDirectory::getUsernameByFullName($fullName);
							
							foreach ($users as $username=>$fname){
								// Only add enabled users from AD
								$adObject = Jax_Utilities_ActiveDirectory::getUserInfo($username,array("*"));
								
								if(($adObject[0]['useraccountcontrol'][0] & 2) == 0){ // true if account is enabled in AD
									$uRole = new Jax_Acl_Role_ActiveDirectory($username);
									$this->addRole($uRole,$role);
								}
							}						
						}
					}
				}
			}
		}
		
		// Add custom roles from data source
		$roles = Jax_Data_Source::getInstance()->getRoles();
		foreach ($roles as $role){
			
			// Check if role was already defined, else create new.
			$cRoleId = $role['role'];			
			if ($this->hasRole($cRoleId)){
				$cRole = $this->getRole($cRoleId);
			} else {
				
				// Determines if defined role is already an object instance of Zend_Acl_Role
				if (!($cRoleId instanceof Zend_Acl_Role)){
					if(@$role['isGroup']){
						$cRole = new Jax_Acl_Role_Group((string) $cRoleId);
					} else {
						$cRole = new Jax_Acl_Role_Default((string) $cRoleId);
					}
				} else {
					$cRole = $cRoleId;
				}
			}
						
			if (!is_null($role['parent'])){
				$pRoleId = $role['parent'];
				if ($this->hasRole($pRoleId)){
					$parent = $this->getRole($pRoleId);
				} else {
					
					if (!($pRoleId instanceof Zend_Acl_Role)){
						
						foreach ($roles as $role2){
							if($role2['role'] == $pRoleId){
								if(@$role2['isGroup']){
									$parent = new Jax_Acl_Role_Group((string) $pRoleId);
									break;
								} 
							}
						}
						if(!isset($parent))
							$parent = new Jax_Acl_Role_Default((string) $pRoleId);
					} else {
						$parent = $pRoleId;
					}
				}
			} else {
				$parent = null;
			}
			
			$this->addRole($cRole,$parent);
		}
		
		unset($parent);
		unset($role);
		unset($cRole);
		unset($uRole);
		
		$modules = Jax_Data_Source::getInstance()->getModuleList();
		foreach ($modules as $module){
			// First determine if a resource class is supplied
			if ($module instanceof Zend_Acl_Resource){
				$this->addResource($module);
			} else {
			
				// Alternative to the commented code block below.
				$classNameBase = APPNAMESPACE."_Acl_Resource_";
				$altClassNameBase = "Jax_Acl_Resource_";
				
				if (!is_null($module['parent']) || trim(strlen($module['parent'])) > 2){
					$resolvedClassName = $this->_buildClassName($module['parent']);
					
					$parentClassName = $classNameBase.$resolvedClassName;
					$altParentClassName = $altClassNameBase.$resolvedClassName;
					
					$resourceClassName = $parentClassName."_".$module['module'];
					$altResourceClassName = $altParentClassName."_".$module['module'];
					
					$basicResourceClassName = $classNameBase.$module['module'];
					$basicParentClassName = $classNameBase.$module['parent'];
					
					if(@class_exists($parentClassName,true)){
						$parent = new $parentClassName();
					} elseif(@class_exists($basicParentClassName,true)){
						$parent = new $basicParentClassName();
					} elseif(@class_exists($altParentClassName,true)){
						$parent = new $altParentClassName();						
					} else {
						throw new Exception("Class $parentClassName not defined! (1)");
					}
					
					if (@class_exists($resourceClassName,true)){
						$resource = new $resourceClassName();
					} elseif(@class_exists($basicResourceClassName,true)){
						$resource = new $basicResourceClassName();
					} elseif(@class_exists($altResourceClassName,true)){
						$resource = new $altResourceClassName();
					} else {
						throw new Exception("Class $resourceClassName not defined! (2)");
					}
					
					$this->addResource($resource,$parent);
				}
				else {
					$resourceClassName = $classNameBase.$module['module'];
					$altResourceClassName = $altClassNameBase.$module['module'];
					
					if (@class_exists($resourceClassName,true)){
						$resource = new $resourceClassName();
					} elseif(@class_exists($altResourceClassName,true)){
						$resource = new $altResourceClassName();
					} else {
						throw new Exception("Class $resourceClassName not defined!(3)");
					}
					$this->addResource($resource);
				}
				
				/*
				 * Alternative to the code block above.
				 * 
				 * 
				$resourceClassName = "Mis_Acl_Resource_".$module['module'];
				if (@class_exists($resourceClassName,true)){
					$resource = new $resourceClassName();
					
					if (!is_null($module['parent'])){
						$parentClassName = "Mis_Acl_Resource_".$module['parent'];
						if(@class_exists($parentClassName,true)){
							$parent = new $parentClassName();
							
							$this->addResource($resource,$parent);
							
						} else {
							throw new Exception("Class $parentClassName not defined!");
						}
					} else {
						$this->addResource($resource);
					}
				} else {
					throw new Exception("Class $resourceClassName not defined!");
				}
				*/
			}
		}
		
		// Create Access Rules
		$rolesConfigData = Jax_Data_Source::getInstance()->getAclRoleRules();
		foreach ($rolesConfigData as $rule){
			if ($rule->allow == 1){ $allow = Jax_Acl::ACCESS_ALLOW; } else { $allow = Jax_Acl::ACCESS_DENY; }
			
			$rsrc = $this->getResource($rule->module);
			
			if ($this->hasRole($rule->role)){
				$role = $this->getRole($rule->role);
				if(!is_null($rsrc))
					$this->createAccessRule($role,$rsrc,$rule->access,$allow);
			}
		}
	}	
}