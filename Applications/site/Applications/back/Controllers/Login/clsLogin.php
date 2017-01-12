<?php
/**
 * @name	c_site_back_LoginException
 * @author 	wiegerjelsma
 * @version	1.0 2012-02-22 22:50:59
 */
class c_site_back_LoginException extends c_site_back_ControllerException {}

/**
 * @name	c_site_back_Login
 * @author 	wiegerjelsma
 * @version	1.0 2012-02-22 22:50:59
 */
class c_site_back_Login extends c_site_back_Controller {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-02-22 22:50:59
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	
	/**
	 * @name	checkAccess
 	 * @desc	Op deze functie en module hebben we altijd access
 	 *			Hier dus niet kijken of we ingelogd zijn, dat zijn we niet namelijk..
 	 */	 
	protected function checkAccess(){
		return true;
	}
	
	
	
	/**
	 * @name	view
 	 * @desc	HIer het login scherm tonen
 	 */	 
	public function view(){
		if(APPLICATION_NAME != FWPREFIX.'.back'){
			$obj_Url = Loader::load('Url');					
			$obj_Url->RedirectToApplication(FWPREFIX.'.back');		
		}
			
		$this->readPost();
		
 		if(isset($this->a_Post['submitted']) && $this->a_Post['submitted']){
 			unset($this->a_Post['submitted']);
 			
 			if($this->validate()){
 				if($this->login()){
 					$application = $this->SESSION->read('loggedin_userlevel') == 2 ? FWPREFIX.'.back.admin' : FWPREFIX.'.back.user'; 				
 					$obj_Url = Loader::load('Url');					
					$obj_Url->RedirectToApplication($application);
 				} else {
 					$this->MESSAGES->push('Ongeldige gebruikersnaam of wachtwoord', 'error', 'form');
 					$this->form($this->a_Post);
 				} 			
 			} else
 				$this->form($this->a_Post);			 			
 		} else
 	 		$this->form();		
 	}

 	
	/**
	 * @name	logout
 	 * @desc	HIer het login scherm tonen
 	 */	 
 	public function logout(){
		$obj_History = Loader::LoadModule('History');
		$obj_History->trackEvent(CONTROLLER_NAME, 'logout', 'user_id', $this->SESSION->read('loggedin_userid'));			

		$this->SESSION->reset();								
					
		$this->MESSAGES->push('U bent succesvol afgemeld.', 'success');
		
		$obj_Url = Loader::load('Url');					
		$obj_Url->Redirect('login');
 	}
	
	
	/**
	 * @name	form
	 * @desc	Toont het login formulier
	 */
	protected function form($dataset = array()){
		$a_Buttons[] = array('type' => 'submit', 'value' => 'login');
		
		$a_Form['action'] = APPLICATION_URL.'/'.CONTROLLER_NAME.'/view';
		$a_Form['id'] = 'loginform';
		$a_Form['name'] = 'loginform';
		
		$this->TPL->assign('Form', $a_Form);		
		$this->TPL->assign('DataSet', $dataset);		
		$this->TPL->assign('Buttons', $a_Buttons); 
		$this->TPL->display('login.tpl');
	}	
	
	
	/**
	 * @name	login
	 * @desc	Feitelijk login actie
	 */
	private function login(){		
		$obj_Users = Loader::loadModule('Users');
		$a_Users = $obj_Users->get(array(
			'gebruikersnaam' => $this->a_Post['gebruikersnaam'], 
			'wachtwoord' => md5($this->a_Post['wachtwoord']),
			'status' => true
		));
		if($a_Users){		
			$this->SESSION->write('loggedin', true);
			$this->SESSION->write('loggedin_user', $a_Users[0]);
			
			$this->SESSION->write('loggedin_userid', $a_Users[0]['id']);
			
			$obj_History = Loader::LoadModule('History');
			$obj_History->trackEvent(CONTROLLER_NAME, 'login', 'user_id', $a_Users[0]['id']);			
			
			return true;
		}				
		return false;
	}	
}
