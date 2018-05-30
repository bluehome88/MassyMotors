<?php
class Hilo_Acl_Resource_Fbpromos extends Hilo_Acl_Resource
{
	protected $_resourceId = "Fbpromos";
		
	public function __construct(){
		$this->_config[self::DISPLAY_NAME] = 'Facebook Promos';
		$this->_config[self::ICON] = 'icon-facebook';
	}
}