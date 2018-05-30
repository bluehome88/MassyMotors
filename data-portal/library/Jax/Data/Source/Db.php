<?php
/**
 * General DB data source class.
 * Uses Zend's MultiDB to allow access to various flavours of RDBMS's
 *  
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Data_Source_Db 
	extends Jax_Data_Source_Abstract 
		implements Jax_Data_Source_Db_Interface
{
	const SESSKEY_DBSCHEMA = 'DefaultPersistedDBSchema';
	
	/**
	 * The configured multi db resource object
	 * 
	 * @var Zend_Application_Resource_Multidb
	 */
	protected $_multiDb;
	
	/**
	 * The default adapter of the configured multidb (if any)
	 * 
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_defaultAdapter;
	
	/**
	 * DB Adapter identified with the application's namespace (if any)
	 * 
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_NSAdapter;
	
	/**
	 * Instance of Zend_Registry
	 */
	protected $_registry;
	
	/**
	 * Default constructor. Retrieves the multidb resource and sets the adapters.
	 */
	public function __construct(){
		// Load registry
		$this->_registry = Zend_Registry::getInstance();
		
		// Get application bootstrap class
		$bootstrap = $this->_registry->get(Jax_System_Constants::SYSTEM_REGKEY_BOOTSTRAP);
		
		// Get multidb object from bootstrap
		try {
			if(!is_object($bootstrap->getResource('multidb'))){
				$bootstrap->getApplication()->bootstrap('multidb');
			}
			$this->_multiDb = $bootstrap->getResource('multidb');
			$this->_NSAdapter = $this->_multiDb->getDb(APPNAMESPACE);
			$this->_setDefaultAdapter();
		} catch (Exception $e){
			die($e->getTraceAsString());
		}
	}
	
	/**
	 * Proxy to getDb() of Zends MultiDB resource.
	 * 
	 * @param string $db - Defined in *.ini file as a multidb config
	 * @return Ambigous <Zend_Db_Adapter_Abstract, multitype:, NULL, mixed>
	 */
	public function getDb($db){
		return $this->getMultiDBResource()->getDb($db);
	}
	
	public function switchDb($db){
		$newDb = $this->getDb($db);
		if($newDb instanceof Zend_Db_Adapter_Abstract){
			$this->_defaultAdapter = $newDb;
			Zend_Db_Table::setDefaultAdapter($this->_defaultAdapter);
		}		
		return $this;
	}
	
	/**
	 * Returns the multiDB resource
	 * 
	 * @return Zend_Application_Resource_Multidb
	 */
	public function getMultiDBResource(){
		return $this->_multiDb;
	}
	
	/**
	 * Returns the default database adapter object
	 * 
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getDefaultAdapter(){
		return $this->_defaultAdapter;
	}
	
	protected function _setDefaultAdapter(){
		$session = new Zend_Session_Namespace(APPNAMESPACE);
		
		if(isset($session->{self::SESSKEY_DBSCHEMA})){
			$this->ZendDB($session->{self::SESSKEY_DBSCHEMA});
		} else {
			$this->_defaultAdapter = $this->_multiDb->getDefaultDb();
		}
		
		return $this;
	}
	
	/**
	 * Returns the configured (namespace) application db adapter
	 * 
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getNSAdapter(){
		return $this->_NSAdapter;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Jax_Data_Source_Db_Interface::getZendDBAdapter()
	 */
	public function getZendDBAdapter($getDefault = false){
		if($getDefault === true) return $this->getDefaultAdapter();
		return $this->getNSAdapter();
	}
	
	/**
	 * Create a new adapter class for specified database
	 * @param string $dbName
	 * @param int $dataSourceIndex Specify an index for storing the new DB object in the applications list of datasources
	 */
	public function ZendDB($dbName=APPNAMESPACE,$db = null){
		if(is_null($db)) $db = APPNAMESPACE;
		
		// Fetch default adapter class config
		$cfg = Zend_Registry::getInstance()->get(Jax_System_Constants::SYSTEM_REGKEY_APPOPTS);
		
		$adapterCfg = $cfg['resources']['multidb'][$db];
		
		$adapterCfg['dbname'] = $dbName;
		
		if($dbName != APPNAMESPACE){
			$this->_defaultAdapter = Zend_Db::factory($adapterCfg['adapter'],$adapterCfg);
			//$this->_NSAdapter = $this->_defaultAdapter; // Removed 2013 05 09. NS adapter should not change. When switching only default db switches.
			Zend_Db_Table::setDefaultAdapter($this->_defaultAdapter);
			
			$session = new Zend_Session_Namespace(APPNAMESPACE);
			$session->{self::SESSKEY_DBSCHEMA} = $dbName;
		}
	}
	
	/**
	 * Used to dynamically invoke methods on this class or alternatively invoke methods on the namespace db adapter.
	 * The namespace db adapter being an object of type Zend_Db_Adapter_Abstract
	 * 
	 * @return mixed
	 */
	public function __call($method, $args){
		/**
		 * Check if method exists within this class and invoke if it does.
		 */
		if(method_exists($this, $method)){
			return call_user_func_array(array($this,$method), $args);
		} 
		
		/**
		 * Invoke the method on the namespace db adapter if method does not exist within this class.
		 */
		else {
			return call_user_func_array(array($this->_NSAdapter,$method), $args);
		}
	}
	
	protected function _checkTblName($table){
		$tbl = APPNAMESPACE."_Models_".$table;
		if(!class_exists($tbl,true)) {
			$tbl = "Jax_Models_".$table;
		}
		
		if(!class_exists($tbl,true)) die('Invalid table name specified.'.__CLASS__.' '.__LINE__);
		
		return $tbl;
	}
	
	/**
	 * Add a record to a db table
	 * @param string $table
	 * @param array $data
	 * @return boolean
	 */
	protected function addRecord($table,array $data){
		$tbl = $this->_checkTblName($table);
		$tblHandle = new $tbl;
		return $tblHandle->insert($data);
	}
	
	/**
	 * Update a record
	 * 
	 * @param string $table
	 * @param string|array $condition
	 * @param array $updates
	 */
	protected function updateRecord($table,$condition,array $updates){
		$tbl = $this->_checkTblName($table);
		$tblHandle = new $tbl;
		
		return $tblHandle->update($updates,$condition);
	}
	
	/**
	 * Remove a record
	 * 
	 * @param string $table
	 * @param string|array $condition
	 */
	protected function deleteRecord($table,$condition){
		$tbl = $this->_checkTblName($table);
		$tblHandle = new $tbl;
		
		return $tblHandle->delete($condition);
	}
	
	/**
	 * Retrieve record(s) from a table
	 * 
	 * @param string $table
	 * @param string|array $condition
	 * @param boolean $multiple
	 */
	protected function getRecord($table,$condition = null,$multiple = false,$order = array()){
		$tbl = $this->_checkTblName($table);		
		
		$tblHandle = new $tbl;
		if(!is_object($tblHandle)) return "Invalid Table";
		
		$select = $tblHandle->select();
		if(!is_null($condition)) $select->where($condition);
		if(!empty($order)) $select->order($order);
		
		try {
			if ($multiple){
				$res = $tblHandle->fetchAll($select);
				if(is_object($res)){
					return $res->toArray();
				} else {
					return $res;
				}
			} else {
				$res = $tblHandle->fetchRow($select);
	
				if (!is_null($res)){
					return $res->toArray();
				} else {
					return null;
				}
			}
		} catch (Exception $e){
			die($e->getMessage());
		}
	}
	
	protected function getTblCols($table){
		$tbl = $this->_checkTblName($table);
		$tbl = new $tbl;
		return $tbl->info($tbl::COLS);
	}
}