<?php
class Jax_Data_Moodle_Rest extends Jax_Data_Source_Abstract 
{
	protected $_TOKEN;
	protected $_MOODLE_BASE;
	protected $_MOODLE_REST_SERVER;
	protected $_CURL;
	
	const TOKEN = "wstoken";
	const FCN = "wsfunction";
	const FORMAT = "moodlewsrestformat";
	
	public function __call($function, $args = array()) {
		if(!is_array($args[0])) throw new Exception("Moodle API: Invalid request format.");
		
		$args[0][self::FCN] = $function;
		$args[0][self::TOKEN] = $this->_TOKEN;
		
		if(!isset($args[0][self::FORMAT])) $args[0][self::FORMAT] = 'json';
		
		if(!isset($args[1])){
			$method = "GET";
		} else {
			$method = $args[1];
		}
		
		$this->_CURL->setopt(CURLOPT_RETURNTRANSFER, TRUE);

		switch ($method){
			case "POST":
				$this->_CURL->post($this->_MOODLE_BASE.$this->_MOODLE_REST_SERVER,$args[0]);
				break;
				
			case "GET":
				$this->_CURL->get($this->_MOODLE_BASE.$this->_MOODLE_REST_SERVER,$args[0]);
				break;
				
			case "PUT":
				$this->_CURL->put($this->_MOODLE_BASE.$this->_MOODLE_REST_SERVER,$args[0]);
				break;
					
			case "DELETE":
				$this->_CURL->delete($this->_MOODLE_BASE.$this->_MOODLE_REST_SERVER,$args[0]);
				break;
				
			default:
				$this->_CURL->get($this->_MOODLE_BASE.$this->_MOODLE_REST_SERVER,$args[0]);
				break;
		}
		
		if($args[0][self::FORMAT] == "json"){
			$r = json_decode($this->_CURL->response);
			
			if(is_object($r) && $r->exception){				
				throw new Exception($r->exception.": ".$r->message);
			}
		}

		return $this->_CURL->response;
	}
	
	public function __construct($token,$moodle_base,$moodle_rest_server){
		$this->_TOKEN = $token;
		$this->_MOODLE_BASE = $moodle_base;
		$this->_MOODLE_REST_SERVER = $moodle_rest_server;
		
		$this->_CURL = new Jax_Curl();
	}
	
	public function curl(){
		return $this->_CURL;
	}
}