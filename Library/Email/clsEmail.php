<?php
require_once 'Zend/Mail.php';
require_once 'Zend/Mail/Transport/Smtp.php';

/**
 * @name	Email
 */
class Email extends Zend_Mail {	

	private static $instance;
	
	private $localTransport;
   	
    public static function singleton(){
    	if (!isset(self::$instance)) {
     		$c = __CLASS__;
       		self::$instance = new $c;
    	}
    
       	return self::$instance;
   	}  	
   	
   	public function __construct(){
   		$this->localTransport = new Zend_Mail_Transport_Smtp('localhost');
    	if(defined("SMTP_POSTMARK") && SMTP_POSTMARK){
			require_once 'Mail_Transport_Postmark.php';    	
    		Zend_Mail::setDefaultTransport(new Mail_Transport_Postmark(SMTP_POSTMARK_APIKEY));
    	} else {
	    	# we are using smtp
	    	if(defined("SMTP_USER") && defined("SMTP_PASS")){
				$authConfig = array('auth' => 'login', 'username' => SMTP_USER, 'password' => SMTP_PASS);
				$this->transport = new Zend_Mail_Transport_Smtp(SMTP, $authConfig);
				Zend_Mail::setDefaultTransport($this->transport);
	    	} else {
				Zend_Mail::setDefaultTransport($this->localTransport);    	
	    	}						
    	}   		
   	}   	
   	
   	
   	/**
   	 * @name	send
   	 */
	public function send($tr = false){
		if($tr == 'localhost')
			parent::send($this->localTransport);
		else
			parent::send();			
	}   	
	
	public function setPostmarkTag($tag){
		if(!defined("SMTP_POSTMARK") or !SMTP_POSTMARK)
			return;
			
		parent::addHeader('PostmarkTag', $tag);
	}
	
   	
   	/**
   	 * @name	setBodyText
   	 * @desc	Content
   	 */
	public function setBodyText($src, $charset = false, $encoding = false){
		// fix the bare lf.. (\n -> \r\n)
		$body = preg_replace("/^(?=\n)|[^\r](?=\n)/", "\\0\r", $src);
		parent::setBodyText($body, $charset, $encoding);
	}
	
	
	/**
   	 * @name	setBodyHtml
   	 * @desc	Content
   	 */
	public function setBodyHtml($src, $charset = false, $encoding = false){
		// fix the bare lf.. (\n -> \r\n)
		$body = preg_replace("/^(?=\n)|[^\r](?=\n)/", "\\0\r", $src);
		parent::setBodyHtml($body, $charset, $encoding);		
	}
}