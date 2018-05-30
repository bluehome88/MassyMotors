<?php
/**
 * Jax File Uploader
 * Provides functionality to upload files
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_File_Uploader
{
	protected $_fileUploadTarget;
	protected $_formAction;
	protected $_attribs = array();
	protected $_file;
	protected $_form;
	protected $_filename;
	protected $_adapter;
	
	const ATTRIB_METHOD = 'method';
	const ATTRIB_ID = 'id';
	const ATTRIB_TARGET = 'target';
	
	public function __construct(){
		$this->_fileUploadTarget = realpath(APPLICATION_PATH).'/../library/'.APPNAMESPACE.'/_upload/';
		$this->_formAction = BASEURL.'Jax/FileManager/upload';
		
		$this->_form = new Zend_Form();
		
		$this->_attribs[self::ATTRIB_ID] = 'Jax_File_Uploader_Form';
		$this->_attribs[self::ATTRIB_METHOD] = 'POST';
		$this->_attribs[self::ATTRIB_TARGET] = 'Jax_File_Uploader_Form_Target';//Jax_File_Uploader_Form_Target
	}
	
	public function setTargetDir($dir){
		$this->_fileUploadTarget = realpath((string) $dir);
		return $this;
	}
	
	public function setFormProcessor($path){
		$this->_formAction = (string) $path;
		return $this;
	}
	
	public function setFormAttrib($attrib,$value){
		if(array_key_exists($attrib, $this->_attribs)){
			$this->_attribs[$attrib] = str_replace(" ", "_", $value);
		} 
		return $this;
	}
	
	public function addElement(Zend_Form_Element $element){
		$this->_form->addElement($element);
		return $this;
	}
	
	public function renderForm(){
		$uploadForm = $this->_form;
    	
    	$uploadForm
    		->setAction($this->_formAction)
    		->setMethod($this->_attribs[self::ATTRIB_METHOD])
    		->setAttrib(self::ATTRIB_ID, $this->_attribs[self::ATTRIB_ID])
    		->setAttrib(self::ATTRIB_TARGET, $this->_attribs[self::ATTRIB_TARGET])
    		->setAttrib('enctype', 'multipart/form-data');
    		
    	$file = new Zend_Form_Element_File('JaxFileUpload_Filename');
    	
    	$file
    		->setLabel('Select a file to upload')
    		->addValidator('Count',false,1);
    		//->addValidator('Size',false,'2097152');
    	
    	$submit = new Zend_Form_Element_Submit(array('name'=>'Upload File'));
    		
    	$uploadForm->addElement($file)->addElement($submit);
    	
    	return $uploadForm->__toString();
	}
	
	public function process($params){
		$this->_file = null;
		if (!is_array($params)) {
			throw new Exception("File upload request parameters not received.");
		}
		
	    $adapter = new Zend_File_Transfer_Adapter_Http();
	    	 		
		$adapter->setDestination($this->_fileUploadTarget);
		
		$filename = @$_FILES['JaxFileUpload_Filename']['name'];
		$this->_filename = $filename;
		$filesize = $adapter->getFileSize('JaxFileUpload_Filename');
		
		$adapter
			//->addValidator('Size',false,'2097152')
			//->addValidator('ExcludeExtension',false,'exe,vbs')
			->addFilter('Rename',$this->_fileUploadTarget.$filename);
			
		// Delete old file if exists
		@unlink($this->_fileUploadTarget.$filename);
			
	    if (!$adapter->receive()) {
		    return false;
		} else {
			$this->_adapter = $adapter;
			$this->_file = $this->_fileUploadTarget.$filename;
			return true;
		}
	}
	
	public function getFileTransferAdapter(){
		return $this->_adapter;
	}
	
	public function getUploadedFileName(){
		return $this->_file;
	}
	
	public function getUploadedFileNameOnly(){
		return $this->_filename;
	}
	
	public function getUploadedFileBinary(){
		$fc = file_get_contents($this->_file);
		if($fc) return $fc;
		die("Unable to fetch file data.  (uploader)");
	}
	
	public function __get($param){
		if(array_key_exists($param, $this->_attribs)){
			return $this->_attribs[$param];
		}
		return null;
	}
}