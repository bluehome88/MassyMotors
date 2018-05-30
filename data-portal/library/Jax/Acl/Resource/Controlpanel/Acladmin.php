<?php
/**
 * ACL Resource - ACL Administration
 * Container Class. Parent Resource used to group resources related to ACL Administration.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link http://www.nerdsconsulting.com
 * @package Jax
 * @version 1.0.0
 */
class Jax_Acl_Resource_Controlpanel_Acladmin extends Jax_Acl_Resource_Controlpanel
{
	protected $_resourceId = Jax_Acl_Constants::RESOURCE_CP_ACLADMIN;
	
	public function __construct(){
		parent::__construct();
		$this->_config[self::DISPLAY_NAME] = 'ACL Administration';
		$this->_config[self::ICON] = 'icon-list';
	}
	
	public function _refreshACL(){
		Jax_Acl::reset();
		Jax_Auth::initJaxAcl(Jax_Auth::getAuthId());
	}
	
	public static function refreshACL(){
		Jax_Acl::reset();
		Jax_Auth::initJaxAcl(Jax_Auth::getAuthId());
	}
}