<?php
class Hilo_ApiController extends Zend_Controller_Action 
{
	protected $_responseHandler;
	
	public function init(){
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if(!isset($_COOKIE["Jax-Active-Application"])){
			setcookie("Jax-Active-Application","Hilo.js",null,"/");
			header("Location: ".BASEURL."Hilo/Api/web?".$_SERVER['QUERY_STRING']);
		}
		
		$ds = new Hilo_Data_Source();
		
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
	}
	
	public function restAction() {
		$server = new Zend_Rest_Server();
		$server->setClass("Hilo_Services_Rest");
		$server->handle();
	}
	
	public function webAction(){
		$params = $this->getRequest()->getParams();	
		$dataClass = "Hilo_Services_Rest";
		
		if (isset($params['device'])){
			$dataClass = "Hilo_Services_Rest2";
		}

		if (!isset($params['method'])) {
			return $this->_sendResponse(Jax_Response::Error("Method not specified"));
		}
		
		$rmethod = $params['method'];
		$ref = new ReflectionClass($dataClass);
		$methods = $ref->getMethods();
		$meta = array();
		
		foreach ($methods as $method){
			if(substr($method->name, 0,1) == "_") continue;
			
			$mparams = $method->getParameters();
			
			$zparams = array();
			foreach ($mparams as $mp){
				$zparams[] = $mp->name;
			}
			
			$meta[$method->name] = $zparams;
		}
		
		if (array_key_exists($rmethod, $meta)){
			$flipped = array_flip($meta[$rmethod]);
			$cparams = array_intersect_key($params, $flipped);
			
			foreach ($cparams as $k=>$v){
				$cparams[$flipped[$k]] = $v;
				unset($cparams[$k]);
			}
			ksort($cparams);
		
			if(count($flipped) != count($cparams)) return $this->_sendResponse(Jax_Response::Error("Invalid Request. Parameters missing."));
			
			$response = call_user_func_array(array(new $dataClass,$rmethod), $cparams);
			return $this->_sendResponse($response);
		} 
		
		else {
			return $this->_sendResponse(Jax_Response::Error("Invalid Method Specified"));
		}
	}
	
	protected function _sendResponse($response){
		$params = $this->getRequest()->getParams();
		if(isset($params['callback'])){
			$response['jsonp'] = $params['callback'];
		}
		
		$this->_responseHandler
		->setResponseData($response)
		->send();
	}
}
