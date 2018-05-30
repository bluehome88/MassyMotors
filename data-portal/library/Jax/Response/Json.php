<?php
/**
 * JSON response class.
 * Encodes data as JSON and returns.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Response_Json extends Jax_Response_Abstract
{
	
	/**
	 * Default constructor.
	 * Accepts and sets response data. Validates the structure of the JSON response object.
	 * 
	 * @param mixed $data
	 */
	public function __construct($data = NULL)
	{
		if (is_null($data)) return;
		
		if (is_array($data))
		{
			$this->_data = array();
			
			$acceptedKeys = array(
				Jax_Response::KEY_REDIRECT,
				Jax_Response::KEY_RESPONSE,
				Jax_Response::KEY_ERROR,
				Jax_Response::KEY_CODE);
			
			foreach ($acceptedKeys as $key)
			{
				if (array_key_exists($key, $data))
				{
					$this->_data[$key] = $data[$key];
				}
			}
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Jax_Response_Abstract::_sendResponseHeaders()
	 */
	protected function _sendResponseHeaders()
	{
		header('Content-type: application/json');
		header('Pragma: no-cache');
    	header('Cache-Control: no-cache');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Jax_Response_Abstract::__toString()
	 */
	public function __toString() {
		if(array_key_exists("jsonp", $this->_data)){
			$callback = (string) $this->_data['jsonp'];
			unset($this->_data['jsonp']);
		}
		
		$return = (string) Zend_Json::encode($this->_data);
		
		if(isset($callback)){
			$return = $callback."(".$return.")";
		}
		
		return $return;
	}
}