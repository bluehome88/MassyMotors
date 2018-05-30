<?php
class Jax_FilemanagerController extends Zend_Controller_Action
{
	protected $_responseHandler;
	
	public function init()
    {
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
   		if(!Jax_Auth::verify()){
			$this->_helper->redirector("index","index","Jax");
		}
		
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
    }
    
    public function indexAction ()
    {
    	$params = $this->getRequest()->getParams();
    	
    	$uploader = new Jax_File_Uploader();
    	
    	if(isset($params['processor'])) $uploader->setFormProcessor($params['processor']);
    	
    	foreach ($params as $h=>$v){
    		$p = explode("_",$h);
    		
    		$pname = ucfirst(strtolower($p[0]));
    		$cName = "Zend_Form_Element_".$pname;
    		if(@class_exists($cName)){
    			$element = new $cName(array('name'=>$p[1],'value'=>$v));
    			$uploader->addElement($element);
    		}
    	}
    	
    	echo $uploader->renderForm();
    	if(isset($params['iframe'])) echo "<iframe name=\"".$uploader->target."\"></iframe>";
    }
    
    public function uploadAction(){
    	if ($this->getRequest()->isPost()){
    		$params = $this->_getAllParams();
    		$uploader = new Jax_File_Uploader();
    		if($uploader->process($params)){
    			$file_name = $uploader->getUploadedFileName();
    			
    			if(isset($params['callback'])) die("<script>eval(\"".$params['callback']."\");</script>");
    			
    			$this->_responseHandler
    				->setResponseData(Jax_Response::Valid(array('file'=>$file_name)))
    				->send();
    				
    				return;
    		} else {
    			$this->_responseHandler
    				->setResponseData(Jax_Response::Error("Unable to upload file."))
    				->send();
    				
    				return;
    		}
    	} else {
    		$this->_responseHandler
    				->setResponseData(Jax_Response::Error("Invalid Request"))
    				->send();
    				
    				return;
    	}
    }
}
