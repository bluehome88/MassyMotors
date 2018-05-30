<?php
class Hilo_Acl_Resource_Valentinesi extends Hilo_Acl_Resource
{
	protected $_resourceId = "Valentinesi";
		
	public function __construct(){
		$this->_config[self::DISPLAY_NAME] = 'Valentines 2014';
		$this->_config[self::ICON] = 'icon-heart';
	}
	
	public function _update($params, $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_UPDATE);
		
		if (isset($params['eid'])){
			$r = Jax_Data_Source::getInstance()->updateRecord("Val2014","`id`=\"".$params['eid']."\"",
					array('verified'=>date("Y-m-d H:i:s"),"verified_by"=>Jax_Auth::getAuthId()),1);
			
			if($r){
				return Jax_Response::Valid($r);
			}
		} 
			
		return Jax_Response::Error("Unable to update entry");
		
	}
}