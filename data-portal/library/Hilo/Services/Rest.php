<?php
class Hilo_Services_Rest {
	protected $_responseHandler;
	
	public function __construct(){
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
	}
	
	protected function _returnResponse($response){
		if(!is_array($response)) throw new Exception('Invalid Response Type');
		
		$this->_responseHandler
		->setResponseData($response)
		->send();
		 
		return;
	}
	
	/**
	 * getToken - Generate a token for future api calls. Tokens are valid for 1hr from last use.
	 * 
	 * @param string $appid
	 * @param string $secret
	 * @return Jax_Response
	 */
	public function getToken($appid,$secret){
		$appid = @mysql_escape_string($appid);
		$secret = @mysql_escape_string($secret);
		
		$this->_validateTokenRequest($appid, $secret);
		
		$token = $this->_genToken($appid, $secret);
		
		$this->_log($appid,__METHOD__, $token);
		return Jax_Response::Valid($token);
	}
	
	/**
	 * Checks the validity of a Smart Shopper number
	 * 
	 * @param string $appid
	 * @param string $token
	 * @param string $ss_num
	 * @return Jax_Response
	 */
	public function verifySSNumber($appid,$token,$ss_num){
		$this->_verifyRequest($appid, $token);
		
		$ss_num = intval($ss_num);
		$r = Jax_Data_Source::getInstance()->getRecord("SSCustomers","`AcctNo`=\"42000$ss_num\"");
				
		if($r){
			//$fetch = array_flip(array("AcctNo","PointsBalance","FirstName","LastName","Email","BirthDate","Gender"));
			//$r = array_intersect_key($r, $fetch);
			
			$this->_log($appid, __METHOD__, "true");
			return Jax_Response::Valid(true);
		}
		$this->_log($appid, __METHOD__, "false");
		return Jax_Response::Valid(false);
	}
	
	public function login($appid,$token,$ss_num,$pwd){
		$this->_verifyRequest($appid, $token);
		
		$ss_num = intval($ss_num);
		$pwd = md5($pwd);
		$r = Jax_Data_Source::getInstance()->getRecord("SSCustomers","`AcctNo`=\"42000$ss_num\" AND `password`=\"$pwd\"");
		
		if($r){	
			$fetch = array_flip(array("AcctNo","PointsBalance","FirstName","LastName","Email","BirthDate","Gender"));
			$r = array_intersect_key($r, $fetch);
			
			$this->_log($appid, __METHOD__, $r);
			return Jax_Response::Valid($r);
		}
		$this->_log($appid, __METHOD__, "false");
		return Jax_Response::Valid(false);
	}
	
	public function massyLogin($appid,$token,$ms_num,$lastname){
		$this->_verifyRequest($appid, $token);
		
		$ss_num = intval($ss_num);
		$pwd = md5($pwd);
		$r = Jax_Data_Source::getInstance()->getRecord("ViewMsData","`acct_no`=\"42$ms_num\" AND `LastName`=\"$lastname\"");
		
		if($r){
			$fetch = array_flip(array("UUID","acct_no","XferDate","FirstName","LastName","Email","PointsBalance","PointsLastUpdated"));
			$r = array_intersect_key($r, $fetch);
				
			$this->_log($appid, __METHOD__, $r);
			return Jax_Response::Valid($r);
		}
		$this->_log($appid, __METHOD__, "false");
		return Jax_Response::Valid(false);
	}
	
	public function generateBarcode($appid,$token,$options,$type){
		$this->_verifyRequest($appid, $token);
		
		$barcodeOptions = $options;
		if(!is_array($barcodeOptions)) $barcodeOptions = array('text'=>'MISSING OPTIONS');
		
		if(@!is_array(@$rendererOptions)) $rendererOptions = array();
		
		$this->_log($appid, __METHOD__, $options);
		
		$barcodeOptions['drawText'] = false;
		
		header("Content-Disposition: inline; filename=\"".substr($barcodeOptions['text'],5).".png\"");
		
		Zend_Barcode::factory(
		$type, 'image', $barcodeOptions, $rendererOptions
		)->render();
		
		die();
	}
	
	public function eCard($appid,$token,$AcctNo){
		
		require_once APPLICATION_PATH.'/../library/Jax/Wideimage/WideImage.php';
		
		$this->_verifyRequest($appid, $token);
		
		if (substr($AcctNo, 0,2) != "42"){
			$AcctNo = "42".$AcctNo;
		}
		
		// update stats table
		try {
			$r = Jax_Data_Source::getInstance()
			->updateOnDuplicate("ApiAcctStats",array('AcctNo'=>$AcctNo,'sys_lasthit'=>date("Y-m-d H:i:s")));
			
			$this->_log($appid, __METHOD__, "Acct: $AcctNo Hit");
		} catch(Exception $e){
			
		}
		
	
		$template = WideImage::load("http://159.253.140.101/wp-content/themes/hi-lo/images/barcode-template.png");
		$r = Jax_Data_Source::getInstance()->getRecord("ViewMsData","`acct_no`=\"$AcctNo\"");
		
		if($r){	
			$acct = (object) $r;
			
			$tcanvas = $template->getCanvas();
			$tcanvas->useFont(APPLICATION_PATH.'/../library/Jax/System/Fonts/arialbd_0.ttf',10,$template->allocateColor(0,0,0));
			$tcanvas->writeText('left + 10', '115', $acct->FirstName." ".$acct->LastName);
			$tcanvas->writeText('left + 50', 'bottom - 10', $acct->acct_no);
			
			$barcode = WideImage::load("http://159.253.140.101/data-portal/public/service/barcodes/?options[text]=".$AcctNo);
			
			$merged = $template->merge($barcode,"right - 5","130",100);
			
			$this->_log($appid, __METHOD__, $AcctNo);
			
			header("Content-Disposition: attachment; filename=\"".$AcctNo.".png\"");
			$merged->output("png");
			
		} else {
			throw new Exception("Unable to load account info.");
		}
	
		die();
	}
	
	/**
	 * Ping back service to track eCard usage on mobile app
	 * 
	 * @param string $appid
	 * @param string $AcctNo
	 * @throws Exception
	 * @return Ambigous <multitype:, multitype:mixed int >
	 */
	public function acctHit($appid,$AcctNo){
		if(empty($AcctNo)) throw new Exception("Account not supplied");
		
		$r = Jax_Data_Source::getInstance()
			->updateOnDuplicate("ApiAcctStats",array('AcctNo'=>$AcctNo,'sys_lasthit'=>date("Y-m-d H:i:s")));
		
		$this->_log($appid, __METHOD__, "Acct: $AcctNo Hit");
		
		if($r){
			return Jax_Response::Valid(true);
		} else {
			return Jax_Response::Valid(false);
		}
	}
	
	/**
	 * Lookup item by barcode
	 * 
	 * @param string $appid
	 * @param string $token
	 * @param string $barcode
	 * 
	 * @throws Exception
	 */
	public function itemLookup($appid,$token,$barcode){
		$this->_verifyRequest($appid, $token);
		
		if(empty($barcode)) throw new Exception("Barcode not supplied");
		
		$barcode = @mysql_escape_string($barcode);
		
		$barcode = substr($barcode, 0,strlen($barcode)-1);
		
		$item = Jax_Data_Source::getInstance()->getRecord("WebItems","`barcode` like \"%$barcode\"");
		
		if($item){
			$this->_log($appid, __METHOD__, $item);
			
			// Append inventory
			$dbBarcode = $item['barcode'];
			
			$allowed = array("storename","qoh","lastUpdt");
			$inventory = Jax_Data_Source::getInstance()->getRecord("WebItemsInv","`barcode`=\"$dbBarcode\"",true);
			foreach ($inventory as &$record){
				foreach ($record as $h=>$v){
					if(!in_array($h, $allowed)){
						unset($record[$h]);
					}
				}
			}
			
			$item['inventory'] = $inventory;
			
			return Jax_Response::Valid($item);
		} else {
			$this->_log($appid, __METHOD__, array("barcode"=>$barcode,"result"=>false));
			
			return Jax_Response::Valid(false);
		}
	}
	
	/**
	 * Generate a token
	 * 
	 * @param string $appid
	 * @param string $secret
	 * @return string $token
	 */
	private function _genToken($appid,$secret){
		// First check if a valid token exists, return this.
		$v = Jax_Data_Source::getInstance()->getRecord("ApiKeys","`app_id`=\"$appid\" AND `secret`=\"$secret\" AND `status`=1 AND `valid_until` > NOW()");
		if($v){
			return $v['token'];
		}
		
		$range = array_merge(range(0, 9),range('a','z'),range('A','Z'));
		$len = 90;
		$token = "";
		
		while (strlen($token) < $len){
			$token .= $range[rand(0,count($range)-1)];
		}
		
		$token = base64_encode($token);
		
		Jax_Data_Source::getInstance()->updateRecord("ApiKeys","`app_id`=\"$appid\"",
		array('token'=>$token,'valid_until'=>date("Y-m-d H:i:s",time()+3600)),1);
		
		return $token;
	}
	
	private function _verifyRequest($appid,$token){
		$v = Jax_Data_Source::getInstance()->getRecord("ApiKeys","`app_id`=\"$appid\" AND `token`=\"$token\" AND `status`=1 AND `valid_until` > NOW()");
		
		if($v){
			return true;
		}
		
		throw new Exception('Invalid Token');
	}
	
	/**
	 * Used to validate a token request. Appid and Secret must exist in DB and be enabled.
	 * 
	 * @param string $appid
	 * @param string $secret
	 * @throws Exception
	 * @return boolean
	 */
	private function _validateTokenRequest($appid,$secret){
		$v = Jax_Data_Source::getInstance()->getRecord("ApiKeys","`app_id`=\"$appid\" AND `secret`=\"$secret\" AND `status`=1");
		
		if($v){
			return true;
		}
		
		throw new Exception('Invalid Token Request');
	}
	
	/**
	 * @param string $appid (app_id)
	 * @param string $m (service_call)
	 * @param string $r (serialized response)
	 */
	private function _log($appid,$m,$r){
		try{
			$ip = $_SERVER['REMOTE_ADDR'];
			Jax_Data_Source::getInstance()->addRecord("ApiLogs",array('app_id'=>$appid,'caller'=>$ip,'service_call'=>$m,'response'=>serialize($r)));
		} catch(Exception $e){}
	}
}