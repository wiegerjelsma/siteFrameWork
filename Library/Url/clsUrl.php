<?php
class Url {
	
	private static $instance;
	
    public static function singleton(){
    	if (!isset(self::$instance)) {
     		$c = __CLASS__;
       		self::$instance = new $c;
    	}
    
       	return self::$instance;
   	}	
   	
	public function RedirectUrl($url){
		$url = preg_replace("#^/#","",$url);
		header('Location: '.$url);
		exit;
	}   
	
	public function Redirect($url = false){	
		$url = preg_replace("#^/#","",$url);
		$url = APPLICATION_URL.'/'.$url;
		header('Location: '.$url);
		exit;		
	}
	
	public function RedirectToApplication($app){	
		$url = FRAMEWORK_URL.'/'.$app;
		header('Location: '.$url);
		exit;		
	}
	
	public function RedirectToShortUrl($short_url){	
		$url = PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.$short_url;
		header('Location: '.$url);
		exit;		
	}
	
	
}