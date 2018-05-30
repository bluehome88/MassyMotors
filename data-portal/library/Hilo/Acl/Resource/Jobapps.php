<?php
class Hilo_Acl_Resource_Jobapps extends Hilo_Acl_Resource
{
	protected $_resourceId = "Jobapps";
		
	public function __construct(){
		$this->_config[self::DISPLAY_NAME] = 'Job Applications';
		$this->_config[self::ICON] = 'icon-clipboard-2';
	}
	
	public function datelistProc(Zend_Controller_Request_Abstract $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_READ);
		
		$params = $request->getParams();
		
		if(!isset($params['date'])) return Jax_Response::Error("Invalid Request");
		
		$date = $params['date'];
		
		$r = Jax_Data_Source::getInstance()->getRecord("WebHrApp","`sys_added` LIKE \"$date%\"",true,array("sys_added"));
		
		$result = array();
		if($r){
			$r = array_reverse($r);
			foreach ($r as $row){
				$rdate = date("Y-m-d",strtotime($row['sys_added']));
				if(!in_array($rdate,$result))
					$result[] = $rdate;
			}
			
			return Jax_Response::Valid($result);
			
		} else {
			return Jax_Response::Valid(0);
		}
	}
	
	public function applistProc(Zend_Controller_Request_Abstract $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_READ);
		
		$params = $request->getParams();
		
		if(!isset($params['date'])) return Jax_Response::Error("Invalid Request");
		
		$date = $params['date'];
		
		$r = Jax_Data_Source::getInstance()->getRecord("WebHrApp","`sys_added` LIKE \"$date%\"",true,array("sys_added"));
		
		if($r){
			return Jax_Response::Valid($r);
		}
		
		return Jax_Response::Error("Unable to retrieve applications.");
	}
	
	public function applookupProc(Zend_Controller_Request_Abstract $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_READ);
	
		$params = $request->getParams();
	
		if(!isset($params['q'])) return Jax_Response::Error("Invalid Request");
	
		$q = $params['q'];
		
		if(empty($q)) return Jax_Response::Valid(array());
		
		$searchCols = array("position","employment_type","preferred_branch","firstname","lastname","email");
		$query = Jax_Utilities_SearchQueryBuilder::run($q,$searchCols);
		
		$applications = Jax_Data_Source::getInstance()->getRecord("WebHrApp",$query,true);
		
		if(!$applications) $applications = array();
		foreach($applications as &$customer){
			foreach($customer as $h=>&$v){
				if(!is_null($v))
					$v=stripslashes($v);
			}
		}
		
		return Jax_Response::Valid($applications);
	}
}