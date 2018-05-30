<?php
/**
 * Role Administration
 * Methods related to administration of user roles in the ACL.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link http://www.nerdsconsulting.com
 * @package Jax
 * @version 1.0.0
 */
class Jax_Acl_Resource_Controlpanel_Acladmin_Roleadmin extends Jax_Acl_Resource_Controlpanel_Acladmin
{
	protected $_resourceId = Jax_Acl_Constants::RESOURCE_CP_ACL_ROLEADMIN;
	protected $_privateRoles = array(
			Jax_Acl_Constants::ROLE_DEVELOPER,
			Jax_Acl_Constants::ROLE_ADMINS
		);
	
	public function __construct(){
		parent::__construct();
		$this->_config[self::DISPLAY_NAME] = 'ACL Roles';
		$this->_config[self::ICON] = 'icon-accessibility';
	}
	
	public function getGroups(){
		$where = "`role` NOT IN (";
		
		foreach ($this->_privateRoles as $role){
			$where .= "\"$role\",";
		}
		
		$where = substr($where, 0,strlen($where)-1).")";
		
		
		$roles = Jax_Data_Source::getInstance()->getRecord("AclRoles",$where,true);
		
		return $roles;
	}
	
	protected function allPermsProc(Zend_Controller_Request_Abstract $request){
		return Jax_Response::Valid(Jax_Data_Source::getInstance()->getACLAccessTypes());
	}
	
	protected function newProc(Zend_Controller_Request_Abstract $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_WRITE);
		
		if(!$request->isPost()) return Jax_Response::Error("Invalid Request (1)");
		
		$params = $request->getParams();
		$pdata = array();
		foreach($params as $h=>$v){
			if(empty($v)) continue;
			$v = @mysql_escape_string($v);
			
			if(substr($h,0,4) == "frm_"){
				$nh = substr($h, 4);
				$pdata[$nh] = $v;
			} else {
				continue;
			}
		}
		
		$pdata['username'] = strtolower($pdata['username']);
		
		$r = Jax_Data_Source::getInstance()->addRecord("AuthUsers",$pdata);
		if($r){
			return $this->_write(array('username'=>$pdata['username'],'role'=>$params['Role']), $request);
		}
		
		return Jax_Response::Error("Unable to add new account");
	}
	
	protected function _read($params,$request){
		$query = $params['query'];
		
		$list = Jax_User_Search::partial($query);

		return Jax_Response::Valid($list);
	}
	
	protected function _write($params,$request){
		$user = @$params['username'];
		$role = @$params['role'];
		
		$this->_canPerformOperationOn($user);
		
		if(Jax_Data_Source::getInstance()->manageUserRole(Jax_Acl_Constants::ACCESS_WRITE,$user,$role)){
			Jax_System_Logger::log(new Jax_LogEntry(
				Jax_LogEntry::LOG_CATEGORY_ACL,
				$this->_resourceId,
				Jax_Auth::getAuthId()." added role ".$role." to ".$user,
				Jax_Acl_Constants::ACCESS_WRITE));
			
			$this->_refreshACL();
			return Jax_Response::Valid("1");
		}
		
		return Jax_Response::Error("Unable to set user role!");
	}
	
	protected function _delete($params,$request){
		$user = @$params['username'];
		$role = @$params['role'];
		
		$this->_canPerformOperationOn($user);
		
		if(Jax_Data_Source::getInstance()->manageUserRole(Jax_Acl_Constants::ACCESS_DELETE,$user,$role)){
			Jax_System_Logger::log(new Jax_LogEntry(
				Jax_LogEntry::LOG_CATEGORY_ACL,
				$this->_resourceId,
				Jax_Auth::getAuthId()." removed role ".$role." from ".$user,
				Jax_Acl_Constants::ACCESS_DELETE));
				
			$this->_refreshACL();
			return Jax_Response::Valid("1");
		}
		
		return Jax_Response::Error("Unable to remove user role!");
	}
	
	protected function _update($params,$request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_UPDATE);
		
		if(!$request->isPost() || !isset($params['p']) || !isset($params['v']) || !isset($params['u'])) return Jax_Response::Error("Invalid Request");
		
		$v = mysql_escape_string($params['v']);
		$p = mysql_escape_string($params['p']);
		$u = mysql_escape_string($params['u']);
		
		$this->_canPerformOperationOn($u);
		
		if($p == 'password') $v = null;
		
		$r = Jax_Data_Source::getInstance()->updateRecord("AuthUsers","`username`='".$u."'",array($p=>$v));
		if($r){
			return Jax_Response::Valid(1);
		}
		return Jax_Response::Error("Nothing to update.");
	}
	
	protected function userPermissionProc(Zend_Controller_Request_Abstract $request){
		$params = $request->getParams();
		
		// For delete requests
		if ($request->isPost() && $request->getParam('access') == Jax_Acl_Constants::ACCESS_DELETE)
			return $this->_delUserPerm($request->getParams());
			
		// For write requests
		if ($request->isPost() && $request->getParam('access') == Jax_Acl_Constants::ACCESS_WRITE)
			return $this->_addUserPerm($request->getParams());
		
		if (!isset($params['role']) || !isset($params['resource'])) return Jax_Response::Error("Invalid request!");
		
		$role = @$params['role'];
		$module = @$params['resource'];
	
		$rolePerms = Jax_Acl::getInstance()->getACLByUser($role);
		$perms = array();
		
		if (array_key_exists($module, $rolePerms)){
			$perms = $rolePerms[$module];
		}
		
		// Get inherited perms
		$types = Jax_Data_Source::getInstance()->getACLAccessTypes();
		$dbModRules = Jax_Data_Source::getInstance()->getRoleRules();
		$dbUserRules = Jax_Data_Source::getInstance()->getUserRules();
		
		$resH = Jax_Acl::getInstance()->getResourceHierarchy();
		$path = Jax_Utilities_ArraySearchRecursive::run($module,$resH,true,array(),true);
		foreach ($path as $i=>$v) if ($v=='children') unset($path[$i]);
		array_pop($path);
		
		$dbRules = array();
		foreach ($dbModRules as $modRule){
			$dbRules[$modRule['role']][$modRule['module']][$modRule['access']] = $modRule['allow'];
		}
		foreach ($dbUserRules as $modRule){
			$dbRules[$modRule['role']][$modRule['module']][$modRule['access']] = $modRule['allow'];
		}
		
		$uRoles = Jax_User::getInstance()->setUser($role)->getUserObject();
		$uRoles = $uRoles['Role'];
		
		foreach ($types as $atype){
			if (Jax_Acl::getInstance()->acl()->isAllowed($role,$module,$atype['access'])){
				if (!isset($perms[$atype['access']]))
					$perms[$atype['access']." (I)"] = "1";
			} else {
				// inherited via user groups
				foreach ($uRoles as $urole){
					if(isset($dbRules[$urole][$module][$atype['access']]))
						$perms[$atype['access']." (I)"] = $dbRules[$urole][$module][$atype['access']];
				}
				
				// inherited via user perms on parent modules
				foreach ($path as $pmod){
					if(isset($dbRules[$role][$pmod][$atype['access']])){
						$perms[$atype['access']." (I)"] = $dbRules[$role][$pmod][$atype['access']];
					}
				}
			}
		}
		
		return Jax_Response::Valid($perms);
	}
	
	protected function _delUserPerm($params){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_DELETE);
		
		$role = @$params['role'];
		$module = @$params['resource'];
		$access = @$params['perm'];
		
		$this->_canPerformOperationOn($role);
		
		if(Jax_Data_Source::getInstance()->manageUserPerms(Jax_Acl_Constants::ACCESS_DELETE,$role,$module,$access)){
			Jax_System_Logger::log(new Jax_LogEntry(
				Jax_LogEntry::LOG_CATEGORY_ACL,
				$this->_resourceId,
				Jax_Auth::getAuthId()." removed permission [$access] on $module from $role",
				Jax_Acl_Constants::ACCESS_DELETE));
				
			$this->_refreshACL();
			return Jax_Response::Valid("1");
		}
		
		return Jax_Response::Error("Unable to remove user permission.");
	}
	
	protected function _addUserPerm($params){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_WRITE);
		
		$role = @$params['role'];
		$module = @$params['resource'];
		$access = @$params['perm'];
		$type = @$params['atype'];
		($type == 'Deny')?$type = "0":null;
		($type == 'Allow')?$type = "1":null;
		
		$this->_canPerformOperationOn($role);
		
		if(Jax_Data_Source::getInstance()->manageUserPerms(Jax_Acl_Constants::ACCESS_WRITE,$role,$module,$access,$type)){
			Jax_System_Logger::log(new Jax_LogEntry(
				Jax_LogEntry::LOG_CATEGORY_ACL,
				$this->_resourceId,
				Jax_Auth::getAuthId()." added permission [$access] on $module to $role",
				Jax_Acl_Constants::ACCESS_WRITE));
				
			$this->_refreshACL();
			return Jax_Response::Valid("1");
		}
		
		return Jax_Response::Error("Unable to add user permission.");
	}
	
	private function _isProtectedAccount($username){
		$roles = Jax_Data_Source::getInstance()->getRecord("AclUserRoles","`role`=\"$username\"",true);
		
		foreach ($roles as $role){
			if(in_array($role['parent'], $this->_privateRoles)) return true;
		}
		
		return false;
	}
	
	private function _canPerformOperationOn($user){
		if($this->_isProtectedAccount($user) && !in_array(Jax_Auth::getAuthLevel(), $this->_privateRoles))
			throw new Exception('Cannot update protected account!');
	}
	
	public static function getUserRole($role){
		$acl = Jax_Acl::getInstance()->acl();
		
		if (!$acl->isAllowed(Jax_Auth::getAuthId(),
			Jax_Acl_Constants::RESOURCE_CP_ACL_ROLEADMIN,
			Jax_Acl_Constants::ACCESS_READ)) return null;
		
		$roleObject = $acl->getRole($role);
		
		if ($roleObject instanceof Jax_Acl_Role){
			return $roleObject->getUserObject();
		}
		
		return false;
	}
}