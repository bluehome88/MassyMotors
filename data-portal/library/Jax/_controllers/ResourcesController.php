<?php
/**
 * Controller for handling access to application resources.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_ResourcesController extends Zend_Controller_Action
{
	/**
	 * The application session namespace
	 * 
	 * @var Zend_Session_Namespace
	 */
	private $_session;
	
	/**
	 * Jax Acl Access class used to query the ACL for resources based on defined access.
	 * 
	 * @var Jax_Acl_Access
	 */
	private $_aclAccess;
	
	/**
	 * Instance of the Access Control List class
	 * 
	 * @var Jax_Acl
	 */
	private $_acl;
	
	/**
	 * Jax Data Source class for accessing remote data.
	 * 
	 * @var Jax_Data_Source
	 */
	private $_dataSource;
	
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
		$this->_helper->viewRenderer->setNoRender(true);
		
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
		
		/**
		 * Only authenticated access is allowed. If no auth session is found, logout.
		 */
		if (!Jax_Auth::verify())
		{
			$this->_helper->redirector('logout','Auth','Jax');
		}
		
		$this->_session = new Zend_Session_Namespace(APPNAMESPACE);
		$this->_aclAccess = new Jax_Acl_Access();
		$this->_acl = Jax_Acl::getInstance();

		$this->_dataSource = Jax_Data_Source::getInstance();
	}
	
	/**
	 * Returns a list of resources that the user is allowed access to based on the type of access being queried.
	 * 
	 * E.g. 
	 * URL1: /Jax/resources/view/access/Read
	 * URL1: /Jax/resources/view/access/Update
	 * 
	 * The first url would return a list of resources that the user has "Read" access to.
	 * The second url would return a list of resources that the user has "Update" access to.
	 * 
	 * @return null
	 */
    public function viewAction ()
    {
    	$params = $this->getRequest()->getParams();
    	if (isset($params[Jax_Acl_Access::REQUEST_KEY_ACCESS])){
    		$access = $params[Jax_Acl_Access::REQUEST_KEY_ACCESS];
    		
    		$allowed = $this->_aclAccess->getAllowedResources($access);

    		$this->_responseHandler->setResponseData(
    			Jax_Response::Valid(array(Jax_Acl_Access::RESPONSE_KEY_RESOURCES=>$allowed))
			)->send();
			
			return;
    	} else {
    		$this->_responseHandler->setResponseData(
    			Jax_Response::Error('Please specify an access level.')
    		)->send();
    		
    		return;
    	}

    }
    
    /**
     * Magic Method used to provide RESTful access to resources. Performs handover of a request to the underlying resource class.
     * 
     * @return null
     */
    public function __call($methodName, $args)
    {    	
    	$moduleName = ucfirst(substr($methodName, 0,strlen($methodName)-6));
    	
    	try {
    		if ($this->_acl->acl()->has($moduleName)){
    				$resource = $this->_acl->acl()->get($moduleName);
    				
    				$this->_responseHandler->setResponseData(
	    				$resource
	    					->setDataAccessObject($this->_dataSource)
	    					->handleRequest($this->getRequest())
	    			)->send();
	    			
	    			return;
    			} else {
    				$this->_responseHandler->setResponseData(
    					Jax_Response::Error('Invalid Resource!')
    				)->send();
    				
    				return;
    			}
    	} catch (Exception $e)
    	{
    		$this->_responseHandler->setResponseData(
    			Jax_Response::Error($e->getMessage())
    		)->send();
    		
    		return;
    	}
    }
}
