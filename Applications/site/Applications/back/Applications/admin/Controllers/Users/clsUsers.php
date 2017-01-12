<?php
/**
 * @name	c_site_back_admin_UsersException
 * @author 	wiegerjelsma
 * @version	1.0 2012-07-17 16:42:27
 */
class c_site_back_admin_UsersException extends c_site_back_admin_ControllerException {}

/**
 * @name	c_site_back_admin_Users
 * @author 	wiegerjelsma
 * @version	1.0 2012-07-17 16:42:27
 */
class c_site_back_admin_Users extends c_site_back_admin_Controller {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-07-17 16:42:27
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	/*
	 * @name	delete
	 */
	public function delete(){
		if(ID_NAME == $this->SESSION->read('loggedin_userid')){	
			$this->MESSAGES->push("Het is niet mogelijk om uzelf als user te verwijderen.", 'error');
			$obj_Url = Loader::load('Url');					
			$obj_Url->Redirect(CONTROLLER_NAME);
		} else
			parent::delete();
	}	
	
	
	/**
	 * @name	validate
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2011-11-24 18:33:18
 	 * @desc	
 	 */	 
	protected function validate(){
		$basicValidation = parent::validate();
		if(!$basicValidation)
			return false;	
			
		$field = 'userlevel';	
			
		if(!isset($this->a_Post[$field]))
			return true;	
						
		$a_ErrorFields = array();	
				
		// validate het emailadres (we mogen niet twee users per emailadres hebben)
		
		
		
		
 		if(count($a_ErrorFields)>=1){
			$this->TPL->assign('ErrorFields', $a_ErrorFields);		
 			return false;
 		} else
 			return true; 		
			
	}	
}
