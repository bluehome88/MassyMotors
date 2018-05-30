<?php
/**
 * Jax Auth
 * Provides static functions for managing authentication.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Auth
{
	const AUTH_RETURN = 'JAX_AUTH_RETURN';
	/**
	 * Verifies if a valid authentication session exists.
	 * 
	 * @return boolean
	 */
	public static function verify()
	{
		$session = new Zend_Session_Namespace(APPNAMESPACE);
		$auth = Zend_Auth::getInstance();
		
		if (isset($session->{Jax_Auth_Constants::SESS_KEY_AUTH_TOKEN}) 
				&& $auth->hasIdentity() 
					&& @$_COOKIE[$session->{Jax_Auth_Constants::SESS_KEY_AUTH_COOKIE}]) return true;

		return false;
	}
	
	/**
	 * Performs login routine 
	 * 
	 * @param Zend_Auth_Adapter_Interface $authAdapter
	 * @return Jax_Response
	 */
	public static function login(Zend_Auth_Adapter_Interface $authAdapter)
	{
		$auth = Zend_Auth::getInstance();
		
    	// Perform Authentication
    	$authResult = $auth->authenticate($authAdapter);
    	
    	switch ($authResult->getCode())
    	{
    		case Zend_Auth_Result::SUCCESS:
    			if ($authResult->isValid()){
    				if(Zend_Registry::isRegistered(Jax_Auth::AUTH_RETURN)) return Jax_Response::Valid(Zend_Registry::get(Jax_Auth::AUTH_RETURN));
    				
    				return Jax_Response::Valid(session_id(),Zend_Auth_Result::SUCCESS);
    			} else {
    				return Jax_Response::Error('An authenticated session could not be established!','-1');
    			}
    			return;
    		break;
    		
    		case Zend_Auth_Result::FAILURE:
    			return Jax_Response::Error($authResult->getMessages(),Zend_Auth_Result::FAILURE);
    		break;
    		
    		case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
    			return Jax_Response::Error($authResult->getMessages(),Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID);
    		break;
    		
    		case Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS:
    			return Jax_Response::Error($authResult->getMessages(),Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS);
    		break;
    		
    		case Zend_Auth_Result::FAILURE_UNCATEGORIZED:
    			return Jax_Response::Error($authResult->getMessages(),Zend_Auth_Result::FAILURE_UNCATEGORIZED);
    		break;
    		
    		default:
    			return Jax_Response::Error('Authentication adapter returned an unrecognized response.','UNKNOWN ('.$authResult->getCode().')'); 		
    		break;
    	}
	}
	
	/**
	 * Performs a logout routine
	 * 
	 * @param string $message
	 * @return Jax_Response
	 */
	public static function logout($message = null,$delay = 3)
	{
		$auth = Zend_Auth::getInstance();
		
		$auth->clearIdentity();
    	
    	$session = new Zend_Session_Namespace(APPNAMESPACE);
    	
    	if (isset($_SERVER['HTTP_COOKIE'])) {
    		$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    		foreach($cookies as $cookie) {
    			$parts = explode('=', $cookie);
    			$name = trim($parts[0]);
    			setcookie($name, '', time()-1000);
    			setcookie($name, '', time()-1000, '/');
    		}
    	}
    	
    	//setcookie($session->{Jax_Auth_Constants::SESS_KEY_AUTH_COOKIE},"",time()-3600,'/');
		
		$session->unsetAll();
		
		@session_destroy();
		
		return Jax_Response::Valid(array('logout'=>$message,'delay'=>intval($delay)));
	}
	
	/**
	 * Retrieves the token generated at logon
	 * 
	 * @return string
	 */
	public static function getAuthToken()
	{
		$session = new Zend_Session_Namespace(APPNAMESPACE);
		return $session->{Jax_Auth_Constants::SESS_KEY_AUTH_TOKEN};
	}
	
	/**
	 * Retrieves the id of the authenticated user.
	 * 
	 * @return string
	 */
	public static function getAuthId()
	{
		$auth = Zend_Auth::getInstance();
		return $auth->getIdentity();
	}
	
	/**
	 * Retrieves the authentication level (user role, ACL) of the current user.
	 * 
	 * @return string
	 */
	public static function getAuthLevel()
	{
		$session = new Zend_Session_Namespace(APPNAMESPACE);
		return $session->{Jax_Auth_Constants::SESS_KEY_AUTH_LEVEL};
	}
	
	/**
	 * Sets the authentication cookie when logon is successful
	 * 
	 * @param string $cookieName
	 * @return boolean
	 */
	public static function setAuthCookie($cookieName){
		$session = new Zend_Session_Namespace(APPNAMESPACE);
		$session->{Jax_Auth_Constants::SESS_KEY_AUTH_COOKIE} = $cookieName;
		setcookie($cookieName,Jax_Auth::getAuthToken(),0,'/');
		
		return true;
	}
	
	/**
	 * Sets the authentication token when logon is successful
	 * 
	 * @param string $token
	 * @return boolean
	 */
	public static function setAuthToken($token){
		$session = new Zend_Session_Namespace(APPNAMESPACE);
		$session->{Jax_Auth_Constants::SESS_KEY_AUTH_TOKEN} = $token;
		
		return true;
	}
	
	/**
	 * Initializes the ACL after a successful login
	 * The Acl is stored for the entire session.
	 */
	public static function initJaxAcl($userId){
		$session = new Zend_Session_Namespace(APPNAMESPACE);
		
		if (class_exists(APPNAMESPACE."_Acl_Config")){
			$cfgClass = APPNAMESPACE."_Acl_Config";
			$config = new $cfgClass($userId);
			
			if($config instanceof Jax_Acl_Config){
				$Acl = Jax_Acl::getInstance();
				$Acl->setConfig($config);
				$session->{Jax_System_Constants::SYSTEM_SESSIONKEY_APPACL} = serialize($Acl);
			}
		}
	}
	
	/**
	 * Sets the authentication level when logon is successful.
	 * 
	 * @param string $level
	 * @return boolean
	 */
	public static function setAuthLevel($level){
			if(Jax_Acl::getInstance()->acl()->hasRole($level)){
				$session = new Zend_Session_Namespace(APPNAMESPACE);
				$session->{Jax_Auth_Constants::SESS_KEY_AUTH_LEVEL} = $level;
				
				return true;
			}
		return false;
	}
	
	/**
	 * Sets an anonymous auth session.
	 */
	public static function anonymousLogon($AppNS){
		if (!isset($_COOKIE['Jax-Active-Application']) && $AppNS != 'Jax')
			setcookie('Jax-Active-Application',$AppNS.'.main.js',0,substr(BASEURL,0,strlen(BASEURL)-1));
		
		if(!self::verify() && APPNAMESPACE != 'Jax')
			return self::login(new Jax_Auth_Adapter_Anonymous());
			
		return self::getAuthToken();
	}
	
	/**
	 * Validates the Jax admin password
	 * 
	 * @param string $pwd
	 * @return boolean
	 */
	public static function verifyJaxAdmin($pwd){
		$sess = new Zend_Session_Namespace(APPNAMESPACE);
		$file = file(APPLICATION_PATH.'/license/admin.pwd');
    	if (trim($file[0]) == md5($pwd)){
    		$sess->{Jax_System_Constants::SYSTEM_SESSIONKEY_VALIDJAXADMIN} = 1;
    		return true;
    	} else {
    		$sess->{Jax_System_Constants::SYSTEM_SESSIONKEY_VALIDJAXADMIN} = null;
    		return false;
    	}
	}
	
	/**
	 * Checks if there was a previous Jax admin validation request.
	 * 
	 * @return boolean
	 */
	public static function isJaxAdminSession(){
		$sess = new Zend_Session_Namespace(APPNAMESPACE);
		if($sess->{Jax_System_Constants::SYSTEM_SESSIONKEY_VALIDJAXADMIN} === 1){
			unset($sess->{Jax_System_Constants::SYSTEM_SESSIONKEY_VALIDJAXADMIN});
			return true;
		} else {
			return false;
		}
	}
	
	public static function pwdCheck($pwd,$user=null){
		if(!Jax_Auth::verify()) throw new Exception('Invalid Session');
		if(!isset($pwd)) throw new Exception('Password not supplied');
		
		if(is_null($user)) $user=Jax_Auth::getAuthId();
		
		$user = @mysql_escape_string($user);
		
		$pwd = md5($pwd);
		
		$rec = Jax_Data_Source::getInstance()->getRecord("AuthUsers","`username`='$user' AND `password`='$pwd'");
		
		if($rec){
			return true;
		}
		
		return false;
	}
	
	public static function changePwd($pwd,$user=null){
		if(!Jax_Auth::verify()) throw new Exception('Invalid Session');
		
		if(!is_null($user))
			Jax_Utilities_ResourceAccessChecker::run(Jax_Acl_Constants::RESOURCE_CP_ACL_ROLEADMIN,Jax_Acl_Constants::ACCESS_WRITE);
		
		if(is_null($user)) $user=Jax_Auth::getAuthId();
		$user = @mysql_escape_string($user);
		$pwd = md5($pwd);
		
		if(!isset($pwd)) throw new Exception('Password not supplied');
		
		$u = Jax_Data_Source::getInstance()->updateRecord("AuthUsers","`username`='$user'",array('password'=>$pwd));
		
		if($u) return true;
	}
}