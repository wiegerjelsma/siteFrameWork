<?php
/**
 * @name	c_ControllerException
 * @author 	wiegerjelsma
 */
class c_TemplateException extends c_ControllerException {}

/**
 * @name	c_Template
 * @author 	wiegerjelsma
 */
class c_Template extends c_Controller {
    
	public function init(){
		parent::init();
	}
	
	public function view(){
		if(defined('CONTROLLER_NAME'))$this->TPL->assign('GA_ControllerName', CONTROLLER_NAME);
		if(defined('FUNCTION_NAME'))$this->TPL->assign('GA_FunctionName', FUNCTION_NAME);
		$this->TPL->display(ID_NAME.'.tpl');		
	}
}