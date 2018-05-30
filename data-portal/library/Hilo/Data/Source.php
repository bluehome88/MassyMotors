<?php
class Hilo_Data_Source extends Jax_Data_Abstract
{
	public function __construct()
	{
		$this->addDataSourceObject(new Hilo_Data_Source_Mysql(),Jax_Data_Source_Types::DB);
	}
}