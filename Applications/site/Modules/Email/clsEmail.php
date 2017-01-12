<?php
/**
 * @name	m_site_EmailException
 * @author 	wiegerjelsma
 * @version	1.0 2012-08-30 16:05:43
 */
class m_site_EmailException extends m_EmailException {}

/**
 * @name	m_site_Email
 * @author 	wiegerjelsma
 * @version	1.0 2012-08-30 16:05:43
 */
class m_site_Email extends m_Email {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-08-30 16:05:43
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	
	/**
	 * @name	sendPassmailer
	 * @desc	Verstuurd het nieuwe wachtwoord
	 * @throws	Exception
	 * @returns	boolean	 
	 */
	public function sendContactMessage($a_Data){	 	
	 	try {
			// assign data
			$TPL = $this->TPL->getNewInstance();
			$TPL->assign('Data', $a_Data);
	 		
	 		$txtsource = $TPL->getSource('emails/contact.txt.tpl');
			$htmlsource = $TPL->getSource('emails/contact.html.tpl');			

			$this->obj_Email = Loader::load('Email', 'Library', true);	 		
			$this->obj_Email->setBodyText($txtsource);
   			$this->obj_Email->setBodyHtml($htmlsource);
   			
			$this->obj_Email->addTo($a_Data['to']);				 		
	 		
			$this->obj_Email->setFrom($a_Data['emailaddress'], $a_Data['name']);			
			$this->obj_Email->setReturnPath($a_Data['emailaddress']);
			$this->obj_Email->setReplyTo($a_Data['emailaddress']);									
			$this->obj_Email->setSubject($a_Data['subject']);				
																
//   			$this->obj_Email->setPostmarkTag('account');

//			$obj_FileSystem = Loader::load('FileSystem');
//			$filename = date('Ymd-His').'-WEBFORM-'.$a_Data['emailaddress'];
//			$obj_FileSystem->writeApplicationFile($filename.'.html', 'Emails', $htmlsource);								
  // 			$obj_FileSystem->writeApplicationFile($filename.'.txt', 'Emails', $txtsource);								   			   			
	   											
	 		$this->obj_Email->send('localhost');	
	 		
//			$obj_History = Loader::LoadModule('History');
//			$obj_History->trackEvent('Email', 'Sent', 'webform', $a_Data['emailaddress']);	 									
	 		
	 	} catch(Exception $e){
			dump("[EMAIL ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			throw new Exception('Unable to send contact message');
	 	}	 
	 	return false;	
	}	
	
	/**
	 * @name	sendPassmailer
	 * @desc	Verstuurd het nieuwe wachtwoord
	 * @throws	Exception
	 * @returns	boolean	 
	 */
	public function sendPassmailer($user_id, $password){	 	
	 	try {
			$obj_Users = Loader::loadModule('Users');		 	
	 		$a_User = $obj_Users->get(array('id' => $user_id));
	 		
			// assign data
			$TPL = $this->TPL->getNewInstance();
			$TPL->assign('User', $a_User[0]);
			$TPL->assign('Wachtwoord', $password);
	 		
	 		$txtsource = $TPL->getSource('emails/passmailer.txt.tpl');
			$htmlsource = $TPL->getSource('emails/passmailer.html.tpl');			

			$this->obj_Email = Loader::load('Email', 'Library', true);	 		
			$this->obj_Email->setBodyText($txtsource);
   			$this->obj_Email->setBodyHtml($htmlsource);
   			
			$this->obj_Email->addTo($a_User[0]['emailadres']);				 		
	 		
			$this->obj_Email->setFrom($this->cfg['email']['passmailer']['from']['email'], $this->cfg['email']['passmailer']['from']['name']);			
			$this->obj_Email->setReturnPath($this->cfg['email']['passmailer']['returnpath']);
			$this->obj_Email->setReplyTo($this->cfg['email']['passmailer']['replyto']);									
			$this->obj_Email->setSubject($this->cfg['email']['passmailer']['subject']);				
																
//   			$this->obj_Email->setPostmarkTag('account');

//			$obj_FileSystem = Loader::load('FileSystem');
//			$filename = date('Ymd-His').'-PASSMAILER-'.$a_User[0]['emailadres'];
//			$obj_FileSystem->writeApplicationFile($filename.'.html', 'Emails', $htmlsource);								
  // 			$obj_FileSystem->writeApplicationFile($filename.'.txt', 'Emails', $txtsource);								   			   			
	   											
	 		$this->obj_Email->send('localhost');	
	 		
//			$obj_History = Loader::LoadModule('History');
//			$obj_History->trackEvent('Email', 'Sent', 'passmailer', $a_User[0]['emailadres']);	 									
	 		
	 	} catch(Exception $e){
			dump("[EMAIL ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			throw new Exception('Unable to send passmailer message');
	 	}	 
	 	return false;	
	}
	
	public function sendForm($a_Form, $a_Data, $a_FormElements){	
	 	try {	 		
			// assign data
			$TPL = $this->TPL->getNewInstance();
			
			$a_ToAssign = array();
			$count = 1;
			foreach($a_FormElements as $element){
				$count++;
				switch($element['type']){
					default:
						$a_ToAssign[] = array('label' => $element['label'], 'value' => $a_Data[$element['fieldname']]);			
					break;
					case 'seperator':
					case 'header':
					case 'comment':
						continue;
					break;
				}
			}			
			
			$TPL->assign('Data', $a_ToAssign);
			$TPL->assign('Form', $a_Form);
						
	 		$txtsource = $TPL->getSource('emails/forms.txt.tpl');
//			$htmlsource = $TPL->getSource('emails/forms.html.tpl');			

			$this->obj_Email = Loader::load('Email', 'Library', true);	 		
			$this->obj_Email->setBodyText($txtsource);
//  			$this->obj_Email->setBodyHtml($htmlsource);
   			
   			if(preg_match('/;/', $a_Form['emailaddress'])){
   				$a_EmailAdresses = explode(';', $a_Form['emailaddress']);   			
   			} else {
   				$a_EmailAdresses = explode(',', $a_Form['emailaddress']);   			
   			}
   			
			$this->obj_Email->addTo(trim(array_shift($a_EmailAdresses)));
			foreach($a_EmailAdresses as $emailadres)
				$this->obj_Email->addCc(trim($emailadres));
			
//			$this->obj_Email->addCc('wieger@medotdesign.com');
							 		
			$this->obj_Email->setFrom($this->cfg['email']['forms']['from']['email'], $this->cfg['email']['forms']['from']['name']);			
			$this->obj_Email->setReturnPath($this->cfg['email']['forms']['returnpath']);
			$this->obj_Email->setReplyTo($this->cfg['email']['forms']['replyto']);									
			$this->obj_Email->setSubject(str_replace('{formname}',$a_Form['name'], $this->cfg['email']['forms']['subject']));				
																
   			$this->obj_Email->setPostmarkTag('osteovitaal');

//			$obj_FileSystem = Loader::load('FileSystem');
//			$filename = date('Ymd-His').'-FORMS-'.$a_Form['name'].'-'.$a_Form['emailaddress'];
//			$obj_FileSystem->writeApplicationFile($filename.'.html', 'Emails', $htmlsource);								
//   			$obj_FileSystem->writeApplicationFile($filename.'.txt', 'Emails', $txtsource);								   			   			
	   											
	 		$this->obj_Email->send();	
	 		
//			$obj_History = Loader::LoadModule('History');
//			$obj_History->trackEvent('Email', 'Sent', 'passmailer', $a_Form['emailaddress']);	 									
	 		
	 	} catch(Exception $e){
			dump("[EMAIL ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			throw new Exception('Unable to send form message');
	 	}	 
	 	return false;			
	}	
	
}
