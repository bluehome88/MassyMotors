<?php
/**
 * Utility Function - Recursively search an array (multiple options for searching)
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Utilities_ArraySearchRecursive implements Jax_Utilities_Interface
{
	/**
	 * Stores the return value.
	 * 
	 * @var array
	 */
	protected static $_path = array();
	
	/**
	 * Runs the recursive search algorithm
	 */
	public static function run()
	{
		$params = func_get_args();
		$haystack = $params[1]; // Multi array
		$needle = $params[0]; // Search value
		$strict = $params[2]; // Strict mode. Used to determine how keys or values are compared. either == or ===
		$path = $params[3]; // Should be empty array in initial method call
		$keySearch = @$params[4]; // Boolean - TRUE: Matches keys instead of haystack values
		
		if( !is_array($haystack) ) {
        	return false;
    	}
 
    	foreach( $haystack as $key => $val ) {
    		if (@$keySearch === true){
		        if( is_array($val) && $subPath = self::run($needle, $val, $strict, $path, $keySearch) ) {
		            $path = array_merge($path, array($key), $subPath);
		            return $path;
		        } elseif( (!$strict && $key == $needle) || ($strict && $key === $needle) ) {
		            $path[] = $key;
		            return $path;
		        }
    		} else {
    			if( is_array($val) && $subPath = self::run($needle, $val, $strict, $path, $keySearch) ) {
		            $path = array_merge($path, array($key), $subPath);
		            return $path;
		        } elseif( (!$strict && $val == $needle) || ($strict && $val === $needle) ) {
		            $path[] = $key;
		            return $path;
		        }
    		}
    	}
    	
    	return false;
	}
}