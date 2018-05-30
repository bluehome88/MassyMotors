<?php
class Jax_App_Options
{
	protected static $_instance;
	
	protected $_options;
	protected $_session;
	
	protected function __construct($options = array()){
		$this->_options = $options;
		$this->_session = new Zend_Session_Namespace(APPNAMESPACE);
		
		$this->initCustom();
	}
	
	public static function getInstance(){
		if (!self::$_instance){
			self::init();
		}
		return self::$_instance;
	}
	
	public static function init($cName=null){
		if (!self::$_instance){
			$cfg = Zend_Registry::get(Jax_System_Constants::SYSTEM_REGKEY_APPCFG);
			$options = array();
			if (isset($cfg[APPNAMESPACE])){
				$options = $cfg[APPNAMESPACE];
			}
			if(is_null($cName)){
				self::$_instance = new self($options);
			} else {
				self::$_instance = new $cName($options);
			}
		}
		
		return self::$_instance;
	}
	
	public function getAllOptions(){
		return $this->_options;
	}
	
	public function __get($name){
		if (is_array($this->_options) && array_key_exists($name, $this->_options)){
			return $this->_options[$name];
		}
		
		return false;
	}
	
	protected function initCustom(){}
}