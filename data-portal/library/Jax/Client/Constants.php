<?php
/**
 * Constants used within Jax_Clients.
 * These options are returned along with the Jax installation specific values to Jax (JS) client via:
 * 
 * ../public/Jax/client/options
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Client_Constants
{
	/**
	 * Path from webserver root where Jax is installed.
	 * 
	 * @var string
	 */
	const OPTIONS_BASEURL = 'baseUrl';
	
	/**
	 * The display name of the application (defined in the applications' .ini file)
	 * 
	 * @var string
	 */
	const OPTIONS_APPNAME = 'appname';
	
	/**
	 * Copyright statement. This has a default server side value but can be changed by Jax JS programatically.
	 * 
	 * @var string
	 */
	const OPTIONS_COPYRIGHTS = 'copyrights';
	
	/**
	 * File name of the applications' logo
	 * 
	 * @var string
	 */
	const OPTIONS_LOGO = 'logo';
	
	/**
	 * Path the to the default image directory
	 * 
	 * @var string
	 */
	const OPTIONS_IMAGEPATH = 'imagePath';
	
	/**
	 * Path to JavaScript libraries on the server. Jax JS should be installed here.
	 * 
	 * @var string
	 */
	const OPTIONS_JSPATH = 'jsPath';
	
	/**
	 * Path to CSS files on the server.
	 * 
	 * @var string
	 */
	const OPTIONS_CSSPATH = 'cssPath';
	
	
	const OPTIONS_AUTH = 'auth';
}