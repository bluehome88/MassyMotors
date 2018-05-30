<?php
/**
 * Determines what avatar to render based on 'Sex' defined in the user object.
 * Male avatar is rendered by default.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_User_Avatar
{
	/**
	 * The current users' gender
	 * 
	 * @var string
	 */
	private $_sex;
	
	/**
	 * Default constructor
	 * Accepts a Jax_User_Abstract object and retrieves the sex information.
	 * 
	 * @param Jax_User_Abstract $userClass
	 */
	public function __construct(Jax_User_Abstract $userClass)
	{
		$userObjectResponse = $userClass->getUserObject();
		
		if (is_array($userObjectResponse) && array_key_exists('response', $userObjectResponse))
		{
			$userObject = $userObjectResponse['response'];
			
			$this->_sex = $userObject->Sex;
		}
	}
	
	/**
	 * Renders the avatar.
	 * 
	 * @return null
	 */
	public function render()
	{		
		switch (strtoupper($this->_sex)){
			case 'F':
				$file = 'auth_user_female.png';
			break;
			
			default:
				$file = 'auth_user_male.png';
			break;
		}
		
		$publicPath = Jax_Config::getPublicPath();
		$im = @imagecreatefrompng(dirname(__FILE__).'/../../../'.$publicPath.'/_assets/js/Jax/Jax/Jax.authUser/'.$file);
		
		Jax_Utilities_RenderImage::run($im);
	}
}