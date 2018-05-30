<?php
abstract class Hilo_Acl_Resource extends Jax_Acl_Resource
{
	protected function oneProc(Zend_Controller_Request_Abstract $request){
		$params = $request->getParams();
		switch (@$params['access']){
			case Jax_Acl_Constants::ACCESS_READ:
				return $this->_read($params,$request);
				break;
				
			case Jax_Acl_Constants::ACCESS_WRITE:
				return $this->_write($params,$request);
				break;
				
			case Jax_Acl_Constants::ACCESS_APPEND:
				return $this->_append($params,$request);
				break;
				
			case Jax_Acl_Constants::ACCESS_UPDATE:
				return $this->_update($params,$request);
				break;
				
			case Jax_Acl_Constants::ACCESS_DELETE:
				return $this->_delete($params,$request);
				break;
				
			default;
				return $this->_default($params,$request);
				break;
		}
	}

	protected function _read($params,$request){}
        
    protected function _write($params,$request){}
        
    protected function _append($params,$request){}
        
    protected function _update($params,$request){}
        
    protected function _delete($params,$request){}
        
    protected function _default($params,$request){}
}