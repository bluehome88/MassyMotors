<?php
/**
 * Zend Framework specific class
 * Handles all framework exceptions
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_ErrorController extends Zend_Controller_Action
{
	/**
	 * Response handler class for returning request responses.
	 * 
	 * @var Jax_Response_Handler
	 */
	private $_responseHandler;
	
    public function errorAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);

    	$errors = $this->_getParam('error_handler');
        
        if (!$errors) {
           	$this->_responseHandler->setResponseData(Jax_Response::Error('You have reached the error page'))
           		->send();
            return;
        }
                
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
        	
        	// Added 130404-0025. Needed for working with MetroJ history API implementation.
        	if($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER ||
        			$errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION) {
        		//$this->_helper->redirector("index","index","Jax",array('r'=>$this->getRequest()->getParam('controller')));
        		setcookie("MOJ-R",$this->getRequest()->getParam('controller'));
        		header("Location: ".BASEURL."?mj-r=".$this->getRequest()->getParam('controller'));
        	}
        	
            $exception = $errors->exception;
            $this->_responseHandler->setResponseData(Jax_Response::Exception($exception))->send();
            return;
        } else {
       		switch ($errors->type) {
	            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
	            	
	            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
	            	$this->_helper->redirector("","","Jax",array('r'=>$this->getRequest()->getParam('controller')));
	            	return;
	            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
               	$this->_responseHandler->setResponseData(Jax_Response::Error("404 - Page not found"))->send();
                return;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->_responseHandler->setResponseData(Jax_Response::Error('Application Error (Error_Controller)'))->send();
               return;
      	  }
        }
    }
}

