<?php
class Hilo_Acl_Resource_Jobapp extends Hilo_Acl_Resource
{
	protected $_resourceId = "Jobapp";
		
	public function __construct(){
		$this->_config[self::DISPLAY_NAME] = 'Job Applications';
		$this->_config[self::ICON] = 'icon-clipboard-2';
	}
	
	protected function listProc(Zend_Controller_Request_Abstract $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_READ);
		$params = $request->getParams();
		$cols = $params['col'];
		
		$data = array();
		foreach ($params as $h=>$v){
	
			if(empty($v)) continue;
			if(is_string($v) && trim($v) == "") continue;
				
			$v = @mysql_escape_string($v);
			if(substr($h, 0, 4) == "frm_"){
				$h = substr($h, 4);
	
				$data[$h] = $v;
			} else {
				continue;
			}
		}
	
		if(count($data) > 0){
			$where = "";
			foreach ($data as $h=>$v){
				
				if($h=="sys_added"){
					$where .= "`$h` LIKE \"$v%\" AND ";
				} 
				
				elseif($h=="sys_reviewed"){
					switch($v){
						case "Reviewed":
							$where .= "`$h` IS NOT NULL AND ";
						break;
						
						case "Not Reviewed":
							$where .= "`$h` IS NULL AND ";
						break;
						
						default:
							continue;
							break;
								
					}
				} 
				
				else {
					$where .= "`$h`='$v' AND ";
				}
			}
				
			$where = substr($where, 0,strlen($where)-5);
		} else {
			$where = null;
		}
		
		if(empty($where)) $where = null;
		$r = Jax_Data_Source::getInstance()->getRecord("WebHrApp",$where,true);
		if(!$r) $r = array();
	
		$notes = array();
	
		$exc = array();
		
		$inc = array("id","sys_reviewed","sys_reviewed_by");
		
		foreach($r as &$row){				
			foreach($row as $h=>&$v){
				if(in_array($h, $inc)) continue;
				
				if(in_array($h, $exc)) unset($row[$h]);
				if(!in_array($h, $cols)) unset($row[$h]);
	
				if(!is_null($v))
					$v=stripslashes($v);
			}
		}
		return Jax_Response::Valid($r);
	}
	
	protected function exportCSVProc(Zend_Controller_Request_Abstract $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_READ);
	
		$data = $this->listProc($request);
		
		$data = $data[Jax_Response::KEY_RESPONSE];
	
		header('Content-type: text/csv');
		header('Content-disposition: attachment;filename=applications.csv');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	
		$header = false;
		foreach($data as $row){
				
			$head = "";
			$rd = "";
			$recordId = 0;
			foreach ($row as $h=>$v){
				if($h == "id") $recordId = $v;
				
				if($h == "cv" && !empty($v)){
					$v = "http://massystorestt.com/data-portal/public/Hilo/Jobapp/cv/cid/".$recordId;
				}
				
				if(!$header){
					$head .= "$h,";
					$rd .= "$v,";
				} else {
					$rd .= "$v,";
				}
			}
				
			if(!$header){
				echo substr($head, 0,strlen($head)-1)."\r\n";
				$header = true;
			}
				
			echo substr($rd, 0,strlen($rd)-1)."\r\n";
		}
		die();
	}
	
	public function exportPrint(Zend_Controller_Request_Abstract $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_READ);
	
		$data = $this->listProc($request);
		$data = $data[Jax_Response::KEY_RESPONSE];
	
		return $data;
	}
	
	protected function markReviewedProc(Zend_Controller_Request_Abstract $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_UPDATE);
		
		$params = $request->getParams();
		
		if(!isset($params['id'])) return Jax_Response::Error("Missing Params");
		
		$id = $params['id'];
		
		$r = Jax_Data_Source::getInstance()->updateRecord("WebHrApp","`id`='$id'",array('sys_reviewed'=>date("Y-m-d H:i:s"),'sys_reviewed_by'=>Jax_Auth::getAuthId()),1);
		
		if($r){
			return Jax_Response::Valid(1);
		}
		
		return Jax_Response::Error('Unable to set as reviewed');
	}
}