<?php
/**
 * Module Administration
 * Methods related to administration of roles (specifically those considered groups) within the sytem.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link http://www.nerdsconsulting.com
 * @package Jax
 * @version 1.0.0
 */
class Jax_Acl_Resource_Controlpanel_Acladmin_Groupadmin extends Jax_Acl_Resource_Controlpanel_Acladmin
{
	protected $_resourceId = Jax_Acl_Constants::RESOURCE_CP_ACL_GROUPADMIN;
	
	public function __construct(){
		$this->_config[self::DISPLAY_NAME] = 'ACL Groups';
		$this->_config[self::ICON] = 'icon-user-3';
	}
	
	protected function allPermsProc(Zend_Controller_Request_Abstract $request){
		return Jax_Response::Valid(Jax_Data_Source::getInstance()->getACLAccessTypes());
	}
	
	public function getACLGroupsProc(){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_READ);
		
		$ACLGroups = Jax_Acl::getInstance()->getRoleHierarchy();
		$ACLGroupsF = array();
		foreach ($ACLGroups as $role=>$parents){
			$ACLRole = Jax_Acl::getInstance()->acl()->getRole($role);
			if (@$ACLRole->isGroup()) $ACLGroupsF[] = $role;
		}
		
		// Hide developers group from non developers
		if(!Jax_Acl::getInstance()->acl()->inheritsRole(Jax_Auth::getAuthId(), "Developers")){
			unset($ACLGroupsF[array_search("Developers", $ACLGroupsF)]);
		}
		return Jax_Response::Valid($ACLGroupsF);
	}
	
	protected function _write($params,$request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_WRITE);
		
		if($request->isPost()){
			$grp = $request->getParam('group');
			
			if($grp){
				$grp = str_replace(" ", "", $grp);
				
				if(!ctype_alpha($grp)) return Jax_Response::Error('Only alphabetic characters allowed!');
				
				if($grp == "Abstract") return Jax_Response::Error('Group name '.$grp.' is reserved. Please use another.');
				
				$r = Jax_Data_Source::getInstance()->addRecord("AclRoles",array('role'=>$grp));
				if($r){
					
					$path = APPLICATION_PATH.'/../library/'.APPNAMESPACE.'/User/';
					
					if(!file_exists($path."$grp.php")){
					
						$fh = fopen($path."$grp.php", "w");
						fwrite($fh, "<?php
class ".APPNAMESPACE."_User_".$grp." extends ".APPNAMESPACE."_User_Abstract
{
				
}");
						fclose($fh);
					}
					
					$this->_refreshACL();
					return Jax_Response::Valid(1);
				} else {
					return Jax_Response::Error('Unable to add group.');
				}
			}
		}
		
		return Jax_Response::Error("Invalid Request");
	}
	
	protected function _append($params, $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_APPEND);
		
		if($request->isPost()){
			if(!isset($params['g'])) return Jax_Response::Error("Group not specified!");
			
			if(isset($params['desc'])){
				$desc = mysql_escape_string($params['desc']);
				$r = Jax_Data_Source::getInstance()->updateRecord("AclRoles","`role`='".$params['g']."'",array('description'=>$desc));
				if($r) return Jax_Response::Valid(1);
				return Jax_Response::Error("Unable to update description.");
			}
			
			if(isset($params['p'])){
				$parent = mysql_escape_string($params['p']);
				if($parent == "") $parent = null;
				$r = Jax_Data_Source::getInstance()->updateRecord("AclRoles","`role`='".$params['g']."'",array('parent'=>$parent));
				if($r) {
					$this->_refreshACL();
					return Jax_Response::Valid(1);
				}
				return Jax_Response::Error("Unable to set parent group.");
			}
		}
		
		return Jax_Response::Error("Invalid Request");
	}
	
	protected function _delete($params, $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_DELETE);
		
		if($request->isPost()){
			if(!isset($params['g'])) return Jax_Response::Error("Group not specified!");
			
			$r = Jax_Data_Source::getInstance()->deleteRecord("AclRoles","`role`='".$params['g']."'");
			if($r) {
				$file = APPLICATION_PATH.'/../library/'.APPNAMESPACE.'/User/'.$params['g'].'.php';
				if(file_exists($file))
					unlink($file);
				
				$this->_refreshACL();
				return Jax_Response::Valid(1);
			}
			return Jax_Response::Error("Unable to remove group.");
		}
		return Jax_Response::Error("Invalid Request");
	}
	
	public function getGroupInfoProc($group){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_READ);
		
		$info = Jax_Data_Source::getInstance()->getRecord("AclRoles","`role`='$group'");
		
		foreach ($info as &$v){
			if(!is_null($v))
				$v = stripslashes($v);
		}

		return Jax_Response::Valid($info);
	}
	
	public function getGroupMembersProc($group){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_READ);
		
		$acl = Jax_Acl::getInstance()->acl();
		
		$roles = $acl->getRoles();
		
		$list = array();
		foreach ($roles as $role){
			$roleObject = $acl->getRole($role);
		
			if (($roleObject instanceof Jax_Acl_Role) && $acl->inheritsRole($role, $group,true) && !$roleObject->isGroup()){
				$ua = $roleObject->getUserObject();
				if($ua)
					$list[$role] = $ua;
			}
		}
		return Jax_Response::Valid($list);
	}
	
	protected function _delGroupPerm($params){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_DELETE);

		$role = @$params['role'];
		$module = @$params['resource'];
		$access = @$params['perm'];
		
		if(Jax_Data_Source::getInstance()->manageUserPerms(Jax_Acl_Constants::ACCESS_DELETE,$role,$module,$access,null,true)){
			Jax_System_Logger::log(new Jax_LogEntry(
			Jax_LogEntry::LOG_CATEGORY_ACL,
			$this->_resourceId,
			Jax_Auth::getAuthId()." removed permission [$access] on $module from $role",
			Jax_Acl_Constants::ACCESS_DELETE));
		
			$this->_refreshACL();
			return Jax_Response::Valid("1");
		}
		
		return Jax_Response::Error("Unable to remove group permission.");
	}
	
	protected function _addGroupPerm($params){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_WRITE);
	
		$role = @$params['role'];
		$module = @$params['resource'];
		$access = @$params['perm'];
		$type = @$params['atype'];
		($type == 'Deny')?$type = "0":null;
		($type == 'Allow')?$type = "1":null;

		if(Jax_Data_Source::getInstance()->manageUserPerms(Jax_Acl_Constants::ACCESS_WRITE,$role,$module,$access,$type,true)){
			Jax_System_Logger::log(new Jax_LogEntry(
			Jax_LogEntry::LOG_CATEGORY_ACL,
			$this->_resourceId,
			Jax_Auth::getAuthId()." added permission [$access] on $module to $role",
			Jax_Acl_Constants::ACCESS_WRITE));
	
			$this->_refreshACL();
			return Jax_Response::Valid("1");
		}
	
		return Jax_Response::Error("Unable to add group permission.");
	}
	
	protected function groupPermsProc(Zend_Controller_Request_Abstract $request){
		$params = $request->getParams();
		
		// For delete requests
		if ($request->isPost() && $request->getParam('access') == Jax_Acl_Constants::ACCESS_DELETE)
			return $this->_delGroupPerm($request->getParams());
			
		// For write requests
		if ($request->isPost() && $request->getParam('access') == Jax_Acl_Constants::ACCESS_WRITE)
			return $this->_addGroupPerm($request->getParams());
		
		if (!isset($params['role']) || !isset($params['resource'])) return Jax_Response::Error("Invalid request!");
		
		$role = @$params['role'];
		$module = @$params['resource'];
	
		$rolePerms = Jax_Acl::getInstance()->getACLByGroup($role);
		$perms = array();
		
		if (array_key_exists($module, $rolePerms)){
			$perms = $rolePerms[$module];
		}
		
		// Get inherited perms
		$types = Jax_Data_Source::getInstance()->getACLAccessTypes();
		$dbModRules = Jax_Data_Source::getInstance()->getRoleRules();
		
		$resH = Jax_Acl::getInstance()->getResourceHierarchy();
		$path = Jax_Utilities_ArraySearchRecursive::run($module,$resH,true,array(),true);
		foreach ($path as $i=>$v) if ($v=='children') unset($path[$i]);
		array_pop($path);
		
		$dbRules = array();
		foreach ($dbModRules as $modRule){
			$dbRules[$modRule['role']][$modRule['module']][$modRule['access']] = $modRule['allow'];
		}
		
		foreach ($types as $atype){
			if (Jax_Acl::getInstance()->acl()->isAllowed($role,$module,$atype['access'])){
				if (!isset($perms[$atype['access']]))
					$perms[$atype['access']." (I)"] = "1";
			} else {
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
}