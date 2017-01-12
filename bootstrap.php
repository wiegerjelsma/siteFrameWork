<?php
session_start();
define("IS_CRON", false);
define("IS_INSTALL", false);
ini_set('display_errors', true);
ini_set('html_errors', false);
mb_internal_encoding("UTF-8");
setlocale(LC_ALL, 'nl_NL');

require_once 'Config/defines.php';
require_once LIBRARY_DIR.'ErrorHandler/clsErrorHandler.php';
require_once LIBRARY_DIR.'Loader/clsLoader.php';
require_once LIBRARY_DIR.'Config/clsConfig.php';

//ini_set("error_log", ROOTFRAMEWORK.LOG_DIR.'php.log');
$bootstrap = new Bootstrap();

class Bootstrap {

	
	protected $cfg = array();
	
	protected $a_Path;
	protected $server;
	
	protected $domainnameConfig;
	
	protected $application;
	protected $language;
	protected $suggestedUrl;
	protected $suggestedPathInfo;
	
	public function __construct(){
		require CONFIG_DIR.'Framework.conf';
		
		$this->cfg = $cfg;
		
		$this->standardIncludes();
				
		$this->splitPathInfo();
		$this->setServerVars();
		$this->setDomainVars();		
		
		$this->subdomainRedirect();
		
		$this->setApplicationVars();
								
		$valid = $this->validateApplicationOnDomain();
		
		/*
		print '<pre>';
		print_r($this->application);
		die();
		*/

		
		if(!$valid){
			$this->splitPathInfo($this->suggestedPathInfo);
			$this->setApplicationVars();						
		}
		
		// set defines
		$this->setApplicationDefines();		
		$this->setDomainDefines();
		$this->setServerDefines();
		
		$this->createLogger();
		
		$this->loadConfig();
		
		// set language
//		$this->setLanguage();
//		$this->validateLanguage();
//		$this->setLanguageDefines();

		// nadat de language geset is kunnen we de urls definen
//		$this->setUrlDefines();		
		
		try {
			$bootstrapper = Loader::loadBootstrapper();
			$bootstrapper->run($this->application);
		} catch (Exception $e){
			// als we hier zijn is er een error teruggekomen..
			// met andere woorden, error pagina tonen

			
									
			ErrorHandler::handleError($e);
		}				
	}

	
	private function loadConfig(){
		$obj_Config = Config::singleton();		
		$this->cfg = $obj_Config->getConfigApplication();
	}
	
	private function createLogger(){
		require_once 'Zend/Log.php';
		require_once 'Zend/Log/Writer/Stream.php';
		require_once 'Zend/Registry.php';
				
		$writer = new Zend_Log_Writer_Stream(ROOTFRAMEWORK.LOG_DIR.$this->application['name'].'.log');
		ini_set("error_log", ROOTFRAMEWORK.LOG_DIR.$this->application['name'].'.log');
		//$writer = new Zend_Log_Writer_Stream('php://output');
		
		$logger = new Zend_Log($writer);
		Zend_Registry::set('logger', $logger);	
	}
	
	private function standardIncludes(){	
		require_once LIBRARY_DIR.'Functions/debug.php';
		require_once LIBRARY_DIR.'Functions/functions.php';
		require_once CONFIG_DIR.'domainnames.php';	
		
		$this->domainnameConfig['subdomains'] = $subdomains;
		$this->domainnameConfig['domains'] = $domains;
		$this->domainnameConfig['doubleext'] = $doubleext;
	}
	
	private function splitPathInfo($pathinfo = false){
		$pathinfo = $pathinfo ? $pathinfo : (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : false);
		if($pathinfo && !defined('PATHINFO'))
			define('PATHINFO', $pathinfo);
			
		$a_Pathinfo = explode('?', $pathinfo);
		$pathinfo = $a_Pathinfo[0];		
		if($pathinfo){
			$sPath = preg_replace("#^\/#","",$pathinfo);
			$sPath = preg_replace("/\/$/","",$sPath);
			$this->a_Path = explode("/",$sPath);
		} else
			$this->a_Path = array();
	}
	
	private function subdomainRedirect(){
		if($this->server['subdomain'] && array_key_exists($this->server['subdomain'], $this->domainnameConfig['subdomains'])){
			header('Location: '.$this->domainnameConfig['subdomains'][$this->server['subdomain']]);
			exit();
		}
	}
	
	private function setServerVars(){
		$this->server['protocol'] = (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
		$this->server['name'] = preg_replace("/^www\./", "", $_SERVER['SERVER_NAME']);
		$this->server['port'] = $_SERVER['SERVER_PORT'];
	}
	
	private function setDomainVars(){		
		if(preg_match("/^(\d+).(\d+).(\d+).(\d+)$/",$this->server['name'])){
			list($this->server['subdomain'], $this->server['domain'], $this->server['tld']) = array(0, 0, 0);
		} else {
			$a_Server = explode(".", $this->server['name']);	
			switch(count($a_Server)){
				default:
					list($this->server['subdomain'], $this->server['domain'], $this->server['tld']) = array(0, 0, 0);
				break;
				case 2:
					list($this->server['domain'], $this->server['tld']) = $a_Server;
					$this->server['subdomain'] = false;
				break;		
				case 3:
				case 4:	
					$a_Ext[] = array_pop($a_Server); // vierde of derde
					$a_Ext[] = array_pop($a_Server); // derde of tweede
					
					// we gaan kijken of de laatste twee elementen in de array a_Extensies voorkomen.
					$match = '.'.$a_Ext[1].'.'.$a_Ext[0];			
					if(in_array($match, $this->domainnameConfig['doubleext'])){
						// we hebben een match dus $match is de extentie
						$this->server['tld'] = preg_replace("/^./", "", $match);			
						
						$this->server['domain'] = array_pop($a_Server);
						$this->server['subdomain'] = (count($a_Server) >= 1) ? array_shift($a_Server) : false;				
					} else {
						$a_Server[] = $a_Ext[1]; // de laatste er weer aan plakken.
						$a_Server[] = $a_Ext[0]; // de laatste er weer aan plakken.
										
						array_reverse($a_Server);
						$this->server['tld'] = array_pop($a_Server);
						$this->server['domain'] = array_pop($a_Server);
						$this->server['subdomain'] = (count($a_Server) >= 1) ? array_shift($a_Server) : false;		
					}
				break;
			}
		}		
	}
	

	/**
	 * @name	setApplicationVars
	 * @desc	sets the application vars
	 */
	private function setApplicationVars(){
		$this->application['scriptname'] = isset($_SERVER['SCRIPT_NAME']) ? str_replace('/','', str_replace('.php','', $_SERVER['SCRIPT_NAME'])) : false;
	//	if(isset($this->server['subdomain']) && $this->server['subdomain']){
	//		$this->application['name'] = 'ka.front.galerie';
	//		$this->application['param'] = $this->server['subdomain'];			
	//	} else {		
			$this->application['name'] = (count($this->a_Path)>0 && $this->a_Path[0]) ? strtolower($this->a_Path[0]) : false;	
			$a_App = explode(':', $this->application['name']);
			$this->application['name'] = array_shift($a_App);
			$this->application['name'] = $this->application['name'] ? $this->application['name'] : $this->cfg['defaults']['application'];				
			$this->application['param'] = (count($a_App)>0) ? $a_App[0] : false;
	//	}
		$this->application['dir'] = ($this->application['name']) ? str_replace('.','/'.APPLICATIONS_DIR,$this->application['name']) : '';
		$this->application['dir'] = !preg_match("#/$#", $this->application['dir']) ? $this->application['dir'].'/' : $this->application['dir'];
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


	/**
	 * @name
	 * @desc
	 */
	protected function setLanguageDefines(){
		define('MULTILINGUAL', $this->cfg['multilingual']);
		if($this->cfg['multilingual'])
			define('LANGUAGE_NAME', $this->language['name']);
	}


	/**
	 * @name
	 * @desc
	 */
	protected function setLanguage(){
		if($this->cfg['multilingual']){
			$this->language['name'] = isset($this->a_Path[1]) && ($this->a_Path[1]) ? $this->a_Path[1] : false;
			$a_Language = explode(':', $this->language['name']);
			$this->language['name'] = array_shift($a_Language);
			$this->language['param'] = $a_Language;
			$this->language['name'] = (($this->language)&&(!preg_match("/^[A-Za-z]$/", $this->language['name']))) ? $this->language['name'] : false;				
		}
	}	
		

	/**
	 * @name
	 * @desc
	 */
	private function validateApplicationOnDomain(){
		$valid = false;
		
		if($this->server['subdomain'] && $this->server['domain'] && $this->server['tld']){
			
			# domainname in array $application
			if(array_key_exists($this->server['subdomain'].'.'. $this->server['domain'].'.'.$this->server['tld'], $this->domainnameConfig['domains'][$this->server['protocol']])){
				
				# application in array $application[domain]
				if(in_array($this->application['name'], $this->domainnameConfig['domains'][$this->server['protocol']][$this->server['subdomain'].'.'. $this->server['domain'].'.'.$this->server['tld']]))
					$valid = true;
				elseif(is_array($application[$this->server['protocol']][$this->server['subdomain'].'.'. $this->server['domain'].'.'.$this->server['tld']])){
					$this->suggestedUrl = $this->server['protocol'].'://'.$this->server['subdomain'].'.'. $this->server['domain'].'.'.$this->server['tld'].'/'.FWPREFIX.'/'.$this->domainnameConfig['domains'][$this->server['protocol']][$this->server['subdomain'].'.'. $this->server['domain'].'.'.$this->server['tld']][0];
					$this->suggestedPathInfo = '/'.$this->domainnameConfig['domains'][$this->server['protocol']][$this->server['subdomain'].'.'. $this->server['domain'].'.'.$this->server['tld']][0];
				}
			}
			
			# domainname (* subdomain) in array $application
			if(array_key_exists('*.'.$this->server['domain'].'.'.$this->server['tld'], $this->domainnameConfig['domains'][$this->server['protocol']])){
				
				# application in array $application[domain (* subdomain)]
				if(in_array($this->application['name'], $this->domainnameConfig['domains'][$this->server['protocol']]['*.'. $this->server['domain'].'.'.$this->server['tld']]))
					$valid = true;
				elseif(is_array($this->domainnameConfig['domains'][$this->server['protocol']]['*.'.$this->server['domain'].'.'.$this->server['tld']])){
					$this->suggestedUrl = $this->server['protocol'].'://'.$this->server['subdomain'].'.'. $this->server['domain'].'.'.$this->server['tld'].'/'.FWPREFIX.'/'.$this->domainnameConfig['domains'][$this->server['protocol']]['*.'. $this->server['domain'].'.'.$this->server['tld']][0];
					$this->suggestedPathInfo = '/'.$this->domainnameConfig['domains'][$this->server['protocol']]['*.'. $this->server['domain'].'.'.$this->server['tld']][0];
				}			
			}
			
		# zonder subdomain	
		} elseif($this->server['domain'] && $this->server['tld']){
			
			# domainname in array $application (zonder subdomain)
			if(array_key_exists($this->server['domain'].'.'.$this->server['tld'], $this->domainnameConfig['domains'][$this->server['protocol']])){
				
				# application in array $application[domain]
				if(in_array($this->application['name'], $this->domainnameConfig['domains'][$this->server['protocol']][ $this->server['domain'].'.'.$this->server['tld']]))
					$valid = true;
				elseif(is_array($this->domainnameConfig['domains'][$this->server['protocol']][$this->server['domain'].'.'.$this->server['tld']])){
					$this->suggestedUrl = $this->server['protocol'].'://'.$this->server['domain'].'.'.$this->server['tld'].'/'.FWPREFIX.'/'.$this->domainnameConfig['domains'][$this->server['protocol']][ $this->server['domain'].'.'.$this->server['tld']][0];
					$this->suggestedPathInfo = '/'.$this->domainnameConfig['domains'][$this->server['protocol']][ $this->server['domain'].'.'.$this->server['tld']][0];
				}			
			}
			
		# zonder subdomain & tld
		} else {
			
			# domainname in array $application (zonder subdomain & tld)
			if(array_key_exists($this->server['name'], $this->domainnameConfig['domains'][$this->server['protocol']])){
				
				# domainname in array $application (zonder subdomain & tld)
				if(in_array($this->application['name'], $this->domainnameConfig['domains'][$this->server['protocol']][$this->server['name']]))
					$valid = true;
				elseif(is_array($this->domainnameConfig['domains'][$this->server['protocol']][$this->server['name']])){
					$this->suggestedUrl = $this->server['protocol'].'://'.$this->server['name'].'/'.FWPREFIX.'/'.$this->domainnameConfig['domains'][$this->server['protocol']][$this->server['name']][0];
					$this->suggestedPathInfo = '/'.$this->domainnameConfig['domains'][$this->server['protocol']][$this->server['name']][0];
				}			
			}
		}
		
		if(!array_key_exists($this->server['protocol'], $this->domainnameConfig['domains']))
			$valid = false;
		
		return $valid;
	}
	

	/**
	 * @name
	 * @desc
	 */
	private function setServerDefines(){
		define("SERVERROOT", PROTOCOL."://".APPLICATION_DOMAIN);
	}
	
	
	/**
	 * @name
	 * @desc
	 */
	private function setApplicationDefines(){
		$a_Apps = explode('.', $this->application['name']);
		
		define("APPLICATION_NAME", $this->application['name']);				
		define("BASEAPPLICATION_NAME", $a_Apps[0]);
		
		define("APPLICATION_DIR", $this->application['dir']);
		define("APPLICATION_DOMAIN", $this->server['name']);
		define("APPLICATION_PARAM", $this->application['param']);

		define("INPUT_APPLICATION", ROOTFRAMEWORK.INPUT_DIR.APPLICATION_NAME.'/');
		define("INPUT_BASEAPPLICATION", ROOTFRAMEWORK.INPUT_DIR.BASEAPPLICATION_NAME.'/');
		define("OUTPUT_APPLICATION", ROOTFRAMEWORK.OUTPUT_DIR.APPLICATION_NAME.'/');	
		define("OUTPUT_BASEAPPLICATION", ROOTFRAMEWORK.OUTPUT_DIR.BASEAPPLICATION_NAME.'/');	
		define("BIN_APPLICATION", ROOTFRAMEWORK.BIN_DIR.APPLICATION_NAME.'/');		
		define("BIN_BASEAPPLICATION", ROOTFRAMEWORK.BIN_DIR.BASEAPPLICATION_NAME.'/');
		define("ROOT_APPLICATION", ROOTFRAMEWORK.APPLICATIONS_DIR.APPLICATION_DIR.ROOT_DIR);		
	}
	
	
	/**
	 * @name
	 * @desc
	 */
	protected function setDomainDefines(){
		define("PROTOCOL", $this->server['protocol']);
		define("SUBDOMAIN", $this->server['subdomain']);
		define("TLD", $this->server['tld']);
		define("DOMAIN", $this->server['domain']);				
		define("QUERYSTRING", $_SERVER['QUERY_STRING']);
	}	
	
	
	/**
	 * @name
	 * @desc
	 */
	private function setUrlDefines(){
		define("IS_AJAX", preg_match('/\.ajax$/',$this->application['scriptname']));
		define("ORIG_FWPREFIX", preg_replace('/\.ajax$/','',$this->application['scriptname']));
	
		$suffix = ($this->cfg['multilingual']) ? '/'.LANGUAGE_NAME : '';
		define("APPLICATION_URL", PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.ORIG_FWPREFIX.'/'.APPLICATION_NAME.$suffix);
		define("APPLICATION_URL_AJAX", PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.ORIG_FWPREFIX.'.ajax/'.APPLICATION_NAME.$suffix);	
		
		define("APPLICATION_URL_SHORT", PROTOCOL.'://'.APPLICATION_DOMAIN.'/');
		define("APPLICATION_URL_AJAX_SHORT", PROTOCOL.'://'.APPLICATION_DOMAIN.'/');
		
		
/*		$a_Applications = array();
		$applications = preg_split("/\./", APPLICATION_NAME);
		foreach($applications as $application){
			$a_Applications[] = join("_",$applications);
			array_pop($applications);
		}
		foreach($a_Applications as $application){
			define("APPLICATION_URL_".strtoupper($application), PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.ORIG_FWPREFIX.'/'.str_replace("_",".",$application).$suffix);
		}	*/			
		
		define("FRAMEWORK_URL", PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.ORIG_FWPREFIX);
									
	}
}