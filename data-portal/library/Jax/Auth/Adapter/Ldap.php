<?php
/**
 * Custom Active Directory Authentication Adapter for Zend Framework written for Jax.
 * Uses adLDAP for authentication instead of the Zend_Auth_Adapter_Ldap class offered by Zend.
 * 
 * Parses LDAP config data from application .ini file and configures Zend_Auth_Adapter_Ldap
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
abstract class Jax_Auth_Adapter_Ldap extends Jax_Auth_Adapter_Abstract
{
	/**
	 * LDAP options as defined in the application .ini file
	 * 
	 * @var array
	 */
	protected $_config;
	
	/**
	 * Instance of the configured adLDAP class.
	 * 
	 * @var adLDAP Class
	 */
	protected $_adLDAP;
	
	/**
	 * Default Constructor
	 * 
	 * Parses LDAP configuration information from application .ini file
	 * 
     * @param  array  $options  (optional) An array of options for adLDAP configuration
	 */
	public function __construct(array $options = null){
		if (is_null($options)){
			$config = new Zend_Config_Ini(APPLICATION_PATH."/../library/".APPNAMESPACE."/".APPNAMESPACE.".ini","production");
			$LdapOptions = $config->ldap->{APPNAMESPACE}->toArray();
		} else {
			$LdapOptions = $options;
		}
		
		$this->_config = $LdapOptions;
		
		// Load adLDAP class
		@require_once 'Jax/adLDAP/adLDAP.php';
		
		// Set adLDAP options from configuration
		$adLDAPOptions = array(
			'account_suffix'=>"@".$this->_config['accountDomainName'],
			'base_dn'=>$this->_config['baseDn'],
			'domain_controllers'=>array(
				$this->_config['host']
			),
			'admin_username'=>$this->_config['adminUsername'],
			'admin_password'=>$this->_config['adminPassword'],
			'use_tls'=>$this->_config['useStartTls']
		);
		
		// Instantiate adLDAP
		$this->_adLDAP = new adLDAP($adLDAPOptions);
	}
	
	/**
	 * Process the authentication request
	 * @see Zend_Auth_Adapter_Interface::authenticate()
	 */
	public function authenticate(){
		if (!is_array($this->_credentials)) throw new Zend_Auth_Adapter_Exception('Credentials not set! '.__METHOD__);
		
		$username = $this->_credentials[Jax_Auth_Constants::FIELD_USERNAME];
		$password = $this->_credentials[Jax_Auth_Constants::FIELD_PASSWORD];
		
		try {
			// Authenticate
			$authResult = $this->_adLDAP->authenticate($username, $password);
			
			if ($authResult){
				if($this->success()){
					$this->_setAuthenticatedSession();
				} else {
					Jax_Auth::logout();
					return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $username,array('User defined failure.'));
				}
				
				return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,$username);
			} else {
				return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $username,array("Invalid Credentials"));
			}
		} catch (Exception $e) {
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $username,array($e->getMessage()));
		}
	}
}