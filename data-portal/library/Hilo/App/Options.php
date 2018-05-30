<?php
class Hilo_App_Options extends Jax_App_Options {
	protected function initCustom(){
		if (!Jax_Auth::verify()) return;

		$options = Jax_Data_Source::getInstance()->getRecord("SysOptions",null,true);
		if (is_array($options)){
			$cfg = array();
			foreach ($options as $entry){
				$cfg[$entry['option']] = $entry['value'];
			}
			$this->_options = array_merge($this->_options,$cfg);
		} else {
			throw new Exception('Unable to retrieve system options. ('.__CLASS__.' '.__LINE__.')');
		}
	}
}