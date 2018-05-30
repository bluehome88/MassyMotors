<?php
date_default_timezone_set('America/Port_of_Spain');

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path()
)));

$JaxAppCookie = explode(".",@$_COOKIE['Jax-Active-Application']);
$appName = $JaxAppCookie[0];
if(is_string($appName) && strlen($appName)>1){
	$JAXAPP = $appName;
} else {
	$JAXAPP = "Jax";
}

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/../library/'.$JAXAPP.'/'.$JAXAPP.'.ini'
);

// Set additional PHP Library Namespaces here to enable autoloading
$appNameSpaces = array('Jax_','FB_');
if($JAXAPP != "Jax"){
	$appNameSpaces[] = $JAXAPP.'_';
}
$application->setAutoloaderNamespaces($appNameSpaces);

$Config = new Jax_Config();
$Config
	->setApplicationName('MetroJ Applications')
	->setApplicationShortName('Mj')
	->setCompanyName('Nerds Consulting Limited')
	->setLogoImage('logo.png')
	->setPathCss('css/')
	->setPathImage('images/')
	->setPathJs('js/')
	->addJsFile('../libs/jQuery/jquery-1.9.1.min.js')
	->addJsFile('../libs/MetroJ/MetroJ.js')
;
// Setup System Constants
Jax_System_Constants::init($application,$Config);

$application->bootstrap()
            ->run();