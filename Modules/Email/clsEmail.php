<?php
/**
 * @name	m_TemplateException
 * @author 	wiegerjelsma
 */
class m_EmailException extends m_ModuleException {}

/**
 * @name	m_Session
 * @author 	wiegerjelsma
 */
class m_Email extends m_Module {		
	
	/**
	 * @name	init 
	 */
	public function init(){
		parent::init();
		
		$this->setUpTemplate();
	}	
	
	/**
	 * @name	sendSystemExceptionMessage
	 * @desc	Er is een fout opgetreden in het systeem. Hier willen we via email van op de hoogte gesteld worden
	 * @throws	Exception
	 * @returns	boolean
	 */
	public function sendSystemExceptionMessage($e, $a_Params){
		try {
			$TPL = $this->TPL->getNewInstance();
			$TPL->assign('ExceptionError', $e);
			$TPL->assign('Params', $a_Params);		
				
			$txtsource = $TPL->getSource('emails/exception.tpl');
			$this->obj_Email = Loader::load('Email', 'Library', true);
			$this->obj_Email->setBodyText($txtsource);			
			
			$this->obj_Email->addTo($this->cfg['email']['systemexceptionmessage']['to']);
			$this->obj_Email->setFrom($this->cfg['email']['systemexceptionmessage']['from']['email'], $this->cfg['email']['systemexceptionmessage']['from']['name']);			
			$this->obj_Email->setReturnPath($this->cfg['email']['systemexceptionmessage']['returnpath']);
			$this->obj_Email->setReplyTo($this->cfg['email']['systemexceptionmessage']['replyto']);									
			$this->obj_Email->setSubject($this->cfg['email']['systemexceptionmessage']['subject']);	
			   			
   			$this->obj_Email->send('localhost'); // Hier niet de POSTMARK app gebruiken  			
   			
			$obj_History = Loader::LoadModule('History');
			$obj_History->trackEvent('Email', 'Sent', 'Exception message', $e->getMessage());    			
   			
	 	} catch(Exception $e){
			dump("[EMAIL ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			throw new Exception('Unable to send exception message');
	 	}   				
	}	
}