<?php
/**
 * Module Administration
 * Methods related to administration of modules (ACL Resources) within the sytem.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link http://www.nerdsconsulting.com
 * @package Jax
 * @version 1.0.0
 */
class Jax_Acl_Resource_Controlpanel_Acladmin_Moduleadmin extends Jax_Acl_Resource_Controlpanel_Acladmin
{
	protected $_resourceId = Jax_Acl_Constants::RESOURCE_CP_ACL_MODULEADMIN;
	protected $_reserved = array("abstract","resource");
	
	public function __construct(){
		$this->_config[self::DISPLAY_NAME] = 'ACL Modules';
		$this->_config[self::ICON] = 'icon-cube';
	}
	
	public function getModuleInfoProc($mod){		
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_READ);
		
		$info = Jax_Data_Source::getInstance()->getRecord("AclModules","`module`='$mod'");
		foreach ($info as &$v){
			if(!is_null($v))
				$v = stripslashes($v);
		}
		
		return Jax_Response::Valid($info);
	}
	
	protected function _write($params,$request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_WRITE);
	
		if($request->isPost()){
			$data = $request->getParam('mod');
			
			if($data){
				$module = Jax_Utilities_ModuleFilesTemplate::setTemplateData($data);
				$mod = $module->module;
				$icon = $module->icon;
				$modid = $module->modid;
	
				if(in_array(strtolower($mod), $this->_reserved)) return Jax_Response::Error('Module name '.$mod.' is reserved. Please use another.');
	
				$r = Jax_Data_Source::getInstance()->addRecord("AclModules",array('module'=>$modid));
				if($r){	
					$this->_createFileTemplates($module);
						
					$this->_refreshACL();
					return Jax_Response::Valid(1);
				} else {
					return Jax_Response::Error('Unable to add module.');
				}
			}
		}
	
		return Jax_Response::Error("Invalid Request");
	}
	
	protected function _append($params, $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_APPEND);
	
		if($request->isPost()){
			if(!isset($params['m'])) return Jax_Response::Error("Module not specified!");
				
			if(isset($params['desc'])){
				$desc = mysql_escape_string($params['desc']);
				$r = Jax_Data_Source::getInstance()->updateRecord("AclModules","`module`='".$params['m']."'",array('description'=>$desc));
				if($r) return Jax_Response::Valid(1);
				return Jax_Response::Error("Unable to update description.");
			}
				
			if(isset($params['p'])){
				$parent = mysql_escape_string($params['p']);
				if($parent == "") $parent = null;
				$r = Jax_Data_Source::getInstance()->updateRecord("AclModules","`module`='".$params['m']."'",array('parent'=>$parent));
				if($r) {
					$this->_refreshACL();
					return Jax_Response::Valid(1);
				}
				return Jax_Response::Error("Unable to set parent module.");
			}
		}
	
		return Jax_Response::Error("Invalid Request");
	}
	
	protected function _delete($params, $request){
		Jax_Utilities_ResourceAccessChecker::run($this->_resourceId,Jax_Acl_Constants::ACCESS_DELETE);
	
		if($request->isPost()){
			if(!isset($params['m'])) return Jax_Response::Error("Module not specified!");
				
			$r = Jax_Data_Source::getInstance()->deleteRecord("AclModules","`module`='".$params['m']."'");
			if($r) {
				$file = APPLICATION_PATH.'/../library/'.APPNAMESPACE.'/Acl/Resource/'.$params['m'].'.php';
				if(file_exists($file))
					unlink($file);
	
				$this->_refreshACL();
				return Jax_Response::Valid(1);
			}
			return Jax_Response::Error("Unable to remove module.");
		}
		return Jax_Response::Error("Invalid Request");
	}
	
	private function _createFileTemplates($module){
		$module->createResourceClass();
		$module->createControllerClass();
		$module->createDefaultView();
		$module->createFrontendTemplate();
	}
}