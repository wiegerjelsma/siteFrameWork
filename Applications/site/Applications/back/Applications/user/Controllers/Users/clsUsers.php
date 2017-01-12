<?php
/**
 * @name	c_site_back_user_UsersException
 * @author 	wiegerjelsma
 * @version	1.0 2012-07-17 16:42:31
 */
class c_site_back_user_UsersException extends c_site_back_user_ControllerException {}

/**
 * @name	c_site_back_user_Users
 * @author 	wiegerjelsma
 * @version	1.0 2012-07-17 16:42:31
 */
class c_site_back_user_Users extends c_site_back_user_Controller {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-07-17 16:42:31
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	/**
	 * @name	view
 	 * @desc	
 	 */	 
 	public function view(){ 	
 		$obj_Url = Loader::load('Url');					
		$obj_Url->Redirect(CONTROLLER_NAME.'/edit/'.USER_ID);
 	}	
 	
 	
 	/**
	 * @name	edit
 	 * @desc	
 	 */	 
 	public function edit(){
 		if(ID_NAME != USER_ID){
 			$obj_Url = Loader::load('Url');					
			$obj_Url->Redirect(CONTROLLER_NAME.'/edit/'.USER_ID); 		
 		} else
	 		parent::edit();
 	}

 	
 	/**
 	 * @name	getFormButtons
 	 */
 	protected function getFormButtons(){
 		$a_Buttons[] = array('type' => 'submit', 'value' => 'opslaan');
 		return $a_Buttons; 		
 	} 	
}
