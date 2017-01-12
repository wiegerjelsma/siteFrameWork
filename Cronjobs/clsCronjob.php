<?php
class cron_CronjobException extends ErrorHandler {}

class cron_Cronjob {
	
	protected $cfg;
	
	protected $vars;
	
	protected $myname;	
	
	/**
	 * @name	init
	 * @param 	array $cfg, array $a_Vars
	 */
	public function setvars($a_Vars){		
		$this->vars = $a_Vars;
   	}
   	
   	
	/**
	 * @name	pushConfig
	 * @param 	array $cfg
	 * @desc	de module config (incl framework / application cfg) wordt gepushed vanuit loader
	 */
	public function pushConfig($cfg, $myname){
		$this->cfg = $cfg;
		$this->myname = $myname;
	}	
	
   	
   	/**
   	 * @name	handle
   	 */
   	public function sendEmailException($error, $a_Params){
   		try {	
   			$obj_Email = Loader::loadModule('Email');
   			$obj_Email->sendSystemExceptionMessage($error, $a_Params);
	 	} catch(Exception $e){	 					
			dump("[CRON ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());	 				
			dump($e,'$e');
			dump($error,'$error');
			dump($a_Params,'$a_Params');			
	 	}   		
   	}
}