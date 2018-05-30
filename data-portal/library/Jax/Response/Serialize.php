<?php
/**
 * PHP Serialize response class.
 * Encodes data as PHP serialized string
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Response_Serialize extends Jax_Response_Abstract
{
	
	/**
	 * (non-PHPdoc)
	 * @see Jax_Response_Abstract::_sendResponseHeaders()
	 */
	protected function _sendResponseHeaders()
	{
		header('Content-type: text/plain');
		header('Pragma: no-cache');
    	header('Cache-Control: no-cache');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Jax_Response_Abstract::__toString()
	 */
	public function __toString() {
		return Zend_Serializer::serialize($this->_data);
	}
}