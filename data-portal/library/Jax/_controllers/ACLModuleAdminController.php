<?php
class Jax_ACLModuleAdminController extends Zend_Controller_Action
{
	public function init(){
		if (!Jax_Auth::verify() || !Jax_Acl::getInstance()->acl()->isAllowed(Jax_Auth::getAuthId(),'Moduleadmin',Jax_Acl_Constants::ACCESS_READ)){
			$this->_helper->redirector("logout","Auth","Jax");
		}
		
		$this->_helper->layout()->disableLayout();
	}
	
    public function indexAction ()
    {
        $this->_helper->redirector("index","index","Jax");
    }
    
    public function modulesAction(){
    	$dbDetails = Jax_Data_Source::getInstance()->getModuleList();
    	for ($i=0;$i<count($dbDetails);$i++){
    		$a = $dbDetails[$i];
    		$dbDetails[$a['module']] = $a;
    		unset($dbDetails[$i]);
    	}
    	
    	$mods = Jax_Acl::getInstance()->acl()->getResources();
    	$modules = array();
    	foreach ($mods as $m){
    		$path = Jax_Acl::getInstance()->getResourceHierarchy($m,true);
    		if (count($path) == 1) {
    			$modules[$m]['parent'] = null;
    		} else {
    			$modules[$m]['parent'] = $path[count($path)-3];
    		}
    		$modules[$m]['dbDetails'] = $dbDetails[$m];
    	}
    	$this->view->modules = $modules;
    }
    
    public function addModuleAction(){
    	$mods = Jax_Acl::getInstance()->acl()->getResources();
    	$this->view->mods = $mods;
    }
}
