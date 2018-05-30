<?php
class Jax_User_Search {
	public static function partial($query){
		$acl = Jax_Acl::getInstance()->acl();
		$roles = $acl->getRoles();
		
		$list = array();
		foreach ($roles as $role){
			$roleObject = $acl->getRole($role);
	
			if (($roleObject instanceof Jax_Acl_Role) && $roleObject->partialSearch($query)){
				$list[$role] = $roleObject->getUserObject();
			}
		}
	
		return $list;
	}
}