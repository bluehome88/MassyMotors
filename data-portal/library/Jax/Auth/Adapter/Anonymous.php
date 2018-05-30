<?php
class Jax_Auth_Adapter_Anonymous extends Jax_Auth_Adapter_Abstract
{
	const IDENTITY = 'public';
	
	/* (non-PHPdoc)
	 * @see Jax_Auth_Adapter_Abstract::getUserRole()
	 */
	protected function getUserRole($username) {
		return self::IDENTITY;
	}

	/* (non-PHPdoc)
	 * @see Zend_Auth_Adapter_Interface::authenticate()
	 */
	public function authenticate() {
		$this->setCredentials(
			array(
				Jax_Auth_Constants::FIELD_USERNAME=>self::IDENTITY,
				Jax_Auth_Constants::FIELD_PASSWORD=>'',
				Jax_Auth_Constants::FIELD_COOKIE=>'Jax-Auth-Anonymous'
			)
		);
		
		$this->_setAuthenticatedSession();
		
		return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, self::IDENTITY);
	}
}