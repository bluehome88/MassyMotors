<?php
error_reporting(E_ERROR);
/**
 * Used to setup a new application on the Jax instance.
 * 
 * Used to create apps and setup skeleton structure.
 * Removes apps by deleting files and folders.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_System_Admin
{
	/**
	 * Template ini files used for creating new applications.
	 * 
	 * @var string
	 * @deprecated
	 */
	private static $_template = 'Template.ini';
	
	/**
	 * Template *.main.js file for new projects 
	 * 
	 * @var string
	 * @deprecated
	 */
	private static $_jsTemplate = 'template.main.js';
	
	/**
	 * Path where template project files are stored.
	 * (Relative to APPLICATION_PATH)
	 */
	private static $_appTemplatePath = "/../library/_app_template/";
	
	/**
	 * Public app path
	 * (Relative to APPLICATION_PATH)
	 */
	private static $_publicPath = "/../public/app/";
	
	/**
	 * library path
	 * (Relative to APPLICATION_PATH)
	 */
	private static $_libraryPath = "/../library/";
	
	/**
	 * List of reserved namespaces that cannot be used to create applications.
	 * 
	 * @var array
	 */
	private static $_nsReserved = array(
		'jax','default','fb','index','home','zend','jquery'
	);
	
	/**
	 * Create a new project in Jax instance.
	 * 
	 * @param array $appCfg
	 * @return stdClass
	 */
	public static function new_project($appCfg){
		$return = new stdClass();
		$return->success = false;
		
		// check for reserved namespaces
		if(!self::checkNS($appCfg['##NS##'])) return self::error("Namespace is reserved or in use. Please try another.");
		
		try {
			$path = APPLICATION_PATH.self::$_appTemplatePath;			
			if(is_dir($path)){
				$dir = new RecursiveDirectoryIterator($path,FilesystemIterator::SKIP_DOTS);
				//$dir->next();
				while ($dir->valid() && $dir->current()->isDir()){
					$dirname = $dir->getFilename();
					
					switch ($dirname){
						case "app":
							if(!mkdir(APPLICATION_PATH.self::$_publicPath.$appCfg['##NS##'])) die("Unable to create new application pub directory.");
							self::_recursiveCreate($dir->getChildren(), APPLICATION_PATH.self::$_publicPath.$appCfg['##NS##']."/",$appCfg);
							break;
							
						case "library":
							if(!mkdir(APPLICATION_PATH.self::$_libraryPath.$appCfg['##NS##'])) die("Unable to create new application lib directory.");
							self::_recursiveCreate($dir->getChildren(), APPLICATION_PATH.self::$_libraryPath.$appCfg['##NS##']."/",$appCfg);
							break;
					}
					$dir->next();
				}
			} else {
				return $return->error = "Template files could not be found!";
			}
		} catch (Exception $e){
			return $return->error = $e->getMessage();
		}
		
		$return->success = true;
		
		return $return;
	}
	
	private static function _recursiveCreate(RecursiveDirectoryIterator $dir,$target,$appCfg){
		while ($dir->valid()){
			$fname = $dir->getFilename();
			
			if($dir->isFile()){
				$fp = $dir->getRealPath();
				
				$fc = file_get_contents($fp);
				
				foreach ($appCfg as $pc=>$val){
					$fc = str_replace($pc, $val, $fc);
				}
				
				$fname = str_replace("##NS##", $appCfg['##NS##'], $fname);
				$fname = str_replace(".jax", "", $fname);
				
				$fh = fopen($target.$fname,"w");
				fwrite($fh, $fc);
				@fclose($fh);
				
				print "Creating file $fname in $target\r\n";
			} 
			
			elseif($dir->isDir()) {
				
				mkdir($target.$fname);
				print "\r\nCreating dir $fname in $target\r\n";
				
				$targetB = $target.$fname."/";
				self::_recursiveCreate($dir->getChildren(), $targetB,$appCfg);
			}
			
			$dir->next();
		}
	}
	
	/**
	 * To be further developed.
	 * 
	 * @param string $e
	 * @return stdClass
	 */
	private static function error($e){
		$error = new stdClass();
		$error->error = $e;
		$error->success = false;
		return $error;
	}
	
	/**
	 * Creates an application by creating application folders and skeleton files.
	 * Application is created based on the configuration information received from the client.
	 * 
	 * @param array $cfg
	 * @deprecated GUI for this method is not longer supported. Use cli tools (APPLICATION_PATH/tools/cli) to create apps instead.
	 */
	public static function createApp(Array $cfg){
		$session = new Zend_Session_Namespace(APPNAMESPACE);

		$cfg['namespace'] = ereg_replace("[^A-Za-z0-9]", "", ucfirst($cfg['namespace']));
		
		// check for reserved namespaces
		if(!self::_checkReservedNS($cfg['namespace'])) return "Namespace is reserved... Please try another.";
		
		// check if ns folder already exists
		if(is_dir(APPLICATION_PATH."/../library/".$cfg['namespace']) && 
			is_dir(APPLICATION_PATH."/../public/_assets/js/".$cfg['namespace']) &&
			file_exists(APPLICATION_PATH."/../library/".$cfg['namespace']."/".$cfg['namespace'].".ini") &&
			file_exists(APPLICATION_PATH."/../public/_assets/js/".$cfg['namespace']."/".$cfg['namespace'].".main.js"))
			
			return "Namespace already exists.";
		
		// create config from template
		$file = file_get_contents(APPLICATION_PATH.'/configs/'.self::$_template);
		
		$file = str_replace("###APP_DISPLAY_NAME###", $cfg['display_name'], $file);
		$file = str_replace("###APP_NAMESPACE###", $cfg['namespace'], $file);
		$file = str_replace("###COMPANY_NAME###", $cfg['company_name'], $file);
		
		if(is_string($cfg['mysql']['host']) && strlen($cfg['mysql']['host']) > 0){
			if(is_null($cfg['mysql']['port']) || $cfg['mysql']['port'] == 0){
				$cfg['mysql']['port'] = 3306;
			}
			
			$mysql = "resources.multidb.".$cfg['namespace'].".adapter = \"pdo_mysql\"\n";
			$mysql .= "resources.multidb.".$cfg['namespace'].".host = \"".$cfg['mysql']['host']."\"\n";
			$mysql .= "resources.multidb.".$cfg['namespace'].".username = \"".$cfg['mysql']['username']."\"\n";
			$mysql .= "resources.multidb.".$cfg['namespace'].".password = \"".$cfg['mysql']['password']."\"\n";
			$mysql .= "resources.multidb.".$cfg['namespace'].".port = \"".$cfg['mysql']['port']."\"\n";
			$mysql .= "resources.multidb.".$cfg['namespace'].".dbname = \"".$cfg['mysql']['dbname']."\"\n";
			$mysql .= "resources.multidb.".$cfg['namespace'].".profiler = TRUE\n";
			$mysql .= "resources.multidb.".$cfg['namespace'].".default = TRUE\n";
			
			$file = str_replace("'###JAX_MYSQL_DB_CONFIG###", $mysql, $file);
		} else {
			$file = str_replace("'###JAX_MYSQL_DB_CONFIG###", "\n", $file);
		}
		
		if(is_string($cfg['ad']['host']) && strlen($cfg['ad']['host']) > 0){
			$ad = "ldap.".$cfg['namespace'].".host = \"".$cfg['ad']['host']."\"\n";
			$ad .= "ldap.".$cfg['namespace'].".useStartTls = TRUE\n";
			$ad .= "ldap.".$cfg['namespace'].".accountDomainName = \"".$cfg['ad']['accountDomainName']."\"\n";
			$ad .= "ldap.".$cfg['namespace'].".accountDomainNameShort = \"".$cfg['ad']['accountDomainNameShort']."\"\n";
			$ad .= "ldap.".$cfg['namespace'].".accountCanonicalForm = 3\n";
			$ad .= "ldap.".$cfg['namespace'].".baseDn = \"".$cfg['ad']['baseDn']."\"\n";
			$ad .= "ldap".$cfg['namespace'].".adminUsername = \"".$cfg['ad']['adminUsername']."\"\n";
			$ad .= "ldap".$cfg['namespace'].".adminPassword = \"".$cfg['ad']['adminPassword']."\"\n";
			
			$file = str_replace("'###JAX_ACTIVE_DIR_CONFIG###", $ad, $file);
		} else {
			$file = str_replace("'###JAX_ACTIVE_DIR_CONFIG###", "\n", $file);
		}

		try {
			// Make Dirs
			mkdir(APPLICATION_PATH."/../library/".$cfg['namespace']);
			mkdir(APPLICATION_PATH."/../library/".$cfg['namespace']."/_controllers");
			mkdir(APPLICATION_PATH."/../library/".$cfg['namespace']."/_layouts");
			mkdir(APPLICATION_PATH."/../library/".$cfg['namespace']."/_views");
			mkdir(APPLICATION_PATH."/../library/".$cfg['namespace']."/_dataCache");
			mkdir(APPLICATION_PATH."/../library/".$cfg['namespace']."/_upload");
			mkdir(APPLICATION_PATH."/../public/_assets/js/".$cfg['namespace']);
			mkdir(APPLICATION_PATH."/../public/_assets/js/".$cfg['namespace']."/images");
			
			// write files
		
			$fh = fopen(APPLICATION_PATH."/../library/".$cfg['namespace']."/".$cfg['namespace'].".ini","w");
			fwrite($fh, $file);
			fclose($fh);
			
			$fh2 = fopen(APPLICATION_PATH."/../public/_assets/js/".$cfg['namespace']."/".$cfg['namespace'].".main.js","w");
			fwrite($fh2, self::_loadJSTemplate($cfg));
			fclose($fh2);
			
			$fh3 = fopen(APPLICATION_PATH."/../library/".$cfg['namespace']."/Bootstrap.php","w");
			fwrite($fh3, "<?php class ".$cfg['namespace']."_Bootstrap extends Zend_Application_Module_Bootstrap {}");
			fclose($fh3);
			
		} catch (Exception $e){
			return $e->getMessage();
		}
		
		return true;
	}
	
	/**
	 * Removes an application. 
	 * 
	 * @param string $appNamespace
	 */
	public static function removeApp($appNamespace){
		if(!self::_checkReservedNS($appNamespace)) return "Private Namespace";
		
		$dirs = array(
			APPLICATION_PATH."\\..\\library\\".$appNamespace,
			APPLICATION_PATH."\\..\\public\\_assets\\js\\".$appNamespace
		);
		$d = 0;
		foreach ($dirs as $dir){
			if(is_dir($dir)){				
				$dirR = $dir;
				$files = new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator($dirR, RecursiveDirectoryIterator::SKIP_DOTS),
				    RecursiveIteratorIterator::CHILD_FIRST
				);
				
				foreach ($files as $fileinfo) {
				    $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
				    if(!$todo($fileinfo->getRealPath())){
				    	return "Unable to remove ".$fileinfo->getRealPath().". Please manually remove and try again.";
				    }
				}
			} else {
				if($d == 0) return "Application not found!";
			}
			$d++;
		}

		return true;
	}
	
	/**
	 * Proxy to private check function
	 * 
	 * @param string $ns
	 * @return boolean
	 */
	public static function checkNS($ns){
		if(is_dir(APPLICATION_PATH."/../library/".$ns) &&
				is_dir(APPLICATION_PATH."/../public/app/".$ns) &&
				file_exists(APPLICATION_PATH."/../library/".$ns."/".$ns.".ini") &&
				file_exists(APPLICATION_PATH."/../public/app/".$ns."/".$ns.".js"))
		
		return false;
		
		return self::_checkReservedNS($ns);
	}
	
	/**
	 * Checks if user supplied namespace is a reserved namespace.
	 * Returns false if namespace is reserved.
	 * @param String $ns
	 * @return boolean
	 */
	private static function _checkReservedNS($ns){
		if (in_array(strtolower($ns), self::$_nsReserved)) return false;
		return true;
	}
	
	private static function _loadJSTemplate($cfg){
		$file = file_get_contents(APPLICATION_PATH.'/configs/'.self::$_jsTemplate);
		$file = str_replace("###APPNAMESPACE###", $cfg['namespace'], $file);
		return $file;
	}
}