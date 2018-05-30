<?php
/**
 * Concrete Resource Container Class
 * 
 * This is a container resource. Other concrete resources can be created
 * and set as children of an instance this class. Permissions can then be set
 * on this class instance, that would be inhereted by all its children.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Acl_Resource_Container extends Jax_Acl_Resource
{	
	/**
	 * Default constructor. Optionally sets the Acl resource id and the resource config array.
	 * 
	 * @param string $resourceId
	 * @param array $config
	 */
	public function __construct($resourceId = NULL,$config = array())
	{
		$this->_resourceId = $resourceId;
		$this->setConfig($config);
	}
}