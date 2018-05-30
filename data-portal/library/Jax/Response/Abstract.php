<?php
/**
 * Jax Response
 * 
 * In the Jax library, all classes return some type of data structure or string.
 * Jax response is designed to be able to encode those responses into specific types
 * as requested by the client.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
abstract class Jax_Response_Abstract
{
	/**
	 * The data to be encoded and returned.
	 * 
	 * @var mixed
	 */
	protected $_data;
	
	/**
	 * Default constructor.
	 * Receives and stores the return data.
	 * 
	 * @param mixed $data
	 */
    public function __construct ($data = null)
    {
    	$this->_data = $data;
    }
    
    /**
     * Returns the data set for encoding and sending to the browser.
     * 
     * @return mixed
     */
    public function getData(){
    	return $this->_data;
    }
    
    /**
     * Sets the data to be encoded and returned.
     * 
     * @param mixed $data
     */
    public function setData($data = null)
    {
    	$this->_data = $data;
    	return $this;
    }
    
    /**
     * Test to determine if the stored data is already an instance of Jax_Response_Abstract
     * 
     * @return boolean
     */
    protected final function _dataCheck()
    {
    	if ($this->_data instanceof Jax_Response_Abstract) return false;
    	
    	return true;
    }
    
    /**
     * Output the response data.
     * 
     * @return void
     */
    public final function send()
    {
    	$this->_sendResponseHeaders();
    	
    	if (!$this->_dataCheck()) print $this->_data->__toString();
    	
    	print $this->__toString();
    }
    
    /**
     * Sends the appropriate response headers.
     * Default is text/html
     * 
     * @return void
     */
    protected function _sendResponseHeaders()
    {
    	header('Content-type: text/html;');
    	header('Pragma: no-cache');
    	header('Cache-Control: no-cache');
    }
    
    /**
     * To be implemented by concrete response classes.
     * Should return a string.
     * 
     * @return string
     */
    abstract public function __toString();
}