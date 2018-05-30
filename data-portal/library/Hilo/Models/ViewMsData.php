<?php
class Hilo_Models_ViewMsData extends Zend_Db_Table_Abstract {
	protected $_name = 'view_ms_data';
	protected $_primary = array('UUID','acct_no');
}
