<?php
/**
 * @name	LoaderException
 * @author 	wiegerjelsma
 */
class LoaderException extends ErrorHandler {}

/**
 * @name	Loader
 * @author 	wiegerjelsma
 * @desc	De loader maakt per applicatie een includes.conf file aan.
 * 			In die file staan alle urls per package die geinclude moeten worden.
 			
 			VB: (app = example.een)
  			$includes['modules']['Distributor'][] = array('url' => 'Modules/Module/clsModule.php', 'class' => 'm_Module');
			$includes['modules']['Distributor'][] = array('url' => 'Applications/example/Modules/Module/clsModule.php', 'class' => 'm_example_Module');
			$includes['modules']['Distributor'][] = array('url' => 'Applications/example/Applications/een/Modules/Distributor/clsDistributor.php', 'class' => 'm_example_een_Distributor');

 */
class Loader {
		
	private static $cfg;
	
	/**
	 * @desc	In deze array cachen we de includes. Dit bespaart ons de include van de file.
	 */
	private static $a_Includes = false;	

	/**
	 * @desc	In deze array houden we de application array vast.
	 * 			Aan de hand van die array wandelen we van boven naar beneden om files te includen.
	 */
	private static $a_Applications = false;	
	
	/**
	 * @desc	holds the required files. 
	 * @var 	array
 	 */
	private static $a_RequiredFiles = array();
	
	/**
	 * @desc	Holds the loaded modules / adapters / library
	 */
	protected static $a_Loaded = array();
	
	
	/**
	 * @name	pushConfig
	 * @param 	array $cfg
	 * @desc	de application config (incl framework cfg) wordt gepushed vanuit bootstrap
	 */	
	public static function pushConfig($cfg){
		self::$cfg = $cfg;
	}	
	
	
	/**
	 * @desc	Deze functie retourneert de array met includes.
	 * 			Als de include file bestaat dan wordt deze geinclude (als deze niet is gecached) en de arry terug gegeven.
	 * 			Bestaat de file niet dan wordt deze gecreerd.
	 */
	public static function getIncludes($type = false){
		if(is_file(BIN_APPLICATION.'includes.conf')){
			require BIN_APPLICATION.'includes.conf';
			self::$a_Includes = $includes;
			return $type ? self::$a_Includes[$type] : self::$a_Includes;
		}
	}
	
	/**
	 * @desc	returns the modules as an array
	 */
	public static function getModules($type = 'Library'){
		if(!self::$a_Includes)
			self::getIncludes();
		
		return array_keys(self::$a_Includes[$type]);		
	}

	
	/**
	 * @param str $dir
	 * @param str $filename
	 */
	private static function requireFile($dir, $filename){
		if(isset(self::$a_RequiredFiles[$dir.$filename]))
			return self::$a_RequiredFiles[$dir.$filename];
			
		if(is_file(ROOTFRAMEWORK.$dir.$filename))
			self::$a_RequiredFiles[$dir.$filename] = (require_once ROOTFRAMEWORK.$dir.$filename) ? true : false;
		else		
			self::$a_RequiredFiles[$dir.$filename] = false;
		return self::$a_RequiredFiles[$dir.$filename];			
	}	

	
	/**
	 *	@desc	laadt een object in 
	 */
	public static function load($package, $type = 'Library', $newinstance = false){
		if(!self::$a_Includes)
			self::getIncludes();

		if(isset(self::$a_Loaded[$type.$package])){
			if(self::$a_Loaded[$type.$package]['singleton'] && !$newinstance)	
				return self::$a_Loaded[$type.$package]['object'];
			else
				return new self::$a_Loaded[$type.$package]['class'];
		}
		if(!isset(self::$a_Includes[$type][$package]))
			return false;
		$count = count(self::$a_Includes[$type][$package]);
		for($i=0; $i<$count; $i++){
			require_once self::$a_Includes[$type][$package][$i]['directory'].self::$a_Includes[$type][$package][$i]['filename'];
			
			if($i==($count-1)){
				$class = self::$a_Includes[$type][$package][$i]['class'];
				if(method_exists($class, 'singleton') && !$newinstance){
					$obj = call_user_func($class.'::singleton', $class);
					self::$a_Loaded[$type.$package] = array('object' => $obj, 'singleton' => true, 'class' => $class);					
				} else {					 
					$obj = new $class;
					self::$a_Loaded[$type.$package] = array('class' => $class, 'singleton' => false);						
				}				
				return $obj;											
			}
		}			
		return false;
	}
	
	
	/**
	 *	@desc	laadt een Controller object in middels deze functie 
	 */	
	public static function loadController($controller = 'Controller'){
		$obj_Controller = self::load($controller, 'Controller');
		if(!$obj_Controller)
			throw new LoaderException("Unable to load controller '$controller'");
		$cfg = self::loadConfig($controller, 'Controller');
		$obj_Controller->pushConfig($cfg, $controller);
		
//		if(method_exists($obj_Controller, 'init'))
//			$obj_Controller->init();

// We callen de init function nu vanuit de bootstrapper..
// Dit omdat we dan alle defines kunnen gebruiken in die init functie.
					
		return $obj_Controller;		
	}
	
	
	/**
	 *	@desc	laadt een Boostrapper object in middels deze functie 
	 */	
	public static function loadBootstrapper($bootstrapper = 'Bootstrapper'){
		$obj_Bootstrapper= self::load($bootstrapper, 'Bootstrapper');
		if(!$obj_Bootstrapper)
			throw new LoaderException("Unable to load bootstrapper '$bootstrapper'");
		$cfg = self::loadConfig(false, 'Application');
		$obj_Bootstrapper->pushConfig($cfg, $bootstrapper);
		
		if(method_exists($obj_Bootstrapper, 'init'))
			$obj_Bootstrapper->init();
					
		return $obj_Bootstrapper;		
	}
	
	
	
	/**
	 *	@desc	laadt een Module object in middels deze functie 
	 */
	public static function loadModule($module){
		$obj_Module = self::load($module, 'Module');
		if(!$obj_Module)
			throw new LoaderException("Unable to load module '$module'");
		
		$cfg = self::loadConfig($module, 'Module');
		$obj_Module->pushConfig($cfg, $module);
		
		if(method_exists($obj_Module, 'init'))
			$obj_Module->init();		
		
		return $obj_Module;
	}
	

	/**
	 *	@desc	laadt een Module object in middels deze functie 
	 */
	public static function loadLibrary($library){
		$obj_Library = self::load($library, 'Library');
		if(!$obj_Library)
			throw new LoaderException("Unable to load library '$library'");
		
		$cfg = self::loadConfig($library, 'Library');
		$obj_Library->pushConfig($cfg, $library);
		
		if(method_exists($obj_Library, 'init'))
			$obj_Library->init();		
		
		return $obj_Library;
	}
	
	
	/**
	 *	@desc	laadt een Module object in middels deze functie 
	 */
	public static function loadCronjob($cronjob){
		$obj_Cronjob = self::load($cronjob, 'Cronjob');
		if(!$obj_Cronjob)
			throw new LoaderException("Unable to load cronjob '$cronjob'");
		
		$cfg = self::loadConfig($cronjob, 'Cronjob');
		$obj_Cronjob->pushConfig($cfg, $cronjob);
		
		if(method_exists($obj_Cronjob, 'init'))
			$obj_Cronjob->init();		
		
		return $obj_Cronjob;
	}
	
	
	/**
	 *	@desc	laadt een Module object in middels deze functie 
	 */
	public static function getNewInstance($class, $type){
		if(!self::$a_Includes)
			self::getIncludes();
			
		foreach(self::$a_Includes[$type] as $package => $a_Includes)
			foreach($a_Includes as $a_Details)
				if($a_Details['class'] == $class){
					$obj = self::load($package, $type, true);
					if(!$obj)
						throw new LoaderException("Unable to load $type '$package'");
											
					$cfg = self::loadConfig($package, $type);
					$obj->pushConfig($cfg, $package);
		
					if(method_exists($obj, 'init'))
						$obj->init();		
		
					return $obj;				
				}	
	}
	
	
	/**
	 *	@desc	laadt een Template object in middels deze functie 
	 */
	public static function loadTemplate($template){
		if(!self::$a_Includes)
			self::getIncludes();
					
		if(defined('LANGUAGE_NAME')){
			$tplname = preg_replace("/\.tpl$/", "", $template);
			$directory = explode('/', $tplname);
			$tplName = LANGUAGE_NAME.'.'.array_pop($directory).'.tpl';
			$lnTpl = (count($directory) >= 1) ? join($directory, '/').'/'.$tplName : $tplName;
			if(isset(self::$a_Includes['Template'][$lnTpl]))
				return file_get_contents(self::$a_Includes['Template'][$lnTpl][0]['directory'].self::$a_Includes['Template'][$lnTpl][0]['filename']);
		}				
		
		if(!isset(self::$a_Includes['Template'][$template]))
			throw new LoaderException("Unable to load template '$template'");
			
		return file_get_contents(self::$a_Includes['Template'][$template][0]['directory'].self::$a_Includes['Template'][$template][0]['filename']);	
	}	
	
	
	/**
	 * @desc	laad de config
	 * @param	type (framework, application, module)
	 */
	private static function loadConfig($package, $type = 'Module'){
		$obj_Config = Config::singleton();

		$fn['Framework'] = 'getConfigFramework';
		$fn['Application'] = 'getConfigApplication';
		$fn['Module'] = 'getConfigModule';
		//$fn['Adapter'] = 'getConfigAdapter';
		$fn['Controller'] = 'getConfigController';
		$fn['Bootstrapper'] = 'getConfigBootstrapper';				
		$fn['Library'] = 'getConfigLibrary';
		$fn['Cronjob'] = 'getConfigCronjob';						
				
		return $package ? $obj_Config->{$fn[$type]}($package) : $obj_Config->{$fn[$type]}();
	}
}