<?php
class Arn_UIController extends Zend_Controller_Action {
	
	protected $_responseHandler;
	
	public function init(){
		
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
		
		$this->_helper->layout()->disableLayout();
		if(!Jax_Auth::verify()){
			$this->_helper->redirector("logout","Auth","Jax");
		}
	}
	
	public function indexAction() {
		
	}
	
	public function searchUserAction(){
		
	}
	
	protected function _noAccess(){
		$this->_responseHandler->setResponseData(Jax_Response::Error("No Access"))->send();
		die();
	}
}
