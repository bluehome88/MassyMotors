<?php
class Arn_AclgroupadminController extends Zend_Controller_Action {
	protected $_responseHandler;
	
	public function init(){
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
		
		$this->_helper->layout()->disableLayout();
		
		if(!Jax_Auth::verify()){
			$this->_helper->redirector("logout","Auth","Jax");
		}
		
		Jax_Utilities_ControllerAccessChecker::run(Jax_Acl_Constants::RESOURCE_CP_ACL_GROUPADMIN,Jax_Acl_Constants::ACCESS_READ);
	}
	
	public function groupAdminAction(){
		$ACLGroups = Jax_Acl::getInstance()
			->acl()
			->get(Jax_Acl_Constants::RESOURCE_CP_ACL_GROUPADMIN)
			->getACLGroupsProc();
		
		if (array_key_exists(Jax_Response::KEY_RESPONSE, $ACLGroups)){
			$this->view->ACLGroups = $ACLGroups[Jax_Response::KEY_RESPONSE];
		} else {
			$this->view->ACLGroups = array();
		}
	}
	
	public function groupDetailsAction(){
		$params = $this->getRequest()->getParams();
		
		$group = $params['m'];
		
		$Groupadmin = Jax_Acl::getInstance()->acl()->get(Jax_Acl_Constants::RESOURCE_CP_ACL_GROUPADMIN);
		$info = $Groupadmin->getGroupInfoProc($group);
		$groups = $Groupadmin->getACLGroupsProc();
		
		
		$this->view->group = trim($group);
		$this->view->info = $info[Jax_Response::KEY_RESPONSE];
		$this->view->groups = $groups[Jax_Response::KEY_RESPONSE];
	}
	
	public function groupMembersAction(){
		$params = $this->getRequest()->getParams();
		$group = $params['g'];
		
		$Groupadmin = Jax_Acl::getInstance()->acl()->get(Jax_Acl_Constants::RESOURCE_CP_ACL_GROUPADMIN);
		$members = $Groupadmin->getGroupMembersProc($group);
		
		$this->view->members = $members[Jax_Response::KEY_RESPONSE];
	}
}
