<?php
/**
 * Custom Zend_Auth Abstract Adapter written for Jax.
 * 
 * All concrete authentication adapters must extend this class.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
abstract class Jax_Auth_Adapter_Abstract implements Zend_Auth_Adapter_Interface
{
	/**
	 * Array of login parameters passed by the client
	 * 
	 * @var array
	 */
	protected $_credentials;
	
	/**
	 * Sets credentials passed from the client.
	 * Must consist of "Jax-Auth-Username" and "Jax-Auth-Password".
	 * 
	 * @param array $credentials
	 * @throws Exception - If username or password fields are not defined in the credentials array.
	 * @return Jax_Auth_Adapter_Abstract
	 */
	public function setCredentials(Array $credentials = array()){
		if(!array_key_exists(Jax_Auth_Constants::FIELD_USERNAME, $credentials) ||
			!array_key_exists(Jax_Auth_Constants::FIELD_PASSWORD, $credentials) ||
			 !array_key_exists(Jax_Auth_Constants::FIELD_COOKIE, $credentials))
				throw new Exception("Mandatory credential field missing!", -1);
		
		$this->_credentials = $credentials;
		
		return $this;
	}
	
	/**
	 * Sets an authentication session for use by Jax_Auth.
	 * 
	 * @return boolean
	 */
	protected function _setAuthenticatedSession(){
		if (APPNAMESPACE == 'Jax') return false;
		Jax_Auth::setAuthToken($this->_generateAuthToken());
		Jax_Auth::setAuthCookie($this->_credentials[Jax_Auth_Constants::FIELD_COOKIE]);
		Jax_Auth::initJaxAcl($this->_credentials[Jax_Auth_Constants::FIELD_USERNAME]);

		$r = Jax_Auth::setAuthLevel($this->getUserRole($this->_credentials[Jax_Auth_Constants::FIELD_USERNAME]));
		
		$this->postAuth();
		
		return $r;
	}
	
	/**
	 * Default method for generating an authentication token.
	 * Concrete classes should override as necessary.
	 * 
	 * @return string $token
	 */
	protected function _generateAuthToken(){
		$sessId = session_id();
		$rand = rand();
		$user = $this->_credentials[Jax_Auth_Constants::FIELD_USERNAME];
		
		$t = implode("|", array($sessId,$rand,$user));

		return base64_encode($t);
	}
	
	/**
	 * Retrieve the user role.
	 * 
	 * @param string $username
	 * @return Zend_Acl_Role - Should return an instance of Zend_Acl_Role which is the users' role | string
	 */
	protected function getUserRole($username) {
		$user = Jax_Acl::getInstance()->acl()->getRole($username);
		
		if ($user instanceof Jax_Acl_Role){
			if(is_array($user->Role) && count($user->Role) > 0){
				return $user->Role[0];
			}
		}
		return null;
	}
	
	/**
	 * Method used to inject custom behaviour after a successful authentication attempt. 
	 * Return value of true allows authentication to continue setting up authenticated session
	 * Return value of false cancels the authentication request
	 * 
	 * @return boolean
	 */
	protected function success(){
		return true;
	}
	
	/**
	 * Executes after authenticated session has been setup. 
	 * Does not affect auth in any way. Used only to inject code after a successful logon and session setup
	 * process. That is, post ACL init, session and cookie setup.
	 */
	protected function postAuth(){
		$logEntry = new Jax_LogEntry(Jax_LogEntry::LOG_CATEGORY_AUTH,null,"Logon Success");
		Jax_System_Logger::log($logEntry,@$this->_credentials[Jax_Auth_Constants::FIELD_USERNAME]);
	}
}