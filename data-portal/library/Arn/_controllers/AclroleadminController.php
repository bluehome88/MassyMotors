<?php
class Arn_AclroleadminController extends Zend_Controller_Action {
	protected $_responseHandler;
	
	public function init(){
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
		
		$this->_helper->layout()->disableLayout();
		
		if(!Jax_Auth::verify()){
			$this->_helper->redirector("logout","Auth","Jax");
		}
		
		Jax_Utilities_ControllerAccessChecker::run(Jax_Acl_Constants::RESOURCE_CP_ACL_ROLEADMIN,Jax_Acl_Constants::ACCESS_READ);
	}
	
	public function roleAdminAction() {
		$ACLGroups = Jax_Acl::getInstance()
		->acl()
		->get(Jax_Acl_Constants::RESOURCE_CP_ACL_GROUPADMIN)
		->getACLGroupsProc();
		
		$this->view->ACLGroups = $ACLGroups[Jax_Response::KEY_RESPONSE];
	}
	
	public function roleAdminViewAction(){
		$params = $this->getRequest()->getParams();
		if(isset($params['u'])){
			$roleObject = Jax_Acl_Resource_Controlpanel_Acladmin_Roleadmin::getUserRole($params['u']);
			
			if(is_array($roleObject)){
				$this->view->roleObject = $roleObject;
				$this->view->user = $params['u'];
				
				$ACLGroups = Jax_Acl::getInstance()
					->acl()
					->get(Jax_Acl_Constants::RESOURCE_CP_ACL_GROUPADMIN)
					->getACLGroupsProc();
				
				$this->view->ACLGroups = $ACLGroups[Jax_Response::KEY_RESPONSE];
				
			} else {
				$this->view->error = 'Unable to fetch user info.';
			}
		} else {
			$this->view->error = 'User not specified.';
		}
	}
	
	public function roleAdminAccessAction(){
		$params = $this->getRequest()->getParams();
		if(isset($params['u'])){
			$modules = Jax_Acl::getInstance()->acl()->getResources();
			$acl = Jax_Acl::getInstance()->acl();
			foreach($modules as $i=>$m){
				if(!$acl->isAllowed(Jax_Auth::getAuthId(),$m,Jax_Acl_Constants::ACCESS_READ)) unset($modules[$i]);
			}
	    	$roleObject = Jax_Acl_Resource_Controlpanel_Acladmin_Roleadmin::getUserRole($params['u']);
	    	
	    	if (is_array($roleObject)) {
	    		$this->view->roleObject = $roleObject;
	    		$this->view->role = $params['u'];
	    		$this->view->modules = $modules;
	    	} else {
	    		$this->view->error = 'Unable to fetch user role. ('.__CLASS__.' ['.__LINE__.'])';
	    	}
		} else {
			$this->view->error = 'User not specified.';
		}
	}
	
}