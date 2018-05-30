<?php
/**
 * Default Controller class.
 * Renders the initial layout.
 * 
 * URL: /
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_IndexController extends Zend_Controller_Action
{
	public function init(){
		if (APPNAMESPACE != 'Jax') {
			$lp = Jax_System_Constants::getApplicationLayoutPath();
			if(file_exists($lp."/layout.phtml"))
				$this->_helper->layout()
					->setLayout("layout",true)
					->setLayoutPath($lp);
		}
	}
	
    // Render initial client side JS interface
    public function indexAction()
    {
    	
    }
}