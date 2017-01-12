<?php
session_start();
define("IS_CRON", true);
define("IS_INSTALL", false);
ini_set('display_errors', true);
ini_set('html_errors', true);

require_once 'Config/defines.php';
require_once LIBRARY_DIR.'ErrorHandler/clsErrorHandler.php';
require_once LIBRARY_DIR.'Loader/clsLoader.php';
require_once LIBRARY_DIR.'Config/clsConfig.php';

$cron = new Cron();

class Cron {
	
	protected $cfg = array();
	
	protected $a_Options;
	
	protected $application;
	
	protected $a_Cronjobs = array();
	
	public function __construct(){
		require CONFIG_DIR.'Framework.conf';
		$this->cfg = $cfg;
		
		$this->getOptions();
		
		$this->setApplicationVars();		
		$this->setApplicationDefines();			
		
		$this->createLogger();
		$this->standardIncludes();
		$this->loadConfig();
		
		$this->getCronjobs();
		
		// set language
		$this->setLanguage();
//		$this->validateLanguage();
//		$this->setLanguageDefines();	
		
		try {
			$time_jobstart = microtime(true);
			
			foreach($this->a_Cronjobs as $cronjob)
				$this->execute($cronjob);
		
			$time_jobend = microtime(true);
			$time_job = $time_jobend - $time_jobstart;					
				
		} catch (Exception $e){

			$a_Params['cronjob'] = $cronjob;
			
			$obj_Cronjob = Loader::loadCronjob('Cronjob');				 						 				
	 		$obj_Cronjob->sendEmailException($e, $a_Params);
		}				
	}
	
	private function execute($_file){

		# get the vars
		$a_Vars = array();
		if(strpos($_file, '?')){
			list($class_method, $_v) = explode('?', $_file);
			$_vars = explode("&", $_v);
			foreach($_vars as $v){
				list($key, $value) = explode('=', $v);
				$a_Vars[$key] = $value;
			}
		} else
			$class_method = $_file;
			
		list($class, $method) = explode('::', $class_method);	
		
		try {		
			$obj_Cronjob = Loader::loadCronjob(ucfirst($class));
			$obj_Cronjob->setvars($a_Vars);
			
			try {
				dump('cron.php : '.$class.'::'.$method);			
				$time_start = microtime(true);
			
				$result = $obj_Cronjob->{$method}();
			
				$time_end = microtime(true);
				$time = $time_end - $time_start;		

				if($result !== false)
					dump('cron.php : Script took '.round($time, 4).' seconds to parse');			
		
			} catch (Exception $e) {
				dump('[CRON ERROR] : '.$e->getMessage()."\n".'['.date("Y-m-d H:i:s").'] from file '.$e->getFile().' at line '.$e->getLine());
			}			
		} catch(Exception $e){
			dump('[CRON ERROR] : '.$e->getMessage()."\n".'['.date("Y-m-d H:i:s").'] from file '.$e->getFile().' at line '.$e->getLine());		
		}		
	}
	
	private function setLanguage(){
		define('LANGUAGE_NAME', 'en');
	}

	
	private function loadConfig(){
		$obj_Config = Config::singleton();		
		$this->cfg = $obj_Config->getConfigApplication();
	}
	
	private function createLogger(){
		require_once 'Zend/Log.php';
		require_once 'Zend/Log/Writer/Stream.php';
		require_once 'Zend/Registry.php';
				
		$writer = new Zend_Log_Writer_Stream(ROOTFRAMEWORK.LOG_DIR.'cron.'.$this->application['name'].'.log');
		
//		$writer = new Zend_Log_Writer_Stream(ROOTFRAMEWORK.LOG_DIR.'cron.'.$this->application['name'].'.'.date('Ymd').'.log');
		ini_set("error_log", ROOTFRAMEWORK.LOG_DIR.'cron.'.$this->application['name'].'.'.date('Ymd').'.log');
		ini_set("error_log", ROOTFRAMEWORK.LOG_DIR.'cron.'.$this->application['name'].'.log');
		//$writer = new Zend_Log_Writer_Stream('php://output');
		
		$logger = new Zend_Log($writer);
		Zend_Registry::set('logger', $logger);	
	}
	
	private function standardIncludes(){
		require_once LIBRARY_DIR.'Functions/debug.php';
		require_once LIBRARY_DIR.'Functions/functions.php';		
	}
	
	private function getOptions(){
		$this->a_Options = getopt("a:"); // a -> Application
	}
	
	private function getCronjobs(){
		$cronconfig = ROOTFRAMEWORK.APPLICATIONS_DIR.APPLICATION_DIR.CRONJOBS_DIR.'cronjobs.conf';
		$cron = array();
		if(is_file($cronconfig))
			require $cronconfig;
		else
			return;
			
		# de datum en tijd
		# minuut (i) uur (H) dag (d) maand (m) weekdag (w)
		$date = explode(".",date("i.H.d.m.w"));
		foreach($date as $value)
			$a_Date[] = $value * 1;	
			
		# haal de files voor dit tijdstip uit de cron array
		$a = $cron;
		for($i=0; $i<count($a_Date); $i++){
			$a_Keys = array();
			foreach($a as $key => $value){
				if(($key == '*') or ($key == $a_Date[$i]))
					$a_Keys[] = $key;
			}
			
			$obj_Library = Loader::loadLibrary('Library');

			if(count($a_Keys) == 2)
				$a = $obj_Library->arrayCombine($a[$a_Keys[0]], $a[$a_Keys[1]]);
			elseif(count($a_Keys) == 1)
				$a = $a[$a_Keys[0]];
			else
				$a = array();
		}
		
		foreach($a as $server => $a_Files){
			if(($server != SERVER_NAME) && ($server != '*'))
				continue;
				
			foreach($a_Files as $file)
				$this->a_Cronjobs[] = $file;
		}							
	}
	

	/**
	 * @name	setApplicationVars
	 * @desc	sets the application vars
	 */
	private function setApplicationVars(){
		$this->application['name'] = (count($this->a_Options)>0 && $this->a_Options['a']) ? strtolower($this->a_Options['a']) : false;	
		$a_App = explode(':', $this->application['name']);
		$this->application['name'] = array_shift($a_App);
		$this->application['name'] = $this->application['name'] ? $this->application['name'] : $this->cfg['defaults']['application'];				
		$this->application['param'] = (count($a_App)>0) ? $a_App[0] : false;
		$this->application['dir'] = ($this->application['name']) ? str_replace('.','/'.APPLICATIONS_DIR,$this->application['name']) : '';
		$this->application['dir'] = !preg_match("#/$#", $this->application['dir']) ? $this->application['dir'].'/' : $this->application['dir']; 
	}
	
	
	/**
	 * @name
	 * @desc
	 */
	private function setApplicationDefines(){
		define("APPLICATION_NAME", $this->application['name']);
		define("APPLICATION_DIR", $this->application['dir']);
		//define("APPLICATION_DOMAIN", $this->server['name']);
		define("APPLICATION_PARAM", $this->application['param']);

		define("INPUT_APPLICATION", ROOTFRAMEWORK.INPUT_DIR.APPLICATION_NAME.'/');
		define("OUTPUT_APPLICATION", ROOTFRAMEWORK.OUTPUT_DIR.APPLICATION_NAME.'/');	
		define("BIN_APPLICATION", ROOTFRAMEWORK.BIN_DIR.APPLICATION_NAME.'/');
		define("ROOT_APPLICATION", ROOTFRAMEWORK.APPLICATIONS_DIR.APPLICATION_DIR.ROOT_DIR);		
	}
	
	
	/**
	 * @name
	 * @desc
	 */
	private function setUrlDefines(){
	
		$suffix = ($this->cfg['multilingual']) ? '/'.LANGUAGE_NAME : '';
		define("APPLICATION_URL", PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.FWPREFIX.'/'.APPLICATION_NAME.$suffix);
		define("APPLICATION_URL_AJAX", PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.FWPREFIX.'.ajax/'.APPLICATION_NAME.$suffix);	
		define("FRAMEWORK_URL", PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.FWPREFIX);							
	}
}