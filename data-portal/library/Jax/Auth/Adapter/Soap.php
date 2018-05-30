<?php
/**
 * Custom SOAP (web services) Authentication Adapter for Zend Framework written for Jax.
 * 
 * If using this adapter for authentication, an instance of Jax_Data_Source_Soap is automatically created and stored
 * within the applications *_Data_Source class.
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
abstract class Jax_Auth_Adapter_Soap extends Jax_Auth_Adapter_Abstract
{
	/**
	 * WSDL location of the webservice.
	 * 
	 * @var string
	 */
	protected $_wsdl = null;
	
	/**
	 * Name of the authentication service (defined in the wsdl).
	 * 
	 * @var string
	 */
	protected $_authService = null;
	
	/**
	 * An ordered array of parameters that will be passed to the service for authentication.
	 * @var array
	 */
	protected $_serviceParameters = array();
	
	/**
	 * Service response from the authentication request.
	 * 
	 * @var mixed - Depends on the service
	 */
	protected $_authResponse = null;
	
	/**
	 * Regex pattern to match the service response for a valid authentication attempt.
	 * 
	 * @var string - Regular expression
	 */
	protected $_validAuthResponse = null;
	
	/**
	 * Default constructor. 
	 * 
	 * @param string $wsdl - (optional)
	 * @param string $service - (optional)
	 * @param array $parameters - (optional)
	 */
	public function __construct($wsdl = null,$service = null, array $parameters = array()){
		$this->_wsdl = $wsdl;
		$this->_authService = $service;
		$this->_serviceParameters = $parameters;
	}
	
	/**
	 * Setter - Sets the _wsdl variable
	 * 
	 * @param string $wsdl
	 */
	public function setWsdl($wsdl = null){ $this->_wsdl = $wsdl; }
	
	/**
	 * Setter - Sets the _authService variable
	 * 
	 * @param string $serviceName
	 */
	public function setAuthServiceName($serviceName = null){ $this->_authService = $serviceName; }
	
	/**
	 * Setter - Sets the _serviceParameters variable
	 * 
	 * @param array $serviceParams
	 */
	public function setAuthServiceParams(Array $serviceParams){ $this->_serviceParameters = $serviceParams; }
	
	/**
	 * Setter - Sets the regular expression used to match against a valid service response
	 * 
	 * @param string $regex - Regular expression
	 */
	public function setValidAuthResponse($regex = null){ $this->_validAuthResponse = $regex; }
	
	/**
	 * Process the authentication request
	 * @see Zend_Auth_Adapter_Interface::authenticate()
	 */
	public function authenticate(){		
		try {
			// Authenticate
			$this->_configureServiceParameters();
			
			// Push wsdl and cache flag to args array
			array_unshift($this->_serviceParameters, $this->_wsdl,false);
			
			// Create and set an instance of Jax_Data_Source_Soap
			Jax_Data_Source::getInstance()
				->getDataSourceWrapper()
				->addDataSourceObject(new Jax_Data_Source_Soap(),Jax_Data_Source_Types::SOAP,__CLASS__);
			
			// Add data source target indicator (to use the new data source class defined above)
			$this->_serviceParameters[] = array(Jax_Data_Source_Types::SOAP,__CLASS__);

			// Call the specified auth service (attempt authentication)
			$authResult = call_user_func_array(array(Jax_Data_Source::getInstance(),$this->_authService), $this->_serviceParameters);
			
			if(preg_match($this->_validAuthResponse, $authResult) == 0){
				return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $this->_credentials[Jax_Auth_Constants::FIELD_USERNAME],array($authResult));
			} else {
				if(!$this->success()){
					Jax_Auth::logout();
					return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $this->_credentials[Jax_Auth_Constants::FIELD_USERNAME],array('User defined failure.'));
				}
			}

			$this->_authResponse = $authResult;
			
			if ($authResult){
				// Set required session values
				$this->_setAuthenticatedSession();
				
				return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,$this->_credentials[Jax_Auth_Constants::FIELD_USERNAME]);
			} else {
				return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $this->_credentials[Jax_Auth_Constants::FIELD_USERNAME],array("Invalid Credentials"));
			}
		} catch (Exception $e){
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $this->_credentials[Jax_Auth_Constants::FIELD_USERNAME],array($e->getMessage()));
		}
	}

	/**
	 * This method must be implemented in concrete classes to allow setting of wsdl,service name, service parameters and regex pattern using the defined setter methods.
	 */
	abstract protected function _configureServiceParameters();
}