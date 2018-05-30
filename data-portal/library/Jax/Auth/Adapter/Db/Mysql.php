<?php
class Jax_Auth_Adapter_Db_Mysql extends Jax_Auth_Adapter_Db
{
	protected function _configureAdapter(){
		$userTable = new Jax_Models_AuthUsers();
		
		$this
			->setTableName($userTable->info('name'))
			->setIdentityCol('username')
			->setCredentialCol('password');
	}
}