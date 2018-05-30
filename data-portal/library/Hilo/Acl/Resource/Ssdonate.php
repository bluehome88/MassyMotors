<?php
class Hilo_Acl_Resource_Ssdonate extends Hilo_Acl_Resource
{
	protected $_resourceId = "Ssdonate";
		
	public function __construct(){
		$this->_config[self::DISPLAY_NAME] = 'Smart Shopper Donate';
		$this->_config[self::ICON] = 'icon-heart';
	}
	
	protected function processProc(Zend_Controller_Request_Abstract $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_UPDATE);
		
		$params = $request->getParams();
		if(!isset($params['did'])) return Jax_Response::Error("Invalid request. Missing DID");
		
		$did = intval($params['did']);
		$r = Jax_Data_Source::getInstance()->updateRecord("SSCharity","`id`=\"$did\"",array('processed'=>date("Y-m-d H:i:s"),'processed_by'=>Jax_Auth::getAuthId()),1);
		
		if ($r){
			Jax_System_Logger::log(new Jax_LogEntry("DONATION",Hilo_Acl_Constants::RESOURCE_SSDONATE,"Points transfer processed. DID: $did",Jax_Acl_Constants::ACCESS_UPDATE),Jax_Auth::getAuthId());
				
			return Jax_Response::Valid(1);
		} 
		return Jax_Response::Error("Unable to mark as processed");
	}
}