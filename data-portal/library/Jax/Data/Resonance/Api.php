<?php
class Jax_Data_Resonance_Api extends Jax_Data_Source_Abstract
{
	/**
	 * APPID assigned by Resonance
	 * @var string
	 */
	protected $_APPID;
	
	/**
	 * Resonance account username (supplied by the end user)
	 * @var string
	 */
	protected $_USER;
	
	/**
	 * Resonance account password (supplied by the end user)
	 * @var string
	 */
	protected $_PASSWORD;
	
	/* (non-PHPdoc)
	 * @see Jax_Data_Source_Abstract::__call()
	 */
	public function __call($method, $args) {
		if(method_exists($this, "API_".$method)){
			return call_user_func_array(array($this,"API_".$method), $args);
		} else {
			return null;
		}
	}
	
	/**
	 * Set the user. Provides a fluent interface.
	 * 
	 * @param string $user
	 * @return Jax_Data_Resonance_Api
	 */
	public function setUser($user) {
		$this->_USER = (string) $user;
		return $this;
	}
	
	/**
	 * Set the password. Provides a fluent interface.
	 * 
	 * @param string $password
	 * @return Jax_Data_Resonance_Api
	 */
	public function setPassword($password) {
		$this->_PASSWORD = (string) $password;
		return $this;
	}
	
	/**
	 * API Call to obtain security token
	 */
	protected function API_GetToken(){
		$params = array();
		$params["AppID"] = $this->_APPID;
		$params["User"] = $this->_USER;
		$params["Password"] = $this->_PASSWORD;
		$params["Timestamp"] = $this->_generateTimestamp();
		$params["Nonce"] = $this->_generateNonce();
	
		return Jax_Utilities_RestHelper::run("http://api.yoozpay.com/gettoken.php",$params,"POST");
	}
	
	/**
	 * Redirect to Yooz payment platform
	 */
	protected function API_Redirect($token,$amount,$currency,$desc,$callback){
		$params = array();
		$params["AppID"] = $this->_APPID;
		$params["TokenID"] = $token;
		$params["txnAmount"] = $amount;
		$params["txnCurr"] = $currency;
		$params["txnDesc"] = $desc;
		$params["returnurl"] = $callback;

		return Jax_Utilities_RestHelper::run("http://api.yoozpay.com/login.php",$params,"GET","redirect");
	}
	
	/**
	 * Verify a YOOZ transaction
	 */
	protected function API_Verify($token){
		$params = array();
		$params["AppID"] = $this->_APPID;
		$params["TokenID"] = $token;
	
		return Jax_Utilities_RestHelper::run("http://api.yoozpay.com/querytxn.php",$params,"GET");
	}
	
	/**
	 * Make timestamp as per Resonance requirements
	 * 
	 * @return string
	 */
	protected function _generateTimestamp(){
		return date("Y-m-dOh:i:s");
	}

	/**
	 * Generate a random int string of length 16 digits
	 * 
	 * @return string
	 */
	protected function _generateNonce(){
		$mt = (string) microtime(true);
		$str = explode(".", $mt);
		$rand = (string) rand($str[1],$str[0]);
		$len = strlen($rand);
		if ($len < 16){
			while ($len < 16){
				$rand .= (string) rand(0, 9);
				$len = strlen($rand);
			}
		}
		
		return $rand;
	}
}