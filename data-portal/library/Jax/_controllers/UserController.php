<?php
/**
 * Enables RESTful access to a user object.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_UserController extends Zend_Controller_Action
{
	/**
	 * The application session namespace
	 * 
	 * @var Zend_Session_Namespace
	 */
	private $_session;
	
	/**
	 * User specific class
	 * 
	 * @var (namespace)_User_(role)
	 */
	private $_userContext;
	
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
		// Disable rendering of the layout, we are returning data only (JSON/XML etc)
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		// Set data type to return
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
		
		if (!Jax_Auth::verify())
		{
			$this->_helper->redirector('logout','Auth','Jax');
		}
		
		// Get authenticated user type (See library/NAMESPACE/User/ for user classes)
		$UserType = Jax_Auth::getAuthLevel();
		
		$UserContext = APPNAMESPACE."_User_".$UserType;
		$this->_userContext = new $UserContext;
	}

	/**
	 * Default action is to return the entire user object.
	 * URL: /Jax/user
	 * 
	 * @return null
	 */
    public function indexAction()
    {
    	$this->_responseHandler
    		->setResponseData($this->_userContext->getUserObject())
    		->send();
    		
    	return;
    }
    
    public function searchAction(){
    	$params = $this->getRequest()->getParams();
    	$query = @$params['query'];
    	
    	$this->_responseHandler
    		->setResponseData(Jax_Response::Valid(Jax_User_Search::partial($query)))
    		->send();
    	
    	return;
    }

    /**
     * Magic method, enabling RESTful access to the user object.
     * 
     *  URL: /Jax/user/xxx/xxx/xxx
     *  
     *  Analysis and returning of the appropriate parts of the user object is handled by Jax_User_Abstract
     *  
     *  @return null
     */
    public function __call($methodName, $args)
    {
    	$propertyName = substr($methodName, 0,strlen($methodName)-6);
    	
    	$response = $this->_userContext->handleRequest($this->getRequest());
    	
    	if (!is_null($response)){
    		$this->_responseHandler
	    		->setResponseData($response)
	    		->send();
    	}	
    	return;
    }
}
