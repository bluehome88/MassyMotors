<?php
class Hilo_Acl_Resource_Customers extends Hilo_Acl_Resource
{
	protected $_resourceId = "Customers";
		
	public function __construct(){
		$this->_config[self::DISPLAY_NAME] = 'Customers';
		$this->_config[self::ICON] = 'icon-user-2';
	}
	
	protected function searchProc(Zend_Controller_Request_Abstract $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_READ);
	
		$params = $request->getParams();
	
		if(!isset($params['q'])) return Jax_Response::Error("Invalid Request");
	
		if(count(explode(":",$params['q'])) > 1) return $this->advSearchProc($request);
	
		$q = trim($params['q']);
		if(empty($q)) return Jax_Response::Valid(array());
	
		$searchCols = array("AcctNo","FirstName","LastName","Email");
		$query = Jax_Utilities_SearchQueryBuilder::run($q,$searchCols);
	
		$customers = Jax_Data_Source::getInstance()->getRecord("SSCustomers",$query,true);
	
		if(!$customers) $customers = array();
		foreach($customers as &$customer){
			foreach($customer as $h=>&$v){
				if(!is_null($v))
					$v=stripslashes($v);
			}
		}
	
		return Jax_Response::Valid($customers);
	}
	
	protected function advSearchProc(Zend_Controller_Request_Abstract $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_READ);
	
		$params = $request->getParams();
	
		$parts = explode(":",$params['q']);
	
		$q = trim($parts[1]);
		if(empty($q)) return Jax_Response::Valid(array());
	
		switch($parts[0]){
			/*case "Order":
				$searchCols = array("first_name","last_name","middle_name","gender","address","country","phone1","mobile","email","customer","meal","group","location","committee","staff","type","package","section");
				$query = Jax_Utilities_SearchQueryBuilder::run($q,$searchCols);
	
				$customers = Jax_Data_Source::getInstance()->getRecord("ViewCustOrder",$query,true);
				break;*/
			default:
				$customers = array();
				break;
		}
	
		if(!$customers) $customers = array();
		foreach($customers as &$customer){
			foreach($customer as $h=>&$v){
				if(!is_null($v))
					$v=stripslashes($v);
			}
		}
	
		return Jax_Response::Valid($customers);
	}
	
	protected function resetPasswordProc(Zend_Controller_Request_Abstract $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_UPDATE);
		
		$params = $request->getParams();
		
		if(!$request->isPost() || !isset($params['s'])) return Jax_Response::Error("Invalid Request");
		
		$s = mysql_escape_string($params['s']);
		
		$r = Jax_Data_Source::getInstance()->updateRecord("SSCustomers","`AcctNo`=\"$s\"",array('password'=>null));
	
		if($r){
			
			Jax_System_Logger::log(new Jax_LogEntry("ACCT",Hilo_Acl_Constants::RESOURCE_CUSTOMERS,"Password reset on $s",Jax_Acl_Constants::ACCESS_UPDATE),Jax_Auth::getAuthId());
			
			return Jax_Response::Valid(1);
		}
		return Jax_Response::Valid(0);
	}
	
	protected function _update($params, $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_UPDATE);
	
		if(!$request->isPost() || !isset($params['p']) || !isset($params['v']) || !isset($params['s'])) return Jax_Response::Error("Invalid Request");
	
		$v = mysql_escape_string($params['v']);
		$p = mysql_escape_string($params['p']);
		$s = mysql_escape_string($params['s']);
	
		if($v == "Not Defined") return Jax_Response::Valid(1);
		if(intval($v)!==0 && empty($v)) return Jax_Response::Valid(1);
	
		if(strtolower(substr($p, 0,4)) == "sys_") return Jax_Response::Error("Invalid Request");
	
		$tbl = new Hilo_Models_SSCustomersUpdates();
		$uCols = $tbl->info('cols');
		
		$tblD = new Hilo_Models_SSCustomers();
		$dCols = $tblD->info('cols');
		
		$diff = array_diff($dCols, $uCols);
		
		$email = $this->_getAcctEmail($s);
		
		//if($p != "Email"){
		$row = Jax_Data_Source::getInstance()->getRecord("SSCustomers","`AcctNo`=\"$s\"");

		foreach ($diff as $col){
			unset($row[$col]);
		}
		
		unset($row['sys_added']);
		
		foreach ($row as $r=>$va){
			$va = trim($va);
			if (empty($va)) unset($row[$r]);
		}
				
		$u = Jax_Data_Source::getInstance()->updateOnDuplicate("SSCustomersUpdates",$row,1);
		
		//	$query = "REPLACE INTO ".$tbl->info('name')." (SELECT `AcctNo`,`Email`,`FirstName`,`MiddleName`,`LastName`,`BirthDate`,`Gender`,`Address1`,`Address2`,`Address3`,`City`,`Country`,`Phone`,`Identification`,`IdentificationNo`,NULL 
		//			FROM ".$tblD->info('name')." WHERE `AcctNo`=\"$s\")";
			
		//	$r = $tbl->getAdapter()->query($query);
		
		
			
			if($u){
				$query2 = "UPDATE ".$tbl->info('name')." SET `$p`=\"$v\" WHERE `AcctNo`=\"$s\"";
				$r = $tbl->getAdapter()->query($query2);
			}
			
		//}

		 /*else {
			//$query = "INSERT INTO ".$tbl->info('name')." (AcctNo,$p) VALUES(\"$s\",\"$v\") ON DUPLICATE KEY UPDATE `$p`=VALUES($p)";
			$query = "INSERT INTO ".$tbl->info('name')." SET `Email`=\"$v\" WHERE `AcctNo`=\"$s\"";
		}*/
		
		//$query = "INSERT INTO ".$tbl->info('name')." (AcctNo,Email,$p) VALUES(\"$s\",\"$email\",\"$v\") ON DUPLICATE KEY UPDATE `$p`=VALUES($p)";
		
		//$r = $tbl->getAdapter()->query($query);
		
		if($r){
			Jax_System_Logger::log(new Jax_LogEntry("ACCT",Hilo_Acl_Constants::RESOURCE_CUSTOMERS,"Updated $p to $v on $s",Jax_Acl_Constants::ACCESS_UPDATE),Jax_Auth::getAuthId());
			
			return Jax_Response::Valid(1);
		}
		return Jax_Response::Valid(0);
	}
	
	private function _getAcctEmail($acct){
		if(!isset($acct)) return false;
		
		$r = Jax_Data_Source::getInstance()->getRecord("SSCustomers","`AcctNo`=\"$acct\"");
		if($r){
			return $r['Email'];
		}
		return false;
	}
	
	public function _read($params, $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_READ);
	
		if(!isset($params['cust'])) return Jax_Response::Error("Invalid Request");
		$i = @mysql_escape_string($params['cust']);
	
		$cust = Jax_Data_Source::getInstance()->getRecord("SSCustomers","`AcctNo`='$i'");
		if($cust){
			foreach($cust as $h=>&$v){
				if(!is_null($v))
					$v=stripslashes($v);
			}
			return Jax_Response::Valid($cust);
		} else {
			return Jax_Response::Error("Invalid Request");
		}
	}
	
}