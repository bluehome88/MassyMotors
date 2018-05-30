<?php
/**
 * Controller class for handoff to Jax_System_Admin for creation, modification and removal of applications within the Jax installation.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_ApplicationsController extends Zend_Controller_Action
{
	/**
	 * Response handler class for returning request responses.
	 * 
	 * @var Jax_Response_Handler
	 */
	private $_responseHandler;
	
	/**
	 * Initialization of the controller class.
	 * 
	 * @see Zend_Controller_Action::init()
	 * @return null
	 */
	public function init()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
	}
	
	/**
	 * Action for creating a new application.
	 * Handoffs to Jax_System_Admin for actual creation.
	 * URL: /Jax/applications/create
	 * 
	 * @return null
	 */
	public function createAction(){
		
		// Check for Jax admin session and a POST request
		if(Jax_Auth::isJaxAdminSession() && $this->getRequest()->isPost()){
			
			$params = $this->getRequest()->getParams();
			
			// Retrieve config params from the HTTP request
			if(isset($params['cfg'])){
				$cfg = Zend_Json_Decoder::decode($params['cfg']);
			
				// Attempt application creation
				$appResult = Jax_System_Admin::createApp($cfg);
				
				if($appResult === true){
					$this->_responseHandler
						->setResponseData(Jax_Response::Redirect('./','Application Created!'))
						->send();
				} else {
					$this->_responseHandler
						->setResponseData(Jax_Response::Redirect('./','Unable to create application! ('.$appResult.')'))
						->send();
				}
			} else {
				$this->_responseHandler
				->setResponseData(Jax_Response::Error('Invalid Request'))
				->send();
			}			
		} else {
			$this->_responseHandler
				->setResponseData(Jax_Response::Error('Unauthorized Request'))
				->send();
		}
	}
	
	/**
	 * Action for removing an application.
	 * Handoffs to Jax_System_Admin for actual creation.
	 * URL: /Jax/applications/remove
	 * 
	 * @return null
	 */
	public function removeAction(){
		if(Jax_Auth::isJaxAdminSession() && $this->getRequest()->isPost()){
			$params = $this->getRequest()->getParams();
			if(isset($params['namespace'])){
				
				$delResult = Jax_System_Admin::removeApp($params['namespace']);
				
				if ($delResult === true){
					$this->_responseHandler
						->setResponseData(Jax_Auth::logout("Application Deleted! (".$params['namespace'].")"))
						->send();
				} else {
					$this->_responseHandler
						->setResponseData(Jax_Response::Error('Unable to remove application! ('.$delResult.')'))
						->send();
				}
				
			} else {
				$this->_responseHandler
				->setResponseData(Jax_Response::Error('Invalid Request'))
				->send();
			}
		} else {
			$this->_responseHandler
				->setResponseData(Jax_Response::Error('Unauthorized Request'))
				->send();
		}
	}
}