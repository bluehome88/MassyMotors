<?php
abstract class Jax_Auth_Adapter_Db extends Jax_Auth_Adapter_Abstract
{
	/**
	 * Table column that stores the user identities
	 * 
	 * @var string
	 */
	protected $_identityCol;
	
	/**
	 * Table column that stores the user credentials
	 * 
	 * @var string
	 */
	protected $_credentialCol;
	
	/**
	 * The table name to be used for authentication
	 * 
	 * @var string 
	 */
	protected $_tableName;
	
	/**
	 * The underlying Zend_Auth_Adapter_DbTable object instance
	 * 
	 * @var Zend_Auth_Adapter_DbTable
	 */
	protected $_zendAuthAdapter;
	
	/**
	 * Default constructor. Initializes the underlying Zend_Auth_Adapter_DbTable object
	 */
	public function __construct(Zend_Db_Adapter_Abstract $dbAdapter = null){
		$this->resetZendAuthAdapter($dbAdapter);
	}
	
	/**
	 * Attempt authentication
	 */
	public function authenticate(){
		try {
			$this->_configureAdapter();
			
			//Pre-authentication checks
			if (is_null($this->_identityCol) || is_null($this->_credentialCol)){
				throw new Exception("Identity and/or Credential columns not set!");
			}
			
			// First check for a blank password, this allows setting of initial password
			// Added 2013-01-30 14:35
			$this->_isFirstLogin();
			
			$this->_zendAuthAdapter
				->setCredential($this->_credentials[Jax_Auth_Constants::FIELD_PASSWORD])
				->setCredentialTreatment('MD5(?)') 
				/* 
				 * If changing remember to change:
				 * _isFirstLogin()
				 * Jax_Auth::pwdCheck()
				 * 
				 */
				->setIdentity($this->_credentials[Jax_Auth_Constants::FIELD_USERNAME]);
				
				$results = $this->_zendAuthAdapter->authenticate();
			
			if ($results->isValid()){
				
				// Determine if account is disabled or not
				$r = Jax_Data_Source::getInstance()->getRecord("AuthUsers","`username`='".$this->_credentials[Jax_Auth_Constants::FIELD_USERNAME]."' AND `sys_disabled`='1'");
				if($r) {
					Jax_Auth::logout();
					return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $this->_credentials[Jax_Auth_Constants::FIELD_USERNAME],array('Account is disabled.'));
				}
				
				if($this->success()){
					if($this->_setAuthenticatedSession() === false){
						Jax_Auth::logout();
						return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $this->_credentials[Jax_Auth_Constants::FIELD_USERNAME],array('User defined failure (Unable to set authenticated session).'));
					}
				} else {
					Jax_Auth::logout();
					return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $this->_credentials[Jax_Auth_Constants::FIELD_USERNAME],array('User defined failure (success::false).'));
				}
			}
			
			return $results;
		
		} catch (Exception $e){
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $this->_credentials[Jax_Auth_Constants::FIELD_USERNAME],array($e->getMessage()));
		}
	}
	
	/**
	 * Retrieves the underlying Zend_Auth_Adapter_DbTable object
	 * 
	 * @return Zend_Auth_Adapter_DbTable
	 */
	public function getZendAuthAdapter(){
		return $this->_zendAuthAdapter;
	}
	
	/**
	 * The table name to be used to process the authentication request
	 * 
	 * @param string $tableName
	 * @return Jax_Auth_Adapter_Db
	 */
	public function setTableName($tableName){
		$this->_tableName = $tableName;
		$this->_zendAuthAdapter->setTableName($tableName);
		return $this;
	}
	
	/**
	 * Sets the identity column
	 * 
	 * @param string $identityColumn
	 * @return Jax_Auth_Adapter_Db
	 */
	public function setIdentityCol($identityColumn){
		$this->_identityCol = $identityColumn;
		$this->_zendAuthAdapter->setIdentityColumn($identityColumn);
		return $this;
	}
	
	/**
	 * Sets the credential column
	 * 
	 * @param string $identityColumn
	 * @return Jax_Auth_Adapter_Db
	 */
	public function setCredentialCol($credentialColumn){
		$this->_credentialCol = $credentialColumn;
		$this->_zendAuthAdapter->setCredentialColumn($credentialColumn);
		return $this;
	}
	
	/**
	 * Sets a new Zend_DB instance for use by the DB auth adapter class.
	 */
	public function resetZendAuthAdapter(Zend_Db_Adapter_Abstract $dbAdapter = null){
		if(is_null($dbAdapter)){
			$dataSource = Jax_Data_Source::getInstance()
								->getDataSourceWrapper()
								->getDataSourceObject(Jax_Data_Source_Types::DB);
			
			$dbAdapter = $dataSource->getDefaultAdapter();
		}
		
		$this->_zendAuthAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		return $this;
	}
	
	protected function _isFirstLogin(){
		$userId = $this->_credentials[Jax_Auth_Constants::FIELD_USERNAME];
		$suppliedPass = $this->_credentials[Jax_Auth_Constants::FIELD_PASSWORD];
	
		$db = Jax_Data_Source::getInstance()->getNSAdapter();
		// Get password
		$r = new Zend_Db_Select($db);
		
		$r->from($this->_tableName,$this->_credentialCol)
			->where("`".$this->_identityCol."`='".$userId."'")
			->query();
		
		$p = $db->fetchAll($r);
		if (count($p) > 0){
			$password = $p[0][$this->_credentialCol];
			
			if (is_null($password) || strlen($password) < 1) {
				$db->query("UPDATE `".$this->_tableName."` SET `".$this->_credentialCol."`='".md5($suppliedPass)."' 
				WHERE `".$this->_identityCol."`='".$userId."'");
			}
		}
	}
	
	/**
	 * Concrete classes should implement this method to set the required parameters for this adapter class.
	 * 
	 * Actions to perform within this method may include those such as setting the names of the identity and 
	 * credential columns via the methods available.
	 */
	abstract protected function _configureAdapter();
}