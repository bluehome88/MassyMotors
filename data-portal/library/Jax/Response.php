<?php
/**
 * Class used to return data to the client.
 * All classes should return one of these static methods if their data is to be passed back directly to the client.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Response
{
	/**
	 * These are all valid keys that can be returned to the client.
	 * 
	 * @var string
	 */
	const KEY_ERROR = 'error';
	const KEY_CODE = 'code';
	const KEY_RESPONSE = 'response';
	const KEY_REDIRECT = 'redirect';
	const KEY_DELAY = 'delay';
	const KEY_MESSAGE = 'message';
	const KEY_LOAD = 'load';
	const KEY_CALLBACK = 'callback';
	const KEY_AJAX = 'ajax';
	
	/**
	 * Use to return an error.
	 * 
	 * @param mixed $data
	 * @param int $code
	 * @return array
	 */
	public static function Error($data,$code=0){
		return array(self::KEY_ERROR=>$data,self::KEY_CODE=>$code);
	}
	
	/**
	 * Use to return a valid response.
	 * 
	 * @param mixed $data
	 * @param int $code
	 * @return array
	 */
	public static function Valid($data,$code=1){
		return array(self::KEY_RESPONSE=>$data,self::KEY_CODE=>$code);
	}
	
	/**
	 * Use to send a signal to the client to redirect to another location. 
	 * Optional message and a delay before redirect can be passed.
	 * 
	 * @param string $url
	 * @param string $message
	 * @param +ve int $delay
	 * @return array
	 */
	public static function Redirect($url,$message='',$delay=5)
	{
		return array(self::KEY_REDIRECT=>$url,self::KEY_DELAY=>$delay,self::KEY_MESSAGE=>$message);
	}
	
	/**
	 * Use to trigger the client to load a plugin.
	 * 
	 * @param array $pluginList
	 * @return array
	 */
	public static function Load(Array $pluginList){
		return array(self::KEY_LOAD=>$pluginList);
	}
	
	/**
	 * Use to return a server side exception to the client.
	 * 
	 * @param Exception $e
	 * @return array
	 */
	public static function Exception(Exception $e){
		return self::Error($e->getMessage(),$e->getCode());
	}
	
	/**
	 * Use to return callback code to client side app.
	 * 
	 * @param string $code
	 * @return array
	 */
	public static function Callback($code){
		return array(self::KEY_CALLBACK=>$code);
	}
	
	public static function Ajax($url,$opts=NULL,$method="GET"){
		return array(self::KEY_AJAX=>array($url,$opts,$method));
	}
}