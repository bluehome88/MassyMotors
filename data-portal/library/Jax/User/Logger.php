<?php
class Jax_User_Logger
{
	public static function log($details = null, $user=null){
		return Jax_Data_Source::getInstance()->log($details,$user);
	}
}