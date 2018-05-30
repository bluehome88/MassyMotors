<?php
class Hilo_SsdonateController extends Zend_Controller_Action
{
	protected $_responseHandler;
		
	public function init(){
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
		
		$this->_helper->layout()->disableLayout();
	
		if(!Jax_Auth::verify()){
			$this->_helper->redirector("logout","Auth","Jax");
		}
	
		Jax_Utilities_ControllerAccessChecker::run(Hilo_Acl_Constants::RESOURCE_SSDONATE,Jax_Acl_Constants::ACCESS_READ);
	}
				
	public function indexAction() {
		$this->view->pending = Jax_Data_Source::getInstance()->getRecord("ViewSSDonate","`processed` IS NULL",1,array('requested'));
	}
	
	public function detailsAction(){
		$params = $this->getRequest()->getParams();
		if(!isset($params['did'])) {
			$this->view->error = "Invalid Request";
		} else {
			$this->view->data = Jax_Data_Source::getInstance()->getRecord("ViewSSDonate","`id`=\"".intval($params['did'])."\"");
		}
	}
}