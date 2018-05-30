<?php
/**
 * Jax ACL Role - Abstract Role Class
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 * @version 1.0.0
 */
abstract class Jax_Acl_Role extends Zend_Acl_Role
{
	protected $_userObject;
	protected $_isGroup = false;
	protected $_excludes = array("sys_update_user");
	
	public function __construct($roleId){
		parent::__construct($roleId);
		
		$this->_setUserInfo();
	}
	
	protected function _setUserInfo($o = false){
		if(Jax_Auth::verify()){
			$this->_userObject = Jax_User::getInstance()->setUser($this->_roleId)->getUserObject($o);
		} else {
			$this->_userObject = Jax_User::getInstance()->setUser($this->_roleId)->getUserObject($o,true);
		}
		return $this;
	}
	
	public function getUserObject($o = false){
		return $this->_setUserInfo($o)->_userObject;
	}
	
	public function isGeneralAccount(){
		if(in_array("GeneralAccounts", $this->getUserObject(true)->Role)) return true;
	}
	
	public function isGroup(){
		return $this->_isGroup;
	}
	
	public function partialSearch($term){
		// update user object
		$this->_setUserInfo();
		
		if (is_array($this->_userObject)){
			foreach ($this->_userObject as $h=>$v){
				if(in_array($h, $this->_excludes)) continue;
				
				if (is_string($v)){
					if (stristr($v, $term)) return $h;
				}
				
				if (is_array($v)){
					if (in_array($term, $v)) return $h;
				}
			}
		}
		
		return false;
	}
	
	public function __get($p){
		$this->_setUserInfo();
		
		if (isset($this->_userObject) && is_array($this->_userObject) && array_key_exists($p, $this->_userObject))
			return $this->_userObject[$p];
			
		return null;
	}
}