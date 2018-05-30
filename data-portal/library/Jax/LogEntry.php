<?php
class Jax_LogEntry
{
	const LOG_CATEGORY_AUTH = 'Auth';
	const LOG_CATEGORY_ACL = 'ACL';
	
	protected $_logLine = array();
	
	public function __construct($category="",$acl_mod="",$details="",$access="Read"){
		$this->_logLine['category'] = $category;
		$this->_logLine['acl_mod'] = $acl_mod;
		$this->_logLine['details'] = $details;
		$this->_logLine['access'] = $access;
	}
	
	public function getLogData(){
		return $this->_logLine;
	}
	
	public function __get($p){
		if (array_key_exists($p, $this->_logLine)) return $this->_logLine[$p];
		return null;
	}
}