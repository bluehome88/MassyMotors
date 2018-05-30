<?php
/**
 * Utility Function - Used to build partial search MySQL Query
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Utilities_SearchQueryBuilder implements Jax_Utilities_Interface
{	
	public static function run()
	{
		$params = func_get_args();
		$q = $params[0];
		$searchCols = $params[1];
		
		$query = "(";
		
		$terms = explode(" ",$q);
		
		if(is_array($searchCols)){
			foreach($terms as $term){
				$term = mysql_escape_string($term);
				foreach($searchCols as $col){
					$query .= "`$col` LIKE '%$term%' OR";
				}
				$query = substr($query, 0,strlen($query)-3).") AND (";
			}
			$query = substr($query, 0,strlen($query)-6);
			
			return $query;
		}
		
		return null;
	}
}