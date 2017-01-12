<?php
/**
 * @name	c_ControllerException
 * @author 	wiegerjelsma
 */
class c_ControllerException extends ErrorHandler {}

/**
 * @name	c_Controller
 * @author 	wiegerjelsma
 */
class c_Controller {
	
	protected $cfg;
	protected static $instance;
	protected $TPL;
	protected $SESSION;
	protected $MESSAGES;
	
	protected $myname;	
	
	protected $a_Post;
	protected $a_Get;
	
	
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
	 * @name	init
	 */
	public function init(){		
		$this->setUpTemplate();
		$this->setUpMessages();		
		$this->defineBasicVars();				
	}
	
	
	/**
	 * @name getNewInstance()
	 */
	public function getNewInstance(){
		return Loader::getNewInstance(get_class($this), 'Controller');

	}	

	
	/**
	 * @name	singleton
	 * @param 	string $class
	 */
	public static function singleton($class){		
		if(!isset(self::$instance)){
			self::$instance = array('object' => new $class(), 'class' => $class);
		}
       	return self::$instance['object'];
   	}
   	
   	
	/**
   	 * @name 	setUpTemplate
   	 */
   	protected function setUpTemplate(){
		if(array_key_exists('tplengine', $this->cfg['defaults']))
			$this->TPL = Loader::loadModule($this->cfg['defaults']['tplengine']);
	}   	
	
	
	/**
   	 * @name 	setUpSession
   	 */
   	protected function setUpSession(){
		if(array_key_exists('sessionengine', $this->cfg['defaults']))
			$this->SESSION = Loader::loadModule($this->cfg['defaults']['sessionengine']);		
	}   
	
	/**
   	 * @name 	setUpMessages
   	 */
   	protected function setUpMessages(){
		$this->MESSAGES = Loader::loadModule('Messages');		
	}   
   	
	
	/**
   	 * @name 	defineBasicVars
   	 */
   	protected function defineBasicVars(){
		$this->defineWebroots();
	}
	
	
	/**
   	 * @name 	defineWebroots
   	 */
   	protected function defineWebroots(){
		define("WEBROOT", "/".FWPREFIX."Root/".APPLICATION_NAME);
		define("WEBROOT_EXTERN", PROTOCOL."://".APPLICATION_DOMAIN.WEBROOT);
		
		$a_Applications = array();
		$applications = preg_split("/\./", APPLICATION_NAME);
		foreach($applications as $application){
			$a_Applications[] = join("_",$applications);
			array_pop($applications);
		}
		foreach($a_Applications as $application){
			$root = "/".FWPREFIX."Root/".str_replace("_",".",$application);
			define("WEBROOT_".strtoupper($application), "/".FWPREFIX."Root/".str_replace("_",".",$application));
			define("WEBROOT_EXTERN_".strtoupper($application), PROTOCOL."://".APPLICATION_DOMAIN.$root);
		}

		define("WEBROOT_FW", "/".FWPREFIX."Root/framework");		
		define("WEBROOT_EXTERN_FW", PROTOCOL."://".APPLICATION_DOMAIN."/".FWPREFIX."Root/framework");		
	}   	
}