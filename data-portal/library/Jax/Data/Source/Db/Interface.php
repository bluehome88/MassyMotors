<?php
/**
 * Interface to be implemented by all data source classes that utilize an underlying Zend_Db_Adapter_Abstract class.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
interface Jax_Data_Source_Db_Interface
{
	/**
	 * Must return an object of instance Zend_Db_Adapter_Abstract
	 * 
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getZendDBAdapter($getDefault = false);
}