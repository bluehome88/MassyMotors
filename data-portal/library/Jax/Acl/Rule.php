<?php
class Jax_Acl_Rule
{
	protected $role;
	protected $module;
	protected $access;
	protected $allow;
	
	public function __construct($rule){
		foreach ($rule as $r=>$v){
			if (property_exists($this, $r))
				$this->$r = $v;
		}
	}
	
	public function __get($p){
		if (isset($this->$p)) return $this->$p;
		return null;
	}
	
	public function __set($r,$v){
		if (property_exists($this, $r))
			$this->$r = $v;
	}
	
	public function __toString(){
		$rule = implode("::", array($this->role,$this->module,$this->access,$this->allow));
	}
}