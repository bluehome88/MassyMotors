<?php
class Hilo_ValentinesiController extends Zend_Controller_Action
{
	protected $_responseHandler;
		
	public function init(){
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
		
		$this->_helper->layout()->disableLayout();
	
		if(!Jax_Auth::verify()){
			$this->_helper->redirector("logout","Auth","Jax");
		}
	
		Jax_Utilities_ControllerAccessChecker::run(Hilo_Acl_Constants::RESOURCE_VALENTINESI,Jax_Acl_Constants::ACCESS_READ);
	}
				
	public function indexAction() {
		$this->view->entries = Jax_Data_Source::getInstance()->getRecord("ViewVal2014",null,true);
	}
	
	public function viewAction(){
		$params = $this->getRequest()->getParams();
		
		if(isset($params['eid'])){
			$eid = $params['eid'];
			
			$this->view->entry = Jax_Data_Source::getInstance()->getRecord("Val2014","`id`=\"$eid\"");
			
		}
	}
}