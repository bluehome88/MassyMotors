<?php
/**
 * Main bootstrap class.
 * Used to initialize application wide settings.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-zendapplication
 * @package Jax
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{	
	/**
	 * Initializes the Zend_View object.
	 * This view object is customized based on application settings from the .ini file for the app.
	 * 
	 * @return Zend_View
	 */
	protected function _initView(){
		$session = new Zend_Session_Namespace(APPNAMESPACE);
		
		$options = $this->getOptions();
		$bootstrapOptions = $session->{Jax_System_Constants::SYSTEM_SESSIONKEY_OPTIONS};
		$app = $bootstrapOptions[Jax_System_Constants::CONFIG_PARENT_NAMESPACE];

		$view = new Zend_View($options['resources']['view']);
		if (isset($options['resources']['view']['doctype'])) $view->doctype($options['resources']['view']['doctype']);
		
		$scriptPaths = array(
			APPLICATION_PATH.'/../library/Jax/_views'
		);
		
		$libPath = APPLICATION_PATH."/../library/";
		$libs = opendir($libPath);
		if($libs){
			while (false !== ($dir = readdir($libs))){
				if ($dir == APPNAMESPACE || $dir == 'Jax') continue;
				$libViewDir = $libPath.$dir."/_views";
				if(is_dir($libViewDir)){
					$scriptPaths[] = $libViewDir;
				}
			}
			closedir($libs);
		}
		$view->setScriptPath(array_merge($scriptPaths,$view->getScriptPaths()));

		$request = new Zend_Controller_Request_Http();
		defined('BASEURL') || define('BASEURL',$request->getBasePath().'/');
		defined('APPASSETS') || define('APPASSETS', BASEURL.'_assets/js/');
		
		$view->date = date("l dS F, Y");
		$view->{JAX_OPTIONS_APP_IMAGEPATH} = BASEURL.$app[JAX_OPTIONS_APP_IMAGEPATH];
		
		$view->headTitle($app[JAX_OPTIONS_APP_SHORTNAME]);
		$view->{JAX_OPTIONS_APP_SHORTNAME} = $app[JAX_OPTIONS_APP_SHORTNAME];
		$view->{JAX_OPTIONS_APP_FULLNAME} = $app[JAX_OPTIONS_APP_FULLNAME];
		$view->{JAX_OPTIONS_APP_COMPANY} = $app[JAX_OPTIONS_APP_COMPANY];
		$view->copyrights = '&copy; Copyright '.date('Y').' '.$view->{JAX_OPTIONS_APP_COMPANY}.'. All rights reserved.';
		
		defined('JAX_VIEW_COPYRIGHTS') || define('JAX_VIEW_COPYRIGHTS', $view->copyrights);
		
		// Set CSS files to load
		foreach ($app[JAX_OPTIONS_APP_CSS] as $cssFile)
       	 $view->headLink()->appendStylesheet(BASEURL.$app[JAX_OPTIONS_APP_CSSPATH].$cssFile);
        
        // Set JS files to load
        foreach ($app[JAX_OPTIONS_APP_JS] as $jsFile)
       		$view->headScript()->appendFile(BASEURL.$app[JAX_OPTIONS_APP_JSPATH].$jsFile);
        
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
 
        return $view;
	}
	
	/**
	 * Initializes the cache manager and loads into session for use within the application.
	 * 
	 * @return null
	 */
	protected function _initCache()
	{
		$session = new Zend_Session_Namespace(APPNAMESPACE);

		if (!isset($session->{Jax_System_Constants::SYSTEM_SESSIONKEY_CACHEMANAGER})){
			$CacheManager = $this->bootstrap('cachemanager')->getResource('cachemanager');
			$session->{Jax_System_Constants::SYSTEM_SESSIONKEY_CACHEMANAGER} = $CacheManager;
		}
	}
	
	/**
	 * Initializes the *_Data_Source object for remote data access.
	 * If this class is defined it is automatically loaded, else data source functionality will not be available.
	 * 
	 * @return null
	 */
	protected function _initDataSource(){
		if(APPNAMESPACE != "Jax"){
			$dataSourceClass = APPNAMESPACE."_Data_Source";
			if(@class_exists($dataSourceClass)){
				Jax_Data_Source::getInstance()->setDataSourceWrapper(new $dataSourceClass);
			}
		}
	}
	
	/**
	 * Stores the application configuration file in Zend_Registry for access within the application
	 *
	 * @return null
	 */
	protected function _initConfig(){
		$file = APPLICATION_PATH."/../library/".APPNAMESPACE."/".APPNAMESPACE.".ini";
		$config = parse_ini_file($file,true);
		Zend_Registry::set(Jax_System_Constants::SYSTEM_REGKEY_APPCFG,$config);
	
		Zend_Barcode::setBarcodeFont(APPLICATION_PATH.'/../library/Jax/System/Fonts/arial_0.ttf');
	
		// Define user options as constants (if any)
		$cName = APPNAMESPACE."_App_Options";
		if (@class_exists($cName,true)){
			$cName::init($cName);
		} else {
			Jax_App_Options::init();
		}
	}
	
	/**
	 * Initializes the ACL object for controlling resource access.
	 * If a *_Acl_Config class is defined it is automatically loaded. 
	 * If not, ACL functionality will not be available.
	 * 
	 * @return null
	 */
	protected function _initACL()
	{
		if(APPNAMESPACE != "Jax"){
			$session = new Zend_Session_Namespace(APPNAMESPACE);
			if(Zend_Auth::getInstance()->hasIdentity()){
				Jax_Acl::restore();
				
				$role = Jax_User::getInstance()->setUser(Jax_Auth::getAuthId())->getUserObject(true);
				if(@$role->sys_disabled == 1){
					return Jax_Auth::logout('Account is disabled',3);
				}
			}
		}
	}
}