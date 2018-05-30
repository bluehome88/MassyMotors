<?php
/**
 * Jax_Response_Handler is responsible for passing the returned data to the
 * appropriate response class and triggering returning (printing) of the response.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Response_Handler
{	
	/**
	 * The response type.
	 * 
	 * @var Jax_Response_Abstract
	 */
	private $_responseClass;
	
	/**
	 * The response data.
	 * 
	 * @var mixed
	 */
	private $_data;
	
	const SERIALIZE = 0;
	const JSON = 1;
	
	/**
	 * Sets the response data to be encoded.
	 * 
	 * @param mixed $data
	 */
	public function setResponseData($data = null)
	{
		$this->_data = $data;
		return $this;
	}
	
	/**
	 * Sets the requested response type
	 * 
	 * @param int $responseType
	 */
	public function setResponseType($responseType = 0)
	{
		switch ($responseType)
		{
			case self::JSON:
				$this->_responseClass = new Jax_Response_Json();
			break;
			
			case self::SERIALIZE:
				$this->_responseClass = new Jax_Response_Serialize();
			break;
		}
		
		return $this;
	}
	
	/**
	 * Encodes and returns the response.
	 * 
	 * @return void
	 */
	public function send()
	{
		return $this->_responseClass->setData($this->_data)->send();
	}
}
