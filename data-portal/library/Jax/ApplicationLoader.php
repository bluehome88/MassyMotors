<?php
/**
 * Reads and returns a list of available apps from the Jax installation.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_ApplicationLoader
{
	
	/**
	 * Returns a list of available apps.
	 * 
	 * @return array
	 */
	public static function getApps($metro = false){
		$self = new self();
		return array("modules"=>$self->_listConfigs($metro));
	}
	
	/**
	 * Returns a list of ini files for all applications
	 * 
	 * @return array
	 */
	private function _listConfigs($metro = false){
		$libs = $this->_listJaxLibs($metro);
		
		$cfgDir = APPLICATION_PATH.'/../library/';
		
		if(is_dir($cfgDir)){

			$ini = array();
			
			foreach($libs as $NS){
				if($NS != "jax"){
					$nsDir = $cfgDir.$NS;
					if(is_dir($nsDir)){
						$cfgFile = $nsDir."/".$NS.".ini";
						if(file_exists($cfgFile)){
							$d = parse_ini_file($cfgFile,true);
							if(!isset($d['config'])){
								$d['config'] = array(); 
							}
							if (isset($d['config']['icon'])){
								if($metro === true){
									$d['config']['iconPath'] = BASEURL.'app/'.$NS.'/';
								} else {
									$d['config']['iconPath'] = BASEURL.'_assets/js/'.$NS.'/';
								}
							}
							$ini[$NS] = array("config"=>$d['config']);
						}
					}
				}
			}
			return $ini;
		}
	}
	
	/**
	 * Returns a list of all available Jax (JS) namespaces.
	 * 
	 * @return array
	 */
	private function _listJaxLibs($metro = false){
		$publicDir = Jax_Config::getPublicPath();
		
		if($metro === true){
			$cfgDir = APPLICATION_PATH.'/../'.$publicDir.'/app';
		} else {
			$cfgDir = APPLICATION_PATH.'/../'.$publicDir.'/_assets/js';
		}
		
		$libs = array();
		if(is_dir($cfgDir)){
			$dh = opendir($cfgDir);

			while (($file = readdir($dh)) != false){
				if(!in_array($file,array(".",".."))){
					if (is_dir($cfgDir.'/'.$file) && strtolower($file)!='jax'){
						$libs[] = $file;
					}
				}
			}
			closedir($dh);
		}
		return $libs;
	}
}