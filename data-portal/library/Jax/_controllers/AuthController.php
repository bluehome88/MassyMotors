<?php
/**
 * Authentication Controller
 * Defines urls for authentication specific tasks.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_AuthController extends Zend_Controller_Action
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
	 * Processes a login request OR returns an error if already logged in.
	 * URL: /Jax/auth/login-process
	 * 
	 * @return null
	 */
    public function loginProcessAction()
    {
    	// Verifies auth session, returns error if session already exists
    	if (Jax_Auth::verify()) {
    		$response = Jax_Response::Error('User '.Jax_Auth::getAuthId().' already logged in! Please close your browser and try again.', '3');
    		
    		$this->_responseHandler
	    		->setResponseData($response)
	    		->send();
	    		
	    	return ;
    	}
    	
    	// Checks for a $_POST request,
    	// Attempts authentication
   		if ($this->getRequest()->isPost())
    	{
    		$credentials = $this->getRequest()->getParams();
    		$credentials[Jax_Auth_Constants::FIELD_USERNAME] = strtolower($credentials[Jax_Auth_Constants::FIELD_USERNAME]);
    		
    		// Initialize the authentication adapter
    		$AuthAdapterClass = APPNAMESPACE."_Auth_Adapter";
    		$authAdapter = new $AuthAdapterClass;
    		$authAdapter->setCredentials($credentials);
    		
    		// Attempt authentication
    		$loginResponse = Jax_Auth::login($authAdapter);
    		
    		// Redirect to applications Controller class for mobiles
    		if(isset($credentials['jax-mobile'])){
	    		$this->_helper->redirector("index","mobile",APPNAMESPACE);
    		}
    		
    		$this->_responseHandler
	    		->setResponseData($loginResponse)
	    		->send();
    		
    	} else {
    		$response = Jax_Response::Error('Credentials not received!','-1');
    		
    		$this->_responseHandler
	    		->setResponseData($response)
	    		->send();
	    		
	    	return ;
    	}
    }
    
    /**
     * Processes a logout request
     * URL: /Jax/auth/logout
     * 
     * @return null
     */
    public function logoutAction()
    {
    	$params = $this->getRequest()->getParams();
    	if(isset($params['delay'])) {
    		$delay = $params['delay'];
    	} else {
    		$delay = 3;
    	}
    	
    	$logoutResponse = Jax_Auth::logout(null,$delay);
    	$this->_responseHandler
    		->setResponseData($logoutResponse)
    		->send();
    		
    	return;
    }
    
    /**
     * Returns the current session token.
     * 
     * @return null
     */
    public function currentSessionAction(){
	    $this->_responseHandler
	    	->setResponseData(Jax_Response::Valid(Jax_Auth::getAuthToken()))
	    	->send();
	    	
	    return;
    }
    
    /**
     * Verifies the Jax admin password.
     * 
     * @return null
     */
    public function jaxAdminAction(){
    	if ($this->getRequest()->isPost()){
	    	$params = $this->getRequest()->getParams();
	    	if(isset($params['pwd'])){
	    		$pwd = $params['pwd'];
	   
	    		$this->_responseHandler
	    				->setResponseData(Jax_Auth::verifyJaxAdmin($pwd))
	    				->send();
	    		
	    				return;
	    	} 
   		} 
   		$this->_responseHandler
	    	->setResponseData(Jax_Response::Error("Invalid"))
	    	->send();
    }
}
