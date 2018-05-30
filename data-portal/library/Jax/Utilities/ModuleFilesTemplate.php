<?php
/**
 * Utility Function - Creates module template files
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @package Jax
 */
class Jax_Utilities_ModuleFilesTemplate implements Jax_Utilities_Interface
{	
	protected static $_instances = array();
	
	protected $icon;
	protected $modid;
	protected $display_name;
	protected $module;
	protected $path;
	
	public static function run(){}
	
	public static function setTemplateData($data = null){
		if(is_string($data)) {
			
			if(array_key_exists($data, self::$_instances)){
				return self::$_instances[$data];
			} else {
				$instance = new self($data);
				
				self::$_instances[$data] = $instance;
				
				return $instance;
			}
		}
		
		return false;
	}
	
	protected function __construct($data){
		$mp = explode("::", $data);
		$mod = $mp[0];
	
		$mod = str_replace(" ", "", $mod);
		if(!ctype_alpha($mod)) throw new Exception('Only alphabetic characters allowed!');
		
		$disp = $mod;
		if(isset($mp[2])){
			$disp = htmlentities(urldecode($mp[2]));
		}
		
		if(count($mp) > 1){
			$icon = $mp[1];
		} else {
			$icon = 'icon-puzzle';
		}
		
		
		$this->icon = $icon;
		$this->modid = ucfirst(strtolower($mod));
		$this->module = $mod;
		$this->display_name = $disp;
		
		$this->path = APPLICATION_PATH.'/../library/'.APPNAMESPACE;
	}
	
	public function __get($name){
		if (property_exists($this, $name)) return $this->$name;
		
		return false;
	}
	
	private function _registerResourceWithConstantsClass($modid){
		$path = $this->path.'/Acl/';
		$file = $path."Constants.php";
		
		$modid = strtoupper($modid);
		
		$data = file_get_contents($file);
		if(!strstr($data,$modid)){
			$data = str_replace("//##INSERT_POINT", "const RESOURCE_".strtoupper($modid)." = \"".$this->modid."\";
	//##INSERT_POINT					
", $data);
			
			$fh = fopen($file, "w");
			if($fh){
				fwrite($fh, $data);
			}
			fclose($fh);
		}
		
		return true;
	}
	
	private function _registerFrontendApp(){	
		$path = APPLICATION_PATH."/../public/app/".APPNAMESPACE."/";
		$js = APPNAMESPACE.".js";
		
		$data = @file_get_contents($path.$js);
		if($data){
			$data = str_replace("//##INSERT_POINT", "$(document).off(\"click\",\"#".$this->modid."\");
$(document).on(\"click\",\"#".$this->modid."\",function(){Moj.u.ls(\"".APPNAMESPACE.".".$this->modid.".js\",true);});
//##INSERT_POINT
", $data);
	
			$fh = fopen($path.$js, "w");
			if($fh){
				fwrite($fh, $data);
			}
			fclose($fh);
		}
	
		return true;
	}
	
	public function createResourceClass(){
		$aclPath = $this->path.'/Acl/Resource/';
		$mod = $this->module;
		$modid = $this->modid;
		$disp = $this->display_name;
		$icon = $this->icon;
		
		$this->_registerResourceWithConstantsClass($modid);
		
		if(!file_exists($aclPath."$mod.php")){
			$fh = fopen($aclPath."$mod.php", "w");
			fwrite($fh, "<?php
class ".APPNAMESPACE."_Acl_Resource_".$mod." extends ".APPNAMESPACE."_Acl_Resource
{
	protected \$_resourceId = \"$modid\";
		
	public function __construct(){
		\$this->_config[self::DISPLAY_NAME] = '$disp';
		\$this->_config[self::ICON] = '$icon';
	}
}");
			fclose($fh);
			
			return true;
		}
		
		return false;
	}
	
	public function createControllerClass(){
		$ctrlPath = $this->path.'/_controllers/';
		$mod = $this->module;
		$modid = $this->modid;
		
		if(!file_exists($ctrlPath.$mod."Controller.php")){
			$fh = fopen($ctrlPath.$mod."Controller.php", "w");
			fwrite($fh, "<?php
class ".APPNAMESPACE."_".$mod."Controller extends Zend_Controller_Action
{
	protected \$_responseHandler;
		
	public function init(){
		\$this->_responseHandler = new Jax_Response_Handler();
		\$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
		
		\$this->_helper->layout()->disableLayout();
	
		if(!Jax_Auth::verify()){
			\$this->_helper->redirector(\"logout\",\"Auth\",\"Jax\");
		}
	
		Jax_Utilities_ControllerAccessChecker::run(".APPNAMESPACE."_Acl_Constants::RESOURCE_".strtoupper($modid).",Jax_Acl_Constants::ACCESS_READ);
	}
				
	public function indexAction() {
				
	}
}");
			fclose($fh);
			
			
				
			return true;
		}
		
		return false;
	}
	
	public function createDefaultView(){
		$mod = strtolower($this->module);
		$viewPath = $this->path.'/_views/'.$mod;
		
		if(!is_dir($viewPath)){
			$dir = mkdir($viewPath);
			
			if($dir){
				$fh = fopen($viewPath."/index.phtml", "w");
				fwrite($fh, "<div id=\"".strtolower(APPNAMESPACE)."-".strtolower($this->modid)."-main\"></div>");
				fclose($fh);
			}
		}
		
		return true;
	}
	
	public function createFrontendTemplate(){
		$path = APPLICATION_PATH."/../public/app/".APPNAMESPACE."/";
		$appDir = $path.$this->modid;
		
		if(!is_dir($appDir)){
			$dir = mkdir($appDir);
				
			if($dir){
				$fh = fopen($appDir."/".APPNAMESPACE.".".$this->modid.".js", "w");
				fwrite($fh, "$(function(){
	Moj.u.lc(\"".APPNAMESPACE.".".$this->modid.".css\",true);
			
	Moj.extend(\"".APPNAMESPACE.".".$this->modid."\",{
		run: function(){
			var f = function(){
				Moj.jp.lv('".APPNAMESPACE."','".$this->modid."','index',function(d){
					$(Moj.n.t()).html(d);
						
				});
				
				Moj.Arn.Resources.d('".$this->display_name."');
			};
			f();
			
			Moj.n.a('".$this->modid."',f);
			Moj.n.h('".$this->modid."');
		}
	});
			
	Moj.".APPNAMESPACE.".".$this->modid.".run();
	Moj.u.lslist['".APPNAMESPACE.".".$this->modid.".js'] = Moj.".APPNAMESPACE.".".$this->modid.".run;
			
});
");
				fclose($fh);
				
				// CSS FILE
				
				$fh = fopen($appDir."/".APPNAMESPACE.".".$this->modid.".css", "w");
				fwrite($fh, "#".strtolower(APPNAMESPACE)."-".strtolower($this->modid)."-main {
	padding:10px;
}");
				fclose($fh);
				
				$this->_registerFrontendApp();
			}
		}
		
		return true;
	}

}