<?php
/**
 * @name	c_ControllerException
 * @author 	wiegerjelsma
 */
class b_BootstrapperException extends ErrorHandler {}

/**
 * @name	c_Controller
 * @author 	wiegerjelsma
 */
class b_Bootstrapper {
	
	protected $cfg;
	protected static $instance;
	
	protected $application;
	
	protected $controller;
	protected $function;
	protected $id;
	protected $a_Path;	

	protected $obj_Controller;
	
	protected $shorturl;
	
	/**
	 * @name	pushConfig
	 * @param 	array $cfg
	 * @desc	de controller config (incl application / framework cfg) wordt gepushed vanuit loader
	 */
	public function pushConfig($cfg){
		$this->cfg = $cfg;
	}	
	
	public function init(){
		
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
	 * @name getNewInstance()
	 */
	public function getNewInstance(){
		return Loader::getNewInstance(get_class($this), 'Bootstrapper');
	}
   	
   	
   	/**
   	 * @name	run
   	 */
	public function run($application){
		$this->application = $application;
		
		$path = $this->rewritepath();
		
		$this->splitPathInfo($path);		
			
		$this->setLanguage();
		$this->validateLanguage();
		$this->setLanguageDefines();
			
		$this->setUrlDefines();
		
		if($this->ipCheck()){
			
			$this->setController();
			$this->setFunction();	
			$this->setId();		
		
			if($this->validateController()){
				$this->setControllerDefines();
				$this->loadController();
				$this->setControllerConfig();
			
				if($this->validateFunction()){				
			
					$this->setFunctionDefines();
					$this->validateID();
					$this->setIdDefines();
										
					if(method_exists($this->obj_Controller, 'init'))
						$this->obj_Controller->init();	

					if(IS_AJAX)
						$this->obj_Controller->{'ajax_'.FUNCTION_NAME}();
					else									
						$this->obj_Controller->{FUNCTION_NAME}();
				} else
					throw new b_BootstrapperException("Unable to call '".CONTROLLER_NAME.'::'.$this->function['name']."'", Zend_Log::CRIT);	
			}
		} else {
			// hier niet valide ip adres
			dump('IP BLOCKED: '.$_SERVER['REMOTE_ADDR']);
		}
	}
	
	private function rewritepath(){
		$pathinfo = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : false;
		$pathinfo = preg_replace('/\/$/','', $pathinfo);
				
		if(!$pathinfo)
			return;	
					
		if(!isset($this->cfg['rewriteurls']) or !$this->cfg['rewriteurls']){
			return preg_replace("/^\/".str_replace(".php","",str_replace("/","",$_SERVER['SCRIPT_NAME']))."/","",$pathinfo);		
		}
		
		$patterns = array();
		$patterns[] = '/\{APPLICATION_NAME\}/';
		$patterns[] = '/\{APPLICATION_PARAM\}/';
		
		$replacements = array();
		$replacements[] = APPLICATION_NAME;
		$replacements[] = APPLICATION_PARAM;
		
		// Check of we een virtual page hebben die deze short url heeft.
		$obj_Pages = Loader::loadModule('Cms.Pages');
		if(!defined('SHORT_URL'))
			define('SHORT_URL', preg_replace('/^\//','',$pathinfo));
		$page = $obj_Pages->getVirtualPageByShortUrl(preg_replace('/^\//','',$pathinfo));
		if($page){
			$path = '/{APPLICATION_NAME}/'.$page['virtual_int_url_controller'].'/';
			$path .= $page['virtual_int_url_function'] ? $page['virtual_int_url_function'].'/' : '';
			$path .= $page['virtual_int_url_id'] ? $page['virtual_int_url_id'].'/' : '';
			return preg_replace($patterns, $replacements, $path);
		}				
		
		foreach($this->cfg['rewrites'] as $a_Rewrite){		
			if(preg_match($a_Rewrite['match_domain'], SERVERROOT)){			
				if(preg_match($a_Rewrite['pathinfo'], $pathinfo)){
					$path = preg_replace($a_Rewrite['pathinfo'], $a_Rewrite['replace'], $pathinfo);					
					return preg_replace($patterns, $replacements, $path);
				}	 				
			}
		}
		return preg_replace("/^\/".str_replace(".php","",str_replace("/","",$_SERVER['SCRIPT_NAME']))."/","",$pathinfo);		
	}
	
	/**
	 * @name
	 * @desc
	 */
	protected function setLanguage(){
		if($this->cfg['multilingual']){
		
			if(isset($this->a_Path[1]))
				$this->language['name'] = isset($this->a_Path[1]) && ($this->a_Path[1]) ? $this->a_Path[1] : false;				
			else
				$this->language['name'] = str_replace(".php","",str_replace("/","",$_SERVER['SCRIPT_NAME']));
			
			$a_Language = explode(':', $this->language['name']);
			$this->language['name'] = array_shift($a_Language);
			$this->language['param'] = $a_Language;
			$this->language['name'] = (($this->language)&&(!preg_match("/^[A-Za-z]$/", $this->language['name'])) && in_array($this->language['name'], $this->cfg['languages'])) ? $this->language['name'] : false;
			if(!$this->language['name']){
				$a_Session = Loader::loadModule($this->cfg['defaults']['sessionengine']);
				$this->language['name'] = $a_Session->read('LANGUAGE_NAME');				
			}
		}
	}
	
	/**
	 * @name
	 * @desc
	 */
	protected function setLanguageDefines(){
		define('MULTILINGUAL', $this->cfg['multilingual']);
		if(MULTILINGUAL){
			define('LANGUAGE_NAME', $this->language['name']);
			$a_Session = Loader::loadModule($this->cfg['defaults']['sessionengine']);
			$a_Session->write('LANGUAGE_NAME', $this->language['name']);
		}
	}
	
	/**
	 * @name
	 * @desc
	 */
	protected function validateLanguage(){
		if($this->cfg['multilingual']){
			if(!$this->language['name']){
				if(array_key_exists('defaults', $this->cfg) && array_key_exists('language', $this->cfg['defaults']))
					$this->language['name'] = $this->cfg['defaults']['language'];
				else
					throw new ErrorHandler('No default language available (cfg)');					
			}		
			if(!$this->language['name'])
				return false;			
		}
		return true;
	}
				
	
	private function ipCheck(){	
		if(IS_AJAX)
			return true;
			
		if(ORIG_FWPREFIX != FWPREFIX && substr(md5(date('jmY')), 0, 4) == ORIG_FWPREFIX)
			return true;	
	
		require_once CONFIG_DIR.'ipvalidation.php';
		if(defined("VALIDATE_IP") && VALIDATE_IP)
			return (in_array($_SERVER['REMOTE_ADDR'], $a_ValidIpAdresses)) ? true : false;
		else
			return true;
	}
	
	private function splitPathInfo($pathinfo = false){
		$pathinfo = $pathinfo ? $pathinfo : (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : false);
		$a_Pathinfo = explode('?', $pathinfo);
		$pathinfo = $a_Pathinfo[0];
		if($pathinfo){
			$aPath = array();
			$sPath = preg_replace("#^\/#","",$pathinfo);
			$sPath = preg_replace("/\/$/","",$sPath);
			$this->a_Path = explode("/",$sPath);
		} else
			$this->a_Path = array();			
	}		
	
	protected function setControllerConfig(){
		$obj_Config = Loader::load('Config');
		$this->cfg = $obj_Config->getConfigController(CONTROLLER_NAME);						
	}
	
	protected function validateFunction(){
		if(!$this->function['name']){
			if(array_key_exists('defaults', $this->cfg) && array_key_exists('function', $this->cfg['defaults'])){
				$this->function['name'] = $this->cfg['defaults']['function'];
			} else {
				throw new b_BootstrapperException('No default function available (cfg)');					
			}
		}		
		if(!$this->function['name']){
			return false;
		}
			
		$function = IS_AJAX ? 'ajax_'.$this->function['name'] : $this->function['name'];		
		if(method_exists($this->obj_Controller, $function)){
			return $this->function['name'];					
		}
		return false;	
	}
	
	protected function validateController(){		
		if(!$this->controller['name']){
			if(array_key_exists('defaults', $this->cfg) && array_key_exists('controller', $this->cfg['defaults']))
				$this->controller['name'] = ucfirst($this->cfg['defaults']['controller']);
			else
				throw new b_BootstrapperException('No default controller available (cfg)');					
		}		
		if(!$this->controller['name'])
			return false;
		return true;
	}
	
	protected function loadController(){
		$this->obj_Controller = Loader::loadController(CONTROLLER_NAME);
		return $this->obj_Controller;	
	}
	
	protected function validateID(){
		if(!$this->id['name']){
			if(array_key_exists('defaults', $this->cfg) && array_key_exists('id', $this->cfg['defaults']))
				$this->id['name'] = $this->cfg['defaults']['id'];
		}		
		if(!$this->id['name'])
			return false;	
	}
	
	/**
	 * @name
	 * @desc
	 */
	private function setUrlDefines(){
		define("IS_AJAX", preg_match('/\.ajax$/',$this->application['scriptname']));
		define("ORIG_FWPREFIX", preg_replace('/\.ajax$/','',$this->application['scriptname']));
	
		$suffix = ($this->cfg['multilingual']) ? '/'.LANGUAGE_NAME : '';
		define("APPLICATION_URL", PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.FWPREFIX.'/'.APPLICATION_NAME.$suffix);
		define("APPLICATION_URL_AJAX", PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.FWPREFIX.'.ajax/'.APPLICATION_NAME.$suffix);	
		
		define("APPLICATION_URL_SHORT", PROTOCOL.'://'.APPLICATION_DOMAIN.$suffix);
		define("APPLICATION_URL_AJAX_SHORT", PROTOCOL.'://'.APPLICATION_DOMAIN.$suffix);
		
		define("THIS_URL", PROTOCOL.'://'.APPLICATION_DOMAIN.$_SERVER['REQUEST_URI']);
		
		if(ORIG_FWPREFIX != FWPREFIX){
			if(!defined('PATHINFO'))
				if(!defined('SHORT_URL'))
					define('SHORT_URL', ORIG_FWPREFIX);
			else
				if(!defined('SHORT_URL'))
					define('SHORT_URL', $_SERVER['REQUEST_URI']);
		}
		if($this->cfg['multilingual']){
			foreach($this->cfg['languages'] as $language){
				if(preg_match('/\/'.LANGUAGE_NAME.'\//', THIS_URL)){
					define('LANGUAGE_URL_'.strToUpper($language), preg_replace('#\/'.LANGUAGE_NAME.'\/#','/'.$language.'/', THIS_URL));
				} else {		
					if(defined('SHORT_URL')){
						$short_url = preg_replace('/^\//','',SHORT_URL);
						define('LANGUAGE_URL_'.strToUpper($language), PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.$language.'/'.$short_url);
					} else {
						// Waarschijnlijk zitten we op domain_name/fwprefix
						define('LANGUAGE_URL_'.strToUpper($language), PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.FWPREFIX.'/'.APPLICATION_NAME.'/'.$language);
					}
				}
			}
		}
		
		define("FRAMEWORK_URL", PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.ORIG_FWPREFIX);									
	}	
	
	protected function setController(){
		$key = ($this->cfg['multilingual']) ? 2 : 1;
		$this->controller['name'] = isset($this->a_Path[$key]) && ($this->a_Path[$key]) ? $this->a_Path[$key] : false;
		$a_Mod = explode(':', $this->controller['name']);
		$this->controller['name'] = array_shift($a_Mod);
		$this->controller['param'] = isset($a_Mod[0]) ? $a_Mod[0] : false;
		$this->controller['name'] = (($this->controller)&&(!preg_match("/^[A-Za-z]$/", $this->controller['name']))) ? $this->controller['name'] : false;
		$this->controller['name'] = join('.', array_map("ucfirst", explode('.', $this->controller['name'])));	
	}
	
	protected function setFunction(){
		$key = ($this->cfg['multilingual']) ? 3 : 2;
		$this->function['name'] = (isset($this->a_Path[$key]) && ($this->a_Path[$key])) ? $this->a_Path[$key] : false;
		$a_Fn = explode(':', $this->function['name']);
		$this->function['name'] = array_shift($a_Fn);
		$this->function['param'] = isset($a_Fn[0]) ? $a_Fn[0] : false;
	}
	
	protected function setId(){
		$key = ($this->cfg['multilingual']) ? 4 : 3;
		$this->id['name'] = (isset($this->a_Path[$key]) && ($this->a_Path[$key])) ? $this->a_Path[$key] : false; 
		$a_ID = explode(':', $this->id['name']);
		$this->id['name'] = array_shift($a_ID);
		$this->id['param'] = isset($a_ID[0]) ? $a_ID[0] : false;
	}		
	
	protected function setControllerDefines(){
		define('CONTROLLER_NAME', $this->controller['name']);
		define("CONTROLLER_PARAM", $this->controller['param']);		
	}
	
	protected function setFunctionDefines(){
		define('FUNCTION_NAME', $this->function['name']);
		define("FUNCTION_PARAM", $this->function['param']);	
	}
	
	protected function setIdDefines(){
		define('ID_NAME', $this->id['name']);
		define("ID_PARAM", $this->id['param']);
	}
}