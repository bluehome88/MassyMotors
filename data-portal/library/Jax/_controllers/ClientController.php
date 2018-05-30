<?php
/**
 * Used to return project specific options that are used to initialize/customize the client side Javascript. 
 * It specifies base urls for the client to access, such as the location of images, javascript css files, application name, copyright notices etc.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_ClientController extends Zend_Controller_Action
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
	 * URL: /Jax/client/index
	 * 
	 * @return null
	 */
    public function indexAction (){}
    
    /**
     * Returns a list of apps installed on the Jax installation.
	 * URL: /Jax/client/load-apps
	 * 
	 * @return null
     */
    public function loadAppsAction(){
    	$params = $this->getRequest()->getParams();
    	if(isset($params['metro'])){
    		$apps = Jax_ApplicationLoader::getApps(true);
    	} else {
    		$apps = Jax_ApplicationLoader::getApps();
    	}
    	
    	$this->_responseHandler
    		->setResponseData(Jax_Response::Valid($apps))
    		->send();
    		
    	return;
    }
    
    /**
     * Returns client options
	 * URL: /Jax/client/options
	 * 
	 * @return null
     */
    public function optionsAction()
    {
    	$this->_responseHandler
    		->setResponseData(Jax_Client_Options::getOptions())
    		->send();
    		
    	return;
    }
    
    /**
     * Returns current application options
     * URL: /Jax/client/application-options
     * 
     * @return null
     */
    public function applicationOptionsAction(){
    	$appIni = Zend_Registry::get(Jax_System_Constants::SYSTEM_REGKEY_APPCFG);
    	
    	$this->_responseHandler
    		->setResponseData(Jax_Response::Valid($appIni['remoteConfig']))
    		->send();
    		
    	return;
    }
    
    /**
     * Provides a list of available themes.
     * Also accepts a POST request to save a selected theme for the current user
	 * URL: /Jax/client/themes
	 * 
	 * @return null
     */
    public function themesAction()
    {
    	if ($this->getRequest()->isPost())
    	{
    		$params = $this->getRequest()->getParams();
    		if (isset($params['theme'])){
    			$theme = strtolower($params['theme']);
    			$response = Jax_System_Themes::saveTheme($theme);
    		} else {
    			$response = Jax_Response::Error('Theme not saved!');
    		}
    	} else {
    		$response = Jax_System_Themes::getThemes();
    	}
    	
    	$this->_responseHandler
    		->setResponseData($response)
    		->send();
    		
    	return;
    }
    
}