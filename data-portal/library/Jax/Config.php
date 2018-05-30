<?php
/**
 * Default Configuation class.
 * It is not necessary to use this class. A default setup is loaded when the Jax installation is accessed initially to load the Jax JS client.
 * Thereafter, all configuration is automatically done by loading the appropriate application ini file.
 * 
 * If needed, change default settings passed by editing ../public/index.php
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Config
{
	/**
	 * Configuration options
	 * 
	 * @var array
	 */
	protected $_config = array();
	
	/**
	 * System constants. Do not edit.
	 * 
	 * @var string
	 */
	const APP_NS = 'app'; // Do not change
	const COMPANY = 'company';
	const APP_FULLNAME = 'fullname';
	const APP_SHORTNAME = 'shortname';
	const APP_PATH_IMAGE = 'imagePath';
	const APP_PATH_JS = 'jsPath';
	const APP_PATH_CSS = 'cssPath';
	const APP_LOGO = 'logo';
	
	const APP_CSS = 'css';
	const APP_JS = 'js';
	
	/**
	 * Default constructor. Accepts configuration options.
	 * 
	 * @param array $config
	 * @return Jax_Config
	 */
	public function __construct(Array $config = null)
	{
		if (!is_null($config)) {
			$this->_config = $config;
		}
		
		if (!isset($this->_config[self::APP_NS][self::APP_CSS])) $this->_config[self::APP_NS][self::APP_CSS] = array();
		if (!isset($this->_config[self::APP_NS][self::APP_JS])) $this->_config[self::APP_NS][self::APP_JS] = array();

	}
	
	/**
	 * Sets the default company name.
	 * 
	 * @param string $name
	 * @return Jax_Config
	 */
	public function setCompanyName($name = 'Nerds Consulting Limited')
	{
		$this->_config[self::APP_NS][self::COMPANY] = $name;
		return $this;
	}
	
	/**
	 * Sets the default application name
	 * 
	 * @param string $name
	 * @return Jax_Config
	 */
	public function setApplicationName($name = 'Jax App')
	{
		$this->_config[self::APP_NS][self::APP_FULLNAME] = $name;
		return $this;
	}
	
	/**
	 * Sets the default application short name.
	 * 
	 * @param string $name
	 * @return Jax_Config
	 */
	public function setApplicationShortName($name = 'Jax')
	{
		$this->_config[self::APP_NS][self::APP_SHORTNAME] = $name;
		return $this;
	}
	
	/**
	 * Sets the default image path
	 * 
	 * @param string $path
	 * @return Jax_Config
	 */
	public function setPathImage($path = './')
	{
		$this->_config[self::APP_NS][self::APP_PATH_IMAGE] = $path;
		return $this;
	}
	
	/**
	 * Sets the default JavaScript path
	 * 
	 * @param string $path
	 * @return Jax_Config
	 */
	public function setPathJs($path = './js')
	{
		$this->_config[self::APP_NS][self::APP_PATH_JS] = $path;
		return $this;
	}
	
	/**
	 * Sets the default CSS path
	 * 
	 * @param string $path
	 * @return Jax_Config
	 */
	public function setPathCss($path = './')
	{
		$this->_config[self::APP_NS][self::APP_PATH_CSS] = $path;
		return $this;
	}
	
	/**
	 * Sets the default logo image. (relative to image path)
	 * 
	 * @param string $image
	 * @return Jax_Config
	 */
	public function setLogoImage($image = 'logo.png')
	{
		$this->_config[self::APP_NS][self::APP_LOGO] = $image;
		return $this;
	}
	
	/**
	 * Adds a JavaScript file to load during the initial Jax client rendering.
	 * <script src="xxx"></script>
	 * 
	 * @param string $file
	 * @return Jax_Config
	 */
	public function addJsFile($file = 'javascript.js')
	{
		$this->_config[self::APP_NS][self::APP_JS][] = $file;
		return $this;
	}
	
	/**
	 * Adds a CSS file to load during the initial Jax client rendering
	 * 
	 * @param string $file
	 * @return Jax_Config
	 */
	public function addCssFile($file = 'style.css')
	{
		$this->_config[self::APP_NS][self::APP_CSS][] = $file;
		return $this;
	}
	
	/**
	 * Returns the configuration
	 * 
	 * @return array
	 */
	public function getConfig()
	{
		return $this->_config;
	}
	
	public static function getPublicPath(){
		$appIni = Zend_Registry::get(Jax_System_Constants::SYSTEM_REGKEY_APPCFG);
		if(isset($appIni['config']['publicPath'])){
			$publicDir = $appIni['config']['publicPath'];
		} else {
			$publicDir = 'public';
		}
		
		return $publicDir;
	}
}