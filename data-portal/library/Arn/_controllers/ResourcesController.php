<?php
class Arn_ResourcesController extends Zend_Controller_Action {
	protected $_responseHandler;
	
	public function init(){
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
	
		$this->_helper->layout()->disableLayout();
		if(!Jax_Auth::verify()){
			$this->_helper->redirector("logout","Auth","Jax");
		}
	}
	
	public function loadAction() {
		$params = $this->getRequest()->getParams();
		$aclAccess = new Jax_Acl_Access();
		
		if (isset($params['m'])) {
			$m = $params['m'];
			
			$s = Jax_Acl::getInstance()->getResourceHierarchy($m);
			
			foreach ($s['children'] as $r=>$c){
				if (!Jax_Acl::getInstance()->acl()->isAllowed(Jax_Auth::getAuthId(),$r,Jax_Acl_Constants::ACCESS_RENDER)){
					unset($s['children'][$r]);
				}
			}
			
			$this->view->resources = $s['children'];
		} else {
			$this->view->resources = $aclAccess->getAllowedResources(Jax_Acl_Constants::ACCESS_RENDER);
		}
	}
}
