<?php
/**
 * Active Directory Utilities
 * 
 * Various functions for working with active directory.
 * Wraps most functionality found in the adLDAP class.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Utilities_ActiveDirectory
{
	
	/**
	 * Get instance of adLDAP as defined by applications.
	 * 
	 * @return adLDAP
	 * @throws Exception
	 */
	private static function adLDAP(){
		$adLDAPWrapper = Jax_Data_Source::getInstance()
			->getDataSourceWrapper()
			->getDataSourceObject(Jax_Data_Source_Types::LDAP,Jax_Data_Source_Types::KEY_adLDAP);
		
		$adLDAP = $adLDAPWrapper->adLDAP();
			
		if ($adLDAP instanceof adLDAP){
			return $adLDAP;
		}
		
		throw new Exception("Invalid object instance. Expecting instance of adLDAP.");
	}
	
	/**
	 * Get all security groups in active directory.
	 * 
	 * @return array
	 */
	public static function getAllSecurityGroups(){
		$adLDAP = self::adLDAP();
		return $adLDAP->group()->allSecurity(true);
	}
	
	/**
	 * Determines if the group is used for ACL
	 * 
	 * @param string $Group
	 * @return boolean
	 */
	public static function isACLGroup($Group){
		if (substr($Group, 0,3) == "ACL") return true;
		
		return false;
	}
	
	/**
	 * Get group members
	 * 
	 * @param string $Group
	 * @return array
	 */
	public static function getGroupMembers($Group){
		$adLDAP = self::adLDAP();
		
		$info = $adLDAP->group()->info($Group, array("member"));
		
		// Workaround where "ACL" is prepended to an existing group name. Parse out the "ACL " and try again.
		if (array_key_exists("count", $info) && $info['count'] == 0){
			$info = $adLDAP->group()->info(substr($Group,4), array("member"));
		}
		
		// Get Group members
		if (array_key_exists("count", $info) && $info['count'] > 0){
			$members = @$info[0]['member'];
			
			return $members;
		}
		
		return array();
	}
	
	/**
	 * Get a username, searching by fullname.
	 *  
	 * @param string $fullname
	 * @return array
	 */
	public static function getUsernameByFullName($fullname){
		$adLDAP = self::adLDAP();
		return $adLDAP->user()->find(true,"CN",$fullname);
	}
	
	/**
	 * Get AD user info
	 * @param string $username
	 * @return array
	 */
	public static function getUserInfo($username,$filter = null){
		$adLDAP = self::adLDAP();
		return $adLDAP->user()->info($username,$filter);
	}
	
	/**
	 * Get a list of ACL groups to which the user belongs.
	 */
	public static function getUserACLGroups($username){
		$info = self::getUserInfo($username,array("memberof"));
		$groups = $info[0]['memberof'];
		
		$ACLGroups = array();
		
		for($i=0;$i<$groups['count'];$i++){
			$gp = self::parseCNDC($groups[$i]);
			
			if (self::isACLGroup($gp['CN'][0])) $ACLGroups[] = self::parseOutACL($gp['CN'][0]);
		}
		
		return $ACLGroups;
	}
	
	public static function parseCNDC($string){
		$parts = explode(",", $string);
		$result = array();
		foreach ($parts as $part){
			$key = substr($part, 0, 2);
			$value = substr($part, 3);
			
			$result[$key][] = $value;
		}
		
		return $result;
	}
	
	public static function parseOutACL($string){
		return str_replace(" ","", substr($string,3));
	}
}