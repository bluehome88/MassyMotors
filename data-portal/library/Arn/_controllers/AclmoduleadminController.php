<?php
class Arn_AclmoduleadminController extends Zend_Controller_Action {
	protected $_responseHandler;
	
	public function init(){
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
		
		$this->_helper->layout()->disableLayout();
		
		if(!Jax_Auth::verify()){
			$this->_helper->redirector("logout","Auth","Jax");
		}
		
		//Jax_Utilities_ControllerAccessChecker::run(Jax_Acl_Constants::RESOURCE_CP_ACL_MODULEADMIN,Jax_Acl_Constants::ACCESS_READ);
	}
	
	public function allModulesAction(){
		$modules = Jax_Acl::getInstance()->acl()->getResources();
		$acl = Jax_Acl::getInstance()->acl();
		foreach($modules as $i=>$m){
			if(!$acl->isAllowed(Jax_Auth::getAuthId(),$m,Jax_Acl_Constants::ACCESS_READ)) unset($modules[$i]);
		}
		$this->view->modules = $modules;
	}
	
	public function allGroupsAction(){
		$ACLGroups = Jax_Acl::getInstance()
			->acl()
			->get(Jax_Acl_Constants::RESOURCE_CP_ACL_GROUPADMIN)
			->getACLGroupsProc();
		
		$this->view->groups = $ACLGroups[Jax_Response::KEY_RESPONSE];
	}
	
	public function moduleAdminAction(){
		$this->view->ACLModules = Jax_Acl::getInstance()->acl()->getResources();
	}
	
	public function moduleDetailsAction(){
		$params = $this->getRequest()->getParams();
		
		$mod = trim($params['m']);
		
		$Moduleadmin = Jax_Acl::getInstance()->acl()->get(Jax_Acl_Constants::RESOURCE_CP_ACL_MODULEADMIN);
		$info = $Moduleadmin->getModuleInfoProc($mod);
		$mods = Jax_Acl::getInstance()->acl()->getResources();
		
		
		$this->view->module = $mod;
		$this->view->info = $info[Jax_Response::KEY_RESPONSE];
		$this->view->modules = $mods;
	}
	
	public function subModsAction(){
		$params = $this->getRequest()->getParams();
		
		if(!isset($params['m'])){
			$this->view->error = "Invalid Request";
		} else {
		
			$mod = $params['m'];
			$acl = Jax_Acl::getInstance()->acl();
			
			$modules = Jax_Acl::getInstance()->acl()->getResources();
			foreach($modules as $i=>$module){
				if(!$acl->inherits($module, $mod)) unset($modules[$i]);
			}
			
			$this->view->modules = $modules;
		}
	}
}
