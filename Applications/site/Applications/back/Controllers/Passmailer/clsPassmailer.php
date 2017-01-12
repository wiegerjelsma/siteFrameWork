<?php
/**
 * @name	c_ka_beheer_PassmailerException
 * @author 	wiegerjelsma
 * @version	1.0 2011-12-14 15:20:53
 */
class c_site_back_PassmailerException extends c_site_back_ControllerException {}

/**
 * @name	c_ka_beheer_Passmailer
 * @author 	wiegerjelsma
 * @version	1.0 2011-12-14 15:20:53
 */
class c_site_back_Passmailer extends c_site_back_Controller {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2011-12-14 15:20:53
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
 	 * @desc	HIer gaan we het formulier tonen waar het emailadres ingevuld moet worden
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
 				if($this->mailPass()){
 					$this->MESSAGES->push('Wij hebben u een nieuw wachtwoord toegestuurd.', 'success','form');
 					$obj_Url = Loader::load('Url');					
					$obj_Url->RedirectToApplication(SITENAME.'.back');
 				} else {
 					$this->form($this->a_Post);
 				} 			
 			} else
 				$this->form($this->a_Post);			 			
 		} else
 	 		$this->form();
	}
	
	
	/**
	 * @name	form
	 * @desc	Toont het passmailer formulier
	 */
	protected function form($dataset = array()){
		$a_Buttons[] = array('type' => 'submit', 'value' => 'ok');
		$a_Buttons[] = array('url' => APPLICATION_URL.'/login', 'value' => 'annuleren');
		
		$a_Form['action'] = APPLICATION_URL.'/'.CONTROLLER_NAME.'/view';
		$a_Form['id'] = 'passmailerform';
		$a_Form['name'] = 'passmailerform';
		
		$this->TPL->assign('Form', $a_Form);		
		$this->TPL->assign('DataSet', $dataset);		
		$this->TPL->assign('Buttons', $a_Buttons); 
		$this->TPL->display('passmailer.tpl');
	}	
	
	
	/**
	 * @name	mailPass
	 * @desc	
	 */	
	protected function mailPass(){
		$obj_Users = loader::loadModule('Users');
		$a_User = $obj_Users->get(array('emailadres' => $this->a_Post['emailadres'], 'status' => true));
		if(!$a_User){
			$this->MESSAGES->push('Het emailadres is niet juist. Probeer het opnieuw.', 'error', 'form'); 					
			return false;
		}
		
		$obj_Tools = loader::load('Tools');
		$pass = $obj_Tools->generatePassword(6);
				
		if(!$obj_Users->save(array('id' => $a_User[0]['id'], 'wachtwoord' => $pass))){
			$this->MESSAGES->push('Er is iets fout gegaan. Probeer het opnieuw.', 'error', 'form'); 					
			return false;		
		}
		
		// mail het nieuwe wachtwoord
		try {
			$obj_Email = loader::loadModule('Email');
			$obj_Email->sendPassmailer($a_User[0]['id'], $pass);
			return true;
		} catch(Exception $e){
			$this->MESSAGES->push('Het nieuwe wachtwoord is niet verstuurd.', 'error','form');
			$this->MESSAGES->push('Er is een algemene fout opgetreden', 'general','form');
			
			dump("[CRITICAL ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}	
	}
}
