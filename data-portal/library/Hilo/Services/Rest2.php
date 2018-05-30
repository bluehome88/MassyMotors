<?php
class Hilo_Services_Rest2 {
	protected $_responseHandler;
	
	protected $_apiKey = "79999a4f2ac803668bf726bcd8a096fd";
	protected $_apiUser = "kirton";
	protected $_apiPwd = "k5rjAxNgdLhRfGI";
	protected $_apiEp = "http://demo.linkuptt.com/restserver/index.php/api/massystoresappapi/";
	
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
	public function getToken($appid,$secret,$device,$version,$mid){
		$appid = @mysql_escape_string($appid);
		$secret = @mysql_escape_string($secret);
		
		$this->_validateTokenRequest($appid, $secret);
		
		$token = $this->_genToken($appid, $secret);
		
		$this->_log($appid,__METHOD__, $token,$device,$version,$mid);
		return Jax_Response::Valid($token);
	}
	
	public function massyLogin($appid,$token,$ms_num,$lastname,$device,$version,$mid){
		$this->_verifyRequest($appid, $token);
		
		if (substr($ms_num, 0,2) != "42"){
			$ms_num = "42".$ms_num;
		}
		
		$r = Jax_Data_Source::getInstance()->getRecord("ViewMsData","`acct_no`=\"$ms_num\" AND `LastName`=\"$lastname\"");
		
		if($r){
			$fetch = array_flip(array("UUID","acct_no","XferDate","FirstName","LastName","Email","PointsBalance","PointsLastUpdated"));
			$r = array_intersect_key($r, $fetch);
				
			$this->_log($appid, __METHOD__, $r,$device,$version,$mid);
			return Jax_Response::Valid($r);
		}
		$this->_log($appid, __METHOD__, "false",$device,$version,$mid);
		return Jax_Response::Error("Invalid Credentials");
	}
	
	public function generateBarcode($appid,$token,$options,$type,$device,$version,$mid){
		$this->_verifyRequest($appid, $token);
		
		$barcodeOptions = $options;
		if(!is_array($barcodeOptions)) $barcodeOptions = array('text'=>'MISSING OPTIONS');
		
		if(@!is_array(@$rendererOptions)) $rendererOptions = array();
		
		$this->_log($appid, __METHOD__, $options,$device,$version,$mid);
		
		$barcodeOptions['drawText'] = false;
		
		header("Content-Disposition: inline; filename=\"".substr($barcodeOptions['text'],5).".png\"");
		
		Zend_Barcode::factory(
		$type, 'image', $barcodeOptions, $rendererOptions
		)->render();
		
		die();
	}
	
	public function eCard($appid,$token,$AcctNo,$device,$version,$mid){
		
		require_once APPLICATION_PATH.'/../library/Jax/Wideimage/WideImage.php';
		
		$this->_verifyRequest($appid, $token);
		
		if (substr($AcctNo, 0,2) != "42"){
			$AcctNo = "42".$AcctNo;
		}
		
		// update stats table
		try {
			$r = Jax_Data_Source::getInstance()
			->updateOnDuplicate("ApiAcctStats",array('AcctNo'=>$AcctNo,'sys_lasthit'=>date("Y-m-d H:i:s")));
			
			$this->_log($appid, __METHOD__, "Acct: $AcctNo Hit",$device,$version,$mid);
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
			
			$this->_log($appid, __METHOD__, $AcctNo,$device,$version,$mid);
			
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
	public function acctHit($appid,$AcctNo,$device,$version,$mid){
		if(empty($AcctNo)) throw new Exception("Account not supplied");
		
		$r = Jax_Data_Source::getInstance()
			->updateOnDuplicate("ApiAcctStats",array('AcctNo'=>$AcctNo,'sys_lasthit'=>date("Y-m-d H:i:s")));
		
		$this->_log($appid, __METHOD__, "Acct: $AcctNo Hit",$device,$version,$mid);
		
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
	public function itemLookup($appid,$token,$barcode,$device,$version,$mid){
		$this->_verifyRequest($appid, $token);
		
		if(empty($barcode)) throw new Exception("Barcode not supplied");
		
		$barcode = @mysql_escape_string($barcode);
		
		$barcode = substr($barcode, 0,strlen($barcode)-1);
		
		$item = Jax_Data_Source::getInstance()->getRecord("WebItems","`barcode` like \"%$barcode\"");
		
		if($item){
			$this->_log($appid, __METHOD__, $item,$device,$version,$mid);
			
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
			$this->_log($appid, __METHOD__, array("barcode"=>$barcode,"result"=>false),$device,$version,$mid);
			
			return Jax_Response::Valid(false);
		}
	}
	
	public function sendShoppingList($appid,$token,$device,$version,$mid,$body,$to,$from,$subject){
		$this->_verifyRequest($appid, $token);
			
			$toCheck = Jax_Mail::mailgunValidation($to);
			if($toCheck->is_valid){
				
				$body .= "<hr/><p>Powered by Massy Stores&reg;. Download Massy Stores Shopping List for your iPhone&#8482;, iPad&reg; or Android&#8482;. Experience the convenience of using your list as you shop and ensure you never miss an item again.</p>
 
<p>The official Massy Stores mobile app puts the supermarket in your pocket, with planning tools, shopping information, news, special offers and more - everything you need for an enjoyable experience.</p>
<p>To get it, just log into your mobile app store and search for Massy Stores&reg;.</p>";
				
				$fromCheck = Jax_Mail::mailgunValidation($from);
				if(!$fromCheck->is_valid) $from = "no-reply@mg.massystorestt.com";
	
				$config = array('auth' => 'login',
						'username' => 'postmaster@mg.massystorestt.com',
						'password' => '0ced2c3b3be55c954f904f68864fb35f');
				
				$tr = new Zend_Mail_Transport_Smtp('smtp.mailgun.org',$config);
					
				Zend_Mail::setDefaultTransport($tr);
				$mail = new Zend_Mail();
				
				$mail
					->clearFrom()
					->setFrom($from)
					->addTo($to)
					->setSubject($subject)
					->setBodyHtml($body)
					->send();
				
				$this->_log($appid, __METHOD__,$to, $device, $version, $mid);
				
				return Jax_Response::Valid(true);
			} 
		$this->_log($appid, __METHOD__,false, $device, $version, $mid);
				
		return Jax_Response::Valid(false);
	}
	
	public function resonanceLogin($appid,$token,$username,$password){
		$this->_verifyRequest($appid, $token);
		
		$data = "username=$username&password=$password";
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $this->_apiEp."/login");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-API-KEY:'.$this->_apiKey,'Accept: application/json'));
		curl_setopt($ch, CURLOPT_USERPWD, $this->_apiUser.":".$this->_apiPwd);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		$output = trim(curl_exec($ch));
		$output = json_decode($output);
		
		return Jax_Response::Valid($output);
	}
	
	public function getcardnumber($appid,$token,$customerid){
		$this->_verifyRequest($appid, $token);
	
		$data = "customerid=$customerid";
	
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_URL, $this->_apiEp."/getcardnumber?".$data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-API-KEY:'.$this->_apiKey,'Accept: application/json'));
		curl_setopt($ch, CURLOPT_USERPWD, $this->_apiUser.":".$this->_apiPwd);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	
		$output = trim(curl_exec($ch));
		$output = json_decode($output);
	
		return Jax_Response::Valid($output);
	}
	
	public function getcustomerbalance($appid,$token,$customerid){
		$this->_verifyRequest($appid, $token);
	
		$data = "customerid=$customerid";
	
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_URL, $this->_apiEp."/getcustomerbalance?".$data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-API-KEY:'.$this->_apiKey,'Accept: application/json'));
		curl_setopt($ch, CURLOPT_USERPWD, $this->_apiUser.":".$this->_apiPwd);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	
		$output = trim(curl_exec($ch));
		$output = json_decode($output);
	
		return Jax_Response::Valid($output);
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
	private function _log($appid,$m,$r,$device,$version,$mid){
		try{
			$ip = $_SERVER['REMOTE_ADDR'];
			Jax_Data_Source::getInstance()->addRecord("ApiLogs2",
			array(
				'app_id'=>$appid,
				'caller_ip'=>$ip,
				'service_call'=>$m,
				'response'=>serialize($r),
				'mid'=>$mid,
				'device_id'=>$device,
				'app_version'=>$version
			));
		} catch(Exception $e){}
	}
}