<?php
class Hilo_CustomersController extends Zend_Controller_Action
{
	protected $_responseHandler;
		
	public function init(){
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
		
		$this->_helper->layout()->disableLayout();
	
		if(!Jax_Auth::verify()){
			$this->_helper->redirector("logout","Auth","Jax");
		}
	
		Jax_Utilities_ControllerAccessChecker::run(Hilo_Acl_Constants::RESOURCE_CUSTOMERS,Jax_Acl_Constants::ACCESS_READ);
	}
				
	public function indexAction() {
				
	}
	
	public function viewAction() {
		$params = $this->getRequest()->getParams();
		if(!isset($params['cust'])) {
			$this->view->error = "Invalid Request";
		} else {
			$custs = Jax_Acl::getInstance()->acl()->get(Hilo_Acl_Constants::RESOURCE_CUSTOMERS);
			$cust = $custs->_read($params,$this->getRequest());
	
			if(array_key_exists(Jax_Response::KEY_RESPONSE, $cust)){
				$this->view->cust = $cust[Jax_Response::KEY_RESPONSE];
			} else {
				$this->view->error = $cust[Jax_Response::KEY_ERROR];
			}
		}
	}
}