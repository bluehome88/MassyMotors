<?php
class Jax_Data_Source_Db_Mysql 
	extends Jax_Data_Source_Db
{
	protected function getUserData($userId){
		$userTable = new Jax_Models_AuthUsers();
		$info = $userTable->fetchRow("`username`='".$userId."'");
		if (is_object($info)) $info = $info->toArray();
		
		return $info;
	}
	
	/**
	 * Retrieve list of roles from the database
	 * 
	 * @return array
	 */
	protected function getRoles(){
		$roles = new Jax_Models_AclRoles();
		$mainRoles = $roles->fetchAll()->toArray();
		foreach ($mainRoles as &$mr) $mr['isGroup'] = true;
		
		$userRolesTbl = new Jax_Models_AclUserRoles();
		$userRoles = $userRolesTbl->fetchAll()->toArray();
		
		foreach ($userRoles as $def){
			$mainRoles[] = $def;
		}
		
		return $mainRoles;
	}
	
	/**
	 * Retrieve list of modules from the database
	 * 
	 * @return array
	 */
	protected function getModuleList(){
		$modules = new Jax_Models_AclModules();
		return $modules->fetchAll()->toArray();
	}
	
	/**
	 * Retrieve dynamic acl rules from the database
	 * 
	 * @return array
	 */
	protected function getAclRoleRules(){
		$rules = $this->getRoleRules();
		$userRules = $this->getUserRules();
		
		foreach ($userRules as $rule){
			$rules[] = $rule;
		}
		
		foreach ($rules as $i=>$rule){
			$rules[] = new Jax_Acl_Rule($rule);
			unset($rules[$i]);
		}
		
		return $rules;
	}
	
	protected function getRoleRules(){
		$roleData = new Jax_Models_AclRoleModules();
		return $roleData->fetchAll(null,"allow DESC")->toArray();
	}
	
	protected function getUserRules(){
		$userData = new Jax_Models_AclUserModules();
		return $userData->fetchAll(null,"allow DESC")->toArray();
	}
	
	protected function getACLAccessTypes(){
		$tbl = new Jax_Models_AclModulesAccess();
		return $tbl->fetchAll()->toArray();
	}
	
	protected function log(Jax_LogEntry $logEntry,$username=null){
		$logTbl = new Jax_Models_SysLogs();
		
		$data = $logEntry->getLogData();
		
		if(isset($username) && !is_null($username)){
			$data['user'] = $username;
		} else {
			$data['user'] = Jax_Auth::getAuthId();
		}
		
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
		$data['role'] = Jax_Auth::getAuthLevel();
		
		return $logTbl->insert($data);
	}
	
	protected function manageUserRole($verb,$user,$role){
		$roleTbl = new Jax_Models_AclUserRoles();
		$db = $roleTbl->getAdapter();
		
		$user = mysql_escape_string($user);
		$role = mysql_escape_string($role);
		
		switch ($verb){
			case Jax_Acl_Constants::ACCESS_WRITE:
				if($db->query("INSERT IGNORE INTO ".$roleTbl->info("name")." (role,parent) VALUES('".$user."','".$role."')")) return true;
				break;
				
			case Jax_Acl_Constants::ACCESS_DELETE:
				$a = $roleTbl->fetchAll("`role`='$user'");
				if($a->count() < 2) return false;
				if($roleTbl->delete("`role`='$user' AND `parent`='$role'")) return true;
				break;
		}
	}
	
	protected function manageUserPerms($verb,$role,$resource,$access,$atype,$group = false){
		if($group) {
			$roleTbl = new Jax_Models_AclRoleModules();
		} else {
			$roleTbl = new Jax_Models_AclUserModules();
		}
		
		$role = trim(mysql_escape_string($role));
		$resource = trim(mysql_escape_string($resource));
		$access = trim(mysql_escape_string($access));
		
		switch ($verb){				
			case Jax_Acl_Constants::ACCESS_DELETE:
				if($roleTbl->delete("`role`='$role' AND `module`='$resource' AND `access`='$access'")) return true;
				break;
				
			case Jax_Acl_Constants::ACCESS_WRITE:
				try{
					if($roleTbl->insert(array('role'=>$role,'module'=>$resource,'access'=>$access,'allow'=>$atype))) return true;
				} catch(Exception $e){
					if($roleTbl->update(array('allow'=>intval($atype)),"`role`='$role' AND `module`='$resource' AND `access`='$access'")) return true;
				}
				break;
		}
	}
	
	public function updateOnDuplicate($table,array $updates){
		$tbl = $this->_checkTblName($table);
		$tblHandle = new $tbl;
		
		
		$query = "INSERT INTO ".$tblHandle->info("name")." (";

		$vals = "";
		$u = "";
		foreach($updates as $c=>$v){
			$query .= "`$c`,";
			$vals .= "\"".$v."\",";
			$u .= "`$c`=\"$v\",";
		}
		
		$u = substr($u, 0,strlen($u)-1);
		$vals = substr($vals, 0,strlen($vals)-1).")";
		$query = substr($query, 0,strlen($query)-1).") VALUES(".$vals." ON DUPLICATE KEY UPDATE ".$u;

		return Jax_Data_Source::getInstance()->getDb()->query($query);
		
		//return $stmt->execute();
	}
}