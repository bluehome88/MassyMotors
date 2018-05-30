<?php
// Load adLDAP class
@require_once 'Jax/adLDAP/adLDAP.php';
class Jax_Data_Source_Ldap extends Jax_Data_Source_Abstract
{
	protected $_adLDAP;
	
	public function __construct($options = null){
		if (is_null($options)){
			$config = new Zend_Config_Ini(APPLICATION_PATH."/../library/".APPNAMESPACE."/".APPNAMESPACE.".ini","production");
			$LdapOptions = $config->ldap->{APPNAMESPACE}->toArray();
		} else {
			$LdapOptions = $options;
		}
		
		$adLDAPOptions = array(
			'account_suffix'=>"@".@$LdapOptions['accountDomainName'],
			'base_dn'=>@$LdapOptions['baseDn'],
			'domain_controllers'=>array(
				@$LdapOptions['host']
			),
			'admin_username'=>@$LdapOptions['adminUsername'],
			'admin_password'=>@$LdapOptions['adminPassword'],
			'use_tls'=>@$LdapOptions['useStartTls']
		);
		
		$this->_adLDAP_OPTIONS = $adLDAPOptions;
		
		$this->_adLDAP = new adLDAP($this->_adLDAP_OPTIONS);
	}
	
	/**
	 * Proxy to adLDAP class.
	 * 
	 * @return adLDAP
	 */
	public function adLDAP(){
		return $this->_adLDAP;
	}
	
	public function setLDAPOptions($config = null){
		$this->_adLDAP = new adLDAP($config);
		return this;
	}
	
	/**
	 * Used to dynamically invoke methods on this class 
	 * @return mixed
	 */
	public function __call($method, $args){
		/**
		 * Check if method exists within this class and invoke if it does, else invoke on adLDAP.
		 */
		if(method_exists($this, $method)){
			return call_user_func_array(array($this,$method), $args);
		} 		
		else {
			if (method_exists($this->_adLDAP, $method)){
				return call_user_func_array(array($this->_adLDAP,$method), $args);
			}
		}
		
		throw new Exception("Unable to invoke method. ".__CLASS__."->".$method);
	}
}