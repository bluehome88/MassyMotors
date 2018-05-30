<?php
class Jax_ACLRoleAdminController extends Zend_Controller_Action
{
	public function init(){
		if (!Jax_Auth::verify() || !Jax_Acl::getInstance()->acl()->isAllowed(Jax_Auth::getAuthId(),'Roleadmin',Jax_Acl_Constants::ACCESS_READ)){
			$this->_helper->redirector("logout","Auth","Jax");
		}
		
		$this->_helper->layout()->disableLayout();
	}
	
    public function indexAction ()
    {
        $this->_helper->redirector("index","index","Jax");
    }
    
    public function roleViewAction(){
    	$params = $this->getRequest()->getParams();
    	
    	if (!isset($params['role'])) $this->_redirect();
    	$role = $params['role'];
    	$roleObject = Jax_Acl_Resource_Controlpanel_Acladmin_Roleadmin::getUserRole($role);
    	
    	if (is_array($roleObject)) {
    		$this->view->roleObject = $roleObject;
    		$this->view->role = $role;
    		
    		$ACLGroups = Jax_Acl::getInstance()->getRoleHierarchy();
    		$this->view->ACLGroups = array();
    		foreach ($ACLGroups as $role=>$parents){
    			$ACLRole = Jax_Acl::getInstance()->acl()->getRole($role);
    			if (!$ACLRole->Role) $this->view->ACLGroups[] = $role;
    		}
    	} else {
    		$this->view->error = 'Unable to fetch user role. ('.__CLASS__.' ['.__LINE__.'])';
    	}
    }
    
    public function userModPermissionsAction(){
    	$params = $this->getRequest()->getParams();
    	
    	if (!isset($params['role'])) $this->_redirect();
    	$role = $params['role'];
    	
    	$modules = Jax_Acl::getInstance()->acl()->getResources();
    	$roleObject = Jax_Acl_Resource_Controlpanel_Acladmin_Roleadmin::getUserRole($role);
    	
    	if (is_array($roleObject)) {
    		$this->view->roleObject = $roleObject;
    		$this->view->role = $role;
    		$this->view->modules = $modules;
    	} else {
    		$this->view->error = 'Unable to fetch user role. ('.__CLASS__.' ['.__LINE__.'])';
    	}
    }
    
    protected function _redirect(){
    	$this->_helper->redirector("index","index","Jax");
    }
}
