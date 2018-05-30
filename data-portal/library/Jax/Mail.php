<?php
/**
 * Jax Mail. Configures and returns Zend_Mail class.
 * Must define options for use.
 *
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Mail {

	/**
	 * Retrieve the configured Zend_Mail Class.
	 * 
	 * @return Zend_Mail
	 */
	public static function getZendMailClass(){
		$opts = Jax_App_Options::getInstance();
		
		$config = array('auth' => 'login',
				'username' => $opts->mail_user,
				'password' => $opts->mail_pwd);
		
		$tr = new Zend_Mail_Transport_Smtp($opts->mail_server,$config);
			
		Zend_Mail::setDefaultTransport($tr);
		$mail =  new Zend_Mail();
		$mail->setFrom($opts->notification_email);
		
		return $mail;
	}
	
	/**
	 * Verify an email using Mailgun.com's API
	 * 
	 * @param string $email
	 * @return boolean|mixed
	 * 
	 * Return's and object. Can verify validity using $result->is_valid
	 */
	public static function mailgunValidation($email){
		if(!filter_var($email,FILTER_VALIDATE_EMAIL)) return false;
		
		$data = "address=$email";
		
		$cli = new Zend_Http_Client();
		$res = $cli->setUri('https://api.mailgun.net/v2/address/validate?'.$data)
			->setAuth('api_key','pubkey-a01efb6db128e6a0e8dc6c7b55e7431a',zend_http_client::AUTH_BASIC)
			->request(Zend_Http_Client::GET);
		$output = $res->getRawBody();
		return json_decode($output);
	}
}