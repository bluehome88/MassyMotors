<?php
class Hilo_JobappController extends Zend_Controller_Action
{
	protected $_responseHandler;
		
	public function init(){
		$this->_responseHandler = new Jax_Response_Handler();
		$this->_responseHandler->setResponseType(Jax_Response_Handler::JSON);
		
		$this->_helper->layout()->disableLayout();
	
		if(!Jax_Auth::verify()){
			$this->_helper->redirector("logout","Auth","Jax");
		}
	
		Jax_Utilities_ControllerAccessChecker::run(Hilo_Acl_Constants::RESOURCE_JOBAPP,Jax_Acl_Constants::ACCESS_READ);
	}
				
	public function listAction(){
		$tbl = new Hilo_Models_WebHrApp();
		$cols = $tbl->info(Zend_Db_Table::COLS);
		$exc = array('id');
		$fcols = array_diff($cols, $exc);
				
		$this->view->fcols = $fcols;
		
		$this->view->locations = Jax_Data_Source::getInstance()->getRecord("WebLocations",null,true);
		
		$appdb = new Hilo_Models_WebHrApp();
		
		$this->view->positions = $appdb->getAdapter()->query("SELECT DISTINCT(`position`) FROM ".$appdb->info('name'))->fetchAll();
	}
	
	public function printAction(){
		$data = Jax_Acl::getInstance()->acl()->get("Jobapp")->exportPrint($this->getRequest());
		$this->view->data = $data;
		
		$this->view->params = $this->getRequest()->getParams();
	}
	
	public function cvAction(){
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$params = $this->getRequest()->getParams();
		
		if(!isset($params['cid'])){
			die('Invalid request. CV not specified.');
		} else {
			$cvid = intval($params['cid']);
			
			$entry = Jax_Data_Source::getInstance()->getRecord("WebHrApp","`id`=$cvid");
			
			if($entry){
			
				$filename = $entry['cv'];
				
				if (!empty($filename)) {
					$data = file_get_contents(APPLICATION_PATH."/../library/Hilo/_upload/".$filename, "r");
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename='.$filename);
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					
					echo $data;
				}
			} else {
				echo "Invalid CV request.";
			}
		}
	}
}