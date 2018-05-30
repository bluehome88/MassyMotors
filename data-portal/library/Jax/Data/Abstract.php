<?php
/**
 * Abstract class that provides methods to allow concrete *_Data_Source classes to 
 * set and manage multiple data sources (Concrete instances of Jax_Data_Source_Abstract)
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
abstract class Jax_Data_Abstract
{
	/**
	 * Flag indicating if the class is restored from session or is a new instance.
	 * 
	 * @var boolean
	 */
	protected $_isRestored = false;
	
	/**
	 * Array for holding concrete Jax_Data_Source_Abstract class instances
	 * 
	 * @var array
	 */
	protected $_dataSourceObjects = array();
	
	/**
	 * Developers must define a *_Data_Source class and extend this (Jax_Data_Abstract) class to use data sources.
	 * 
	 * Sample class for an app that would use SOAP (web services) as its data source:
	 * 
	 * class MyApp_Data_Source extends Jax_Data_Abstract {
	 * 		public function __construct(){
	 * 			$this->addDataSourceObject(new Jax_Data_Source_Soap(),Jax_Data_Source_Types::SOAP);
	 * 		}
	 * }
	 */
	abstract public function __construct();
	
	/**
	 * Proxy to addDataSourceObject
	 * 
	 * @param Jax_Data_Source_Abstract $object
	 * @param string $dataSourceType
	 * @param mixed $index
	 * @deprecated - Use addDataSourceObject instead.
	 */
	public function setDataSourceObject(Jax_Data_Source_Abstract $object, $dataSourceType = null, $index = null){
		return $this->addDataSourceObject($object,$dataSourceType,$index);
	}
	
	/**
	 * Adds a concrete instance of Jax_Data_Source_Abstract to the list of available data sources for the application.
	 * 
	 * @param Jax_Data_Source_Abstract $object
	 * @param string $dataSourceType (See Jax_Data_Source_Types for a list of candidate types. Custom types accepted)
	 * @param mixed (String | Int) $index - (optional) The location where the supplied object should be placed.
	 * @throws Exception - if dataSourceType is not supplied
	 */
	public function addDataSourceObject(Jax_Data_Source_Abstract $object, $dataSourceType = null, $index = null, $replace = false){
		
		$useIndex = false;
		
		if (!is_null($dataSourceType)){
			if(!@is_array($this->_dataSourceObjects[$dataSourceType])){
				$this->_dataSourceObjects[$dataSourceType] = array();
			}
			
			if($replace === true){
				$useIndex = true;
			} else {
				// Verify location before adding (no overwrites)
				if (!is_null($index)){
					if(array_key_exists($index, $this->_dataSourceObjects[$dataSourceType])){
						throw new Exception("DSO Index not empty!", "-2");
					} else {
						$useIndex = true;
					}
				}
			}
			
			if($useIndex){
				@$this->_dataSourceObjects[$dataSourceType][$index] = $object;
			} else {
				@$this->_dataSourceObjects[$dataSourceType][] = $object;
			}
		} else {
			throw new Exception("Data source type not set!", "-1");
		}
		
		return $this;
	}
	
	/**
	 * Retrieves a data source class instance specified by a dataSourceType and an index.
	 * 
	 * @param string $dataSourceType
	 * @param mixed (string | int) $index
	 * @throws Exception - If dataSourceType is not set.
	 * @return Jax_Data_Source_Abstract
	 */
	public function getDataSourceObject($dataSourceType = null, $index = null){
		if (!is_null($dataSourceType)){
			
			// Check index location for object
			if (!is_null($index)){
				if(array_key_exists($index, $this->_dataSourceObjects[$dataSourceType])){
					return $this->_dataSourceObjects[$dataSourceType][$index];
				} else {
					return null;
				}
			} 
			
			// Return first instance of Jax_Data_Source_Abstract found
			else {
				foreach ($this->_dataSourceObjects[$dataSourceType] as $index=>$object){
					if($object instanceof Jax_Data_Source_Abstract)
						return $object;
				}
				return null;
			}
		} else {
			throw new Exception("Data source type not set!", "-2");
		}
	}
	
	/**
	 * Return the first data source object that was set in *_Data_Source.
	 * 
	 * @return Jax_Data_Source_Abstract
	 */
	public function getDefaultDataSourceObject(){
		foreach ($this->_dataSourceObjects as $type=>$list){
			foreach ($list as $index=>$object) {
				if ($object instanceof Jax_Data_Source_Abstract)
					return $object;
			}
		}
	}
	
	/**
	 * Remove a data source object from the list of data sources.
	 * 
	 * @param string $dataSourceType
	 * @param mixed (string | int) $index
	 * @throws Exception - If dataSourceType is not set.
	 */
	public function removeDataSourceObject($dataSourceType = null, $index = null){
		if (!is_null($dataSourceType)){
			
			// Remove a specific object of the specified type
			if (!is_null($index)){
				if(array_key_exists($index, $this->_dataSourceObjects[$dataSourceType]))
					unset($this->_dataSourceObjects[$dataSourceType][$index]);
			} 
			// Remove all objects of the specified type
			else {
				if(array_key_exists($dataSourceType, $this->_dataSourceObjects))
					unset($this->_dataSourceObjects[$dataSourceType]);
			}
		} else {
			throw new Exception("Data source type not set!", "-3");
		}
		
		return $this;
	}
	
	/**
	 * Retrieves the list of data source objects.
	 * 
	 * @return array 
	 */
	public function getDataSourceObjects(){
		return $this->_dataSourceObjects;
	}
	
	/**
	 * Sets the class indicator as being restored from session
	 */
	public function setAsRestored(){
		$this->_isRestored = true;
		return $this;
	}
}