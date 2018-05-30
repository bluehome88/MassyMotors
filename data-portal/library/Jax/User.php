<?php
/**
 * Jax_User
 * Provides an additional level of abstraction for retrieving user info.
 * This class can either retrieve user info from Active Directory or underlying data source classes.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 * @version 1.0.0
 */
class Jax_User
{
	protected static $_instance;
	protected $_currentUser;
	protected $_appOptions;
	protected $_userContext;
	
	public static function getInstance(){
		if (!self::$_instance){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	protected function __construct(){
		if (Jax_Auth::verify()){
			$this->setUser(Jax_Auth::getAuthId());
		}
		
		$this->_appOptions = Jax_App_Options::getInstance();
	}
	
	public function setUser($userId){
		$this->_currentUser = $userId;
		
		$this->_setUserContext();
		
		return $this;
	}
	
	public function getUserObject($returnObject = false, $skipUserContext = false){
		if ($this->_hasUser() && $skipUserContext == true){
			if ($this->_appOptions->active_directory){
				if($returnObject) return (object) $this->_getFromActiveDirectory();
				return $this->_getFromActiveDirectory();
			} else {
				if($returnObject) return (object) $this->_getFromDataSource();
				return $this->_getFromDataSource();
			}
		} 
		
		elseif ($this->_hasUser() && $skipUserContext == false){
			$this->_setUserContext();
			
			if(!Jax_Auth::verify()) return $this->getUserObject(false,true);
			$oResult = $this->_userContext->setUserId($this->_currentUser)->getUserObject();
			
			$userObject = $oResult[Jax_Response::KEY_RESPONSE];
			
			if ($returnObject == false){
				return (Array) $userObject;
			}
			
			return $userObject;
		}
		
		return false;
	}
	
	protected function _setUserContext(){
		if(Jax_Auth::getAuthLevel()){
			$UserType = Jax_Auth::getAuthLevel();
			
			$UserContext = APPNAMESPACE."_User_".$UserType;
			$this->_userContext = new $UserContext($this->_currentUser);
		}
		
		return $this;
	}
	
	protected function _getFromActiveDirectory(){
		$adObject = Jax_Utilities_ActiveDirectory::getUserInfo($this->_currentUser,array("*"));
		$adObject = $adObject[0];
		
		if (!is_array($adObject)) return null;
		
		$displayName = $adObject['displayname'][0];
		$names = explode(" ", $displayName);
		
		$userObject['Firstname'] = $names[0];
		$userObject['Lastname'] = $names[count($names)-1];
		$userObject['Sex'] = "M";
		$userObject['Role'] = Jax_Acl::getInstance()->getRoleHierarchy($this->_currentUser);
		$userObject['Phone'] = @$adObject['telephonenumber'][0];
		$userObject['Email'] = @$adObject['mail'][0];
		$userObject['Fullname'] = @$userObject['Firstname']." ".@$userObject['Lastname'];

		//return $this->_appendEmployeeData($userObject);
		return $userObject;
	}
	
	protected function _getFromDataSource(){
		$userObject = Jax_Data_Source::getInstance()->getUserData($this->_currentUser);
		if (!is_array($userObject)) return null;
		
		$userObject['Role'] = Jax_Acl::getInstance()->getRoleHierarchy($this->_currentUser);
		$userObject['Fullname'] = @$userObject['Firstname']." ".@$userObject['Lastname'];
		
		if(array_key_exists('password', $userObject))
			unset($userObject['password']);
		
		//return $this->_appendEmployeeData($userObject);
		return $userObject;
	}
	/*
	protected function _appendEmployeeData($userObject){
		if($this->_hasUser() && @class_exists(APPNAMESPACE."_Models_ArnEmployees")){
			$edata = Jax_Data_Source::getInstance()->getRecord("ArnEmployees","`username`='".$this->_currentUser."'");
			if($edata){
				return array_merge($userObject,$edata);
			}
		}
		
		return $userObject;
	}
	*/
	protected function _hasUser(){
		if ($this->_currentUser) return true;
		
		return false;
	}
	
	public function __get($key){
		$o = $this->getUserObject();
		if(is_array($o) && array_key_exists($key, $o)) return $o[$key];
		
		return null;
	}
}