<?php
class Arn_AuthController extends Zend_Controller_Action {
	public function init(){
		$this->_helper->layout()->disableLayout();
	}
	
	public function indexAction() {
		
	}
	
	public function userAction(){
		$this->_authCheck();
		
		$u = Jax_User::getInstance()->getUserObject(true);
		$this->view->user = $u->Fullname;
		$ar = "";
		if(is_array($u->Role)){
			foreach ($u->Role as $r){
				$ar .= $r.", ";
			}
			$this->view->role = substr($ar,0,strlen($ar)-2);
		} else {
			$this->view->role = $u->Role;
		}
	}
	
	protected function _authCheck(){
		if(!Jax_Auth::verify()){
			$this->_helper->redirector("logout","Auth","Jax");
		}
	}
}
