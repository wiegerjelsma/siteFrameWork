<?php
class Config {
	
	private static $instance;

	private static $a_Configs = array();
	
	private static $a_RequiredFiles = array();
	
	public static function singleton(){
    	if (!isset(self::$instance)) {
     		$c = __CLASS__;
       		self::$instance = new $c;
    	}
       	return self::$instance;
   	}
   	
   	
   	public function getConfigFramework(){
		if(isset(self::$a_Configs['framework']))
			return self::$a_Configs['framework'];
   		
   		$cfg = array();
		require BIN_APPLICATION.CONFIG_DIR.'Framework.conf';
		self::$a_Configs['framework'] = $cfg;
		return self::$a_Configs['framework'];
   	}
   	
   	/**
   	 * 
   	 */
   	public function getConfigApplication(){
		if(isset(self::$a_Configs['application']))
			return self::$a_Configs['application'];
   		
   		$cfg = array();
		require BIN_APPLICATION.CONFIG_DIR.'Application.conf';
		self::$a_Configs['application'] = $cfg;
		return self::$a_Configs['application'];
   	}
   	
   	/**
   	 * 
   	 */
   	public function getConfigBootstrapper($bootstrapper = 'Bootstrapper'){
		if(isset(self::$a_Configs['Bootstrapper'][$bootstrapper]))
			return self::$a_Configs['Bootstrapper'][$bootstrapper];
   		
   		$cfg = array();
		require BIN_APPLICATION.CONFIG_DIR.BOOTSTRAPPERS_DIR.$bootstrapper.'.conf';
		self::$a_Configs['Bootstrapper'][$bootstrapper] = $cfg;
		return self::$a_Configs['Bootstrapper'][$bootstrapper];
   	}
   	
   	/**
   	 * 
   	 */
   	public function getConfigController($controller = 'Controller'){
		if(isset(self::$a_Configs['Controller'][$controller]))
			return self::$a_Configs['Controller'][$controller];
   		
   		$cfg = array();
		require BIN_APPLICATION.CONFIG_DIR.CONTROLLERS_DIR.$controller.'.conf';
		self::$a_Configs['Controller'][$controller] = $cfg;
		return self::$a_Configs['Controller'][$controller];
   	}
   	
   	/**
   	 * 
   	 */
   	public function getConfigModule($module = 'Module'){
		if(isset(self::$a_Configs['Module'][$module]))
			return self::$a_Configs['Module'][$module];
   		
   		$cfg = array();
   		require BIN_APPLICATION.CONFIG_DIR.MODULES_DIR.$module.'.conf';
		self::$a_Configs['Module'][$module] = $cfg;
		return self::$a_Configs['Module'][$module];   	   	
   	}
   	
   	/**
   	 * 
   	 */
   	public function getConfigLibrary($library = 'Library'){
		if(isset(self::$a_Configs['Library'][$library]))
			return self::$a_Configs['Library'][$library];
   		
   		$cfg = array();
   		require BIN_APPLICATION.CONFIG_DIR.LIBRARY_DIR.$library.'.conf';
		self::$a_Configs['Library'][$library] = $cfg;
		return self::$a_Configs['Library'][$library];   	   	
   	}
   	
   	/**
   	 * 
   	 */
   	public function getConfigCronjob($cronjob = 'Cronjob'){
		if(isset(self::$a_Configs['Cronjob'][$cronjob]))
			return self::$a_Configs['Cronjob'][$cronjob];
   		
   		$cfg = array();
   		require BIN_APPLICATION.CONFIG_DIR.CRONJOBS_DIR.$cronjob.'.conf';
		self::$a_Configs['Cronjob'][$cronjob] = $cfg;
		return self::$a_Configs['Cronjob'][$cronjob];   	   	
   	}   	
   	
   	
   	/**
   	 * 
   	 */
   	/*public function getConfigAdapter($adapter = 'Adapter'){
		if(isset(self::$a_Configs['Adapter'][$adapter]))
			return self::$a_Configs['Adapter'][$adapter];
   		
   		$cfg = array();
   		require BIN_APPLICATION.CONFIG_DIR.ADAPTERS_DIR.$adapter.'.conf';
		self::$a_Configs['Adapter'][$adapter] = $cfg;
		return self::$a_Configs['Adapter'][$adapter]; 	   	
   	} */  	
}



/*
class Config {
	
	public function readConfigFromDir($directory){
    	$directory .= preg_match("#/$#",$directory) ? '' : '/';
        $cfg = array();
	            
        $obj_FileSystem = Loader::loadLibrary('FileSystem');
	    $aFiles = $obj_FileSystem->getFiles($directory);

		if(is_array($aFiles) && (count($aFiles)>0)){
			foreach($aFiles as $file){
				if(preg_match("/(\.conf)$/i",$file))								
					require $directory.$file;
			}
		} else {
			return false;
		}
		return $cfg;
	}	
	
	public function readConfigFrameWork(){
		return $this->readConfigFromDir(CONFIG_DIR);		
	}

	public function readConfigApplication(){
		$application_dir = (APPLICATION_NAME) ? str_replace(".","/",APPLICATION_NAME) : '';
		$application_dir = !preg_match("#/$#", $application_dir) ? $application_dir.'/' : $application_dir;
		
		// hier per stap in de applicatie string config laden uit de dirs
		$applications = preg_split("/\./", APPLICATION_NAME);
		$a_Applications = array();

		foreach($applications as $application){
			$a_Applications[] = join("/".APPLICATIONS_DIR,$applications);
			array_pop($applications);
		}
		$a_Applications = array_reverse($a_Applications);
		$cfg = array();
		foreach($a_Applications as $application){
			$dir = APPLICATIONS_DIR.$application.'/'.CONFIG_DIR;
			$cfg = arrayMerge($cfg, $this->readConfigFromDir($dir));
		}

		$dir = APPLICATIONS_DIR.$application_dir.CONFIG_DIR;
		return arrayMerge($cfg, $this->readConfigFromDir($dir));		
	}

	public function readConfigModule(){
		$a_Mods = preg_split("/\./", MODULE_NAME);
		
		foreach($a_Mods as $module){
			$a_Modules[] = str_replace(" ","/", ucwords(join(" ",$a_Mods)));
			array_pop($a_Mods);
		}
		$a_Modules = array_reverse($a_Modules);				
		
		$application_dir = (APPLICATION_NAME) ? str_replace(".","/",APPLICATION_NAME) : '';
		$application_dir = !preg_match("#/$#", $application_dir) ? $application_dir.'/' : $application_dir;
		
		// hier per stap in de applicatie string config laden uit de dirs
		$applications = preg_split("/\./", APPLICATION_NAME);
		$a_Applications = array();

		foreach($applications as $application){
			$a_Applications[] = join("/".APPLICATIONS_DIR,$applications);
			array_pop($applications);
		}
		$a_Applications = array_reverse($a_Applications);
		$cfg = array();
		
				
		foreach($a_Modules as $module){
			$dir = MODULES_DIR.ucfirst($module).'/';
			$cfg = arrayMerge($cfg, $this->readConfigFromDir($dir));
			foreach($a_Applications as $application){
				$dir = APPLICATIONS_DIR.$application.'/'.MODULES_DIR.ucfirst($module).'/';
				$cfg = arrayMerge($cfg, $this->readConfigFromDir($dir));
			}
		}
		return $cfg;
	}	
	
	public function readConfigAdapter($adapter){
		$a_Adaps = preg_split("/\./", $adapter);
		
		
		foreach($a_Adaps as $module){
			$a_Adapters[] = str_replace(" ","/", ucwords(join(" ",$a_Adaps)));
			array_pop($a_Adaps);
		}
		$a_Adapters = array_reverse($a_Adapters);			
		
		$application_dir = (APPLICATION_NAME) ? str_replace(".","/",APPLICATION_NAME) : '';
		$application_dir = !preg_match("#/$#", $application_dir) ? $application_dir.'/' : $application_dir;
		
		// hier per stap in de applicatie string config laden uit de dirs
		$applications = preg_split("/\./", APPLICATION_NAME);
		$a_Applications = array();

		foreach($applications as $application){
			$a_Applications[] = join("/".APPLICATIONS_DIR,$applications);
			array_pop($applications);
		}
		$a_Applications = array_reverse($a_Applications);
		$cfg = array();
		
				
		foreach($a_Adapters as $adapter){
			$dir = ADAPTERS_DIR.ucfirst($adapter).'/';
			$cfg = arrayMerge($cfg, $this->readConfigFromDir($dir));
			foreach($a_Applications as $application){
				$dir = APPLICATIONS_DIR.$application.'/'.ADAPTERS_DIR.ucfirst($adapter).'/';
				$cfg = arrayMerge($cfg, $this->readConfigFromDir($dir));
			}
		}
		return $cfg;			
	}	
}*/