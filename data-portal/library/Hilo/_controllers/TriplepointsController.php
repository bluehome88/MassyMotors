<?php
class Hilo_TriplepointsController extends Zend_Controller_Action
{
	protected $_responseHandler;
		
	public function init(){
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
		
		$this->_helper->layout()->disableLayout();
	
		if(!Jax_Auth::verify()){
			$this->_helper->redirector("logout","Auth","Jax");
		}
	
		Jax_Utilities_ControllerAccessChecker::run(Hilo_Acl_Constants::RESOURCE_TRIPLEPOINTS,Jax_Acl_Constants::ACCESS_READ);
	}
				
	public function indexAction() {
		$Tp = Jax_Acl::getInstance()->acl()->get(Hilo_Acl_Constants::RESOURCE_TRIPLEPOINTS);
		$promos = $Tp->listTcpPromos();
		
		$this->view->promos = $promos;
	}
	
	public function optionsAction(){
		$params = $this->getRequest()->getParams();
		if(isset($params['p'])){
			$tbl = @mysql_escape_string($params['p']);
			
			$sess = new Zend_Session_Namespace();
			$sess->TCPTBL = $tbl;
			
		} else {
			$this->view->error = "Invalid Request";
		}
	}
}