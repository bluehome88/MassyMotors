<?php
/**
 * All access to data resources is done via this singleton class.
 * 
 * At runtime, application specific *_Data_Source classes are stored within this class. These "wrapper" classes
 * themselves contain instances of concrete Jax_Data_Source_Abstract classes, which are the actual classes used
 * to access data (databases, webservices, etc).
 * 
 * See Jax_Data_Abstract for documentation on how to create your *_Data_Source class for managing your concrete
 * data source classes. Once an application contains a *_Data_Source class it will be automatically used by this
 * class to provide data access services.
 * 
 * Only this class should be used to access data. Example usage is as follows:
 * 
 * In your code, 
 * 
 * 	$data = Jax_Data_Source::getInstance()->METHOD([arg1,arg2,...,array(TYPE,INDEX)]);
 * 
 * METHOD - a method in one of the concrete classes of Jax_Data_Source_Abstract
 * 
 * Note: The last argument can optionally be an array as shown.
 * This is used to identify which concrete data source class should be queried for data. (See Jax_Data_Abstract for details)
 * If this last array argument is ommitted, the default data source class is used, which is the first class you specify
 * in your *_Data_Source class constructor.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_Data_Source
{	
	/**
	 * Singleton instance
	 * 
	 * @var Jax_Data_Source
	 */
	protected static $_instance;
	
	/**
	 * Default concrete Jax_Data_Source_Abstract object, extracted from *_Data_Source class.
	 * 
	 * @var Jax_Data_Source_Abstract
	 */
	protected $_defaultDataSource;
	
	/**
	 * The *_Data_Source class for the application.
	 * This class (if defined) is automatically added at runtime.
	 * 
	 * @var Jax_Data_Abstract
	 */
	protected $_dataSourceWrapper;
	
	/**
	 * Singleton pattern
	 */
	public static function getInstance()
	{
		if(!self::$_instance)
		{
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}

	/**
	 * Protected constructor (Singleton Pattern)
	 */
	protected function __construct(){}
	
	/**
	 * Used to set the *_Data_Source class at runtime. (See application/Bootstrap.php)
	 * 
	 * @param Jax_Data_Abstract $dataWrapperClass
	 * @return Jax_Data_Source
	 */
	public function setDataSourceWrapper(Jax_Data_Abstract $dataWrapperClass)
	{
		$this->_dataSourceWrapper = $dataWrapperClass;
		$this->_setDefaultDataSourceObject();		
		return $this;
	}
	
	/**
	 * Proxy to the *_Data_Source wrapper class
	 * 
	 * @return Jax_Data_Abstract
	 */
	public function getDataSourceWrapper()
	{
		return $this->_dataSourceWrapper;
	}
	
	/**
	 * Returns the list (array) of concrete Jax_Data_Source_Abstract classes defined in the *_Data_Source class.
	 * 
	 * @return array
	 */
	public function getDataSourceObjects()
	{
		return $this->_dataSourceWrapper->getDataSourceObjects();
	}
	
	/**
	 * Gets and sets the default data source object. 
	 * This is the first concrete Jax_Data_Source_Abstract class you define in the *_Data_Source class.
	 * 
	 * @return Jax_Data_Source
	 */
	protected function _setDefaultDataSourceObject()
	{
		$this->_defaultDataSource = $this->_dataSourceWrapper->getDefaultDataSourceObject();
		return $this;
	}
	
	/**
	 * Magic method used to invoke the requested method on the specific concrete Jax_Data_Source_Abstract class.
	 * 
	 * @param string $method - Name of the method
	 * @param array $args - array of arguments passed
	 * @throws Exception - if a data source class is not found.
	 */
	public function __call($method,$args)
	{
		$targetDSO = $this->_defaultDataSource;
		
		// Checks if last parameter passed is an array indicator for specifying the target class.
		$target = end($args);
		if(is_array($target) && count($target) == 2){
			$type = @$target[0];
			$index = @$target[1];
			
			$DSO = $this->_dataSourceWrapper->getDataSourceObject($type,$index);
			if ($DSO instanceof Jax_Data_Source_Abstract){
				
				// Remove the last parameter that was passed.
				array_pop($args);
				$targetDSO = $DSO;
			}
		}
		
		if (!($targetDSO instanceof Jax_Data_Source_Abstract)) throw new Exception('Data source not set!');
		
		// Invoke the requested method on target data source class.
		return @call_user_func_array(array($targetDSO,$method), $args);
	}
}