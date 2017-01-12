<?php
class Install {
	
	protected $a_Applications;

	private $cfg;
	protected $options = array();
	
	/**
	 * @desc	deze functie gaat de nodige configuratie laden
	 */
	public function __construct($options = false){
		require_once LIBRARY_DIR.'Loader/clsLoader.php';
		$cfg = array();		
		require 'Install.conf';
		$this->cfg = $cfg;

		if($options){
			$a_Options = explode(':', $options);	
			foreach($a_Options as $option){
				list($key, $value) = strpos('=', $option) ? explode('=', $option) : array($option, true);
				$this->options[$key] = $value;
			} 
		}
		
		print __METHOD__;
	}
	
	public function createconfigs(){
		$this->createincludes();
		
		require_once LIBRARY_DIR.'FileSystem/clsFileSystem.php';
		$obj_FileSystem = FileSystem::singleton();
		$obj_FileSystem->setCache(false);		
				
		# load the framework config
		$dir = ROOTFRAMEWORK.CONFIG_DIR;
		$cfg = (is_file($dir.'Framework.conf')) ? file_get_contents($dir.'Framework.conf') : '';
		
		if($obj_FileSystem->writeFile(BIN_APPLICATION.CONFIG_DIR, 'Framework.conf', $cfg, 'overwrite'))
			print "[SUCCESS] Config file created '".BIN_APPLICATION.CONFIG_DIR."Framework.conf'\n";

		# load application config
		/*
		 * a_ApplicationsArray is een array als volgt: (vb: example.test)
		 * 1 => dir => '', app => false
		 * 2 => dir => example/, app => example
		 * 3 => dir => example/Applications/test/, app => example_test
		 */	
		$a_Applications = $this->getApplicationArray();
		foreach($a_Applications as $application){
			if($application['app']){
				$dir = ROOTFRAMEWORK.$application['dir'].CONFIG_DIR;				
				$cfg .= (is_file($dir.'Application.conf')) ? preg_replace("/^<\?php/","", file_get_contents($dir.'Application.conf')) : '';	
			}
		}
		if($obj_FileSystem->writeFile(BIN_APPLICATION.CONFIG_DIR, 'Application.conf', $cfg, 'overwrite'))
			print "[SUCCESS] Config file created '".BIN_APPLICATION.CONFIG_DIR."Application.conf'\n";
					
		#maak de config files voor de modules etc
		$types[] = 'Module';
		$types[] = 'Controller';
		$types[] = 'Cronjob';
		$types[] = 'Library';
		
		foreach($types as $type)
			$this->loadConfigFor($type, $cfg);		
	}
	
	protected function loadConfigFor($type, $baseCfg){
		require_once LIBRARY_DIR.'FileSystem/clsFileSystem.php';
		$obj_FileSystem = FileSystem::singleton();
		$obj_FileSystem->setCache(false);		
		
		$directory['Bootstrapper'] = BOOTSTRAPPERS_DIR;
		$directory['Module'] = MODULES_DIR;
		$directory['Library'] = LIBRARY_DIR;
		$directory['Controller'] = CONTROLLERS_DIR;
		$directory['Cronjob'] = CRONJOBS_DIR;

		$a_Applications = $this->getApplicationArray();
		
		$cfg = $baseCfg;
		# laadt de basis module config
		foreach($a_Applications as $application){
			if($application['app']){
				$dir = ROOTFRAMEWORK.$application['dir'].$directory[$type];
				$cfg .= (is_file($dir.$type.'.conf')) ? preg_replace("/^<\?php/","", file_get_contents($dir.$type.'.conf')) : '';
			}
		}		
		if($obj_FileSystem->writeFile(BIN_APPLICATION.CONFIG_DIR.$directory[$type], $type.'.conf', $cfg, 'overwrite'))
			print "[SUCCESS] Config file created '".BIN_APPLICATION.CONFIG_DIR.$directory[$type].$type.".conf'\n";		
		
		# laad voor elke module de config	
		$a_Modules = array();
		foreach($a_Applications as $application){
			$modules = $this->getPackages($application['dir'].$directory[$type]);
			if($modules)
				foreach($modules as $module)
					if(!in_array($module, $a_Modules))
						$a_Modules[] = $module;			
		}
		
		$includes = array();
		require BIN_APPLICATION.'includes.conf';
		$includes = array_reverse($includes);
		foreach($a_Modules as $module){
			$dir = BIN_APPLICATION.CONFIG_DIR.$directory[$type];
			$created = $this->makeDir($dir);			
			if($created === true)
				print "[SUCCESS] Dir created :: '$dir'\n";
			elseif($created === false)				 
				print "[ERROR] Unable to create dir '$dir'\n";		

			$cfg = $baseCfg;
			foreach($includes[$type][$module] as $include){
				$a_Class = explode("_", $include['class']);
				$configFile = $a_Class[count($a_Class)-1];
				$cfg .= (is_file($include['directory'].$configFile.'.conf')) ? preg_replace("/^<\?php/","", file_get_contents($include['directory'].$configFile.'.conf')) : '';
			}	
			if($obj_FileSystem->writeFile(BIN_APPLICATION.CONFIG_DIR.$directory[$type], $module.'.conf', $cfg, 'overwrite'))
				print "[SUCCESS] Config file created '".BIN_APPLICATION.CONFIG_DIR.$directory[$type].$module.".conf'\n";			
		}		
	}
	
	public function createincludes(){
		#1: Get alle shit
		$a_Includes['Bootstrapper'] = self::getIncludeUrlsFor('Bootstrapper');
		$a_Includes['Controller'] = self::getIncludeUrlsFor('Controller');
		$a_Includes['Module'] = self::getIncludeUrlsFor('Module');
		$a_Includes['Library'] = self::getIncludeUrlsFor('Library');
		$a_Includes['Template'] = self::getIncludeUrlsFor('Template');
		$a_Includes['Cronjob'] = self::getIncludeUrlsFor('Cronjob');
		
		$src = '<?php'."\n";
		foreach($a_Includes as $type => $a_Modules)
			foreach($a_Modules as $module => $a_Urls)		
				foreach($a_Urls as $url){
					$src .= '$'."includes['$type']['$module'][] = array(";
					$src .= "'directory' => '".$url['directory']."', ";
					$src .= "'filename' => '".$url['filename']."'";
					$src .= ($type != 'Template') ? ", 'class' => '".$url['class']."'" : '';
					$src .= ");"."\n";				
				}
						
		require_once LIBRARY_DIR.'FileSystem/clsFileSystem.php';
		$obj_FileSystem = FileSystem::singleton();
		$obj_FileSystem->setCache(false);				
		if($obj_FileSystem->writeFile(BIN_APPLICATION, 'includes.conf', $src, 'overwrite'))
			print "[SUCCESS] Includes file created\n";	
	}
	
	/**
	 * @name	create
	 * @param 	string $name
	 * @desc	creates the module (and base if needed)
	 */	
	public function create($name = false){
		$name = $name ? $name : $this->moduleKey;
		$name = str_replace(" ",".",ucwords(str_replace("."," ", $name)));		
		
		if(!$this->exists($name, APPLICATION_NAME)){
			$a_Modules = explode(".", $name);
			$moduleToCreate = $a_Modules[count($a_Modules)-1];
					
			$class = ($this->modulePrefix) ? $this->modulePrefix.'_' : '';
			$class .= $this->getModulePrefix(APPLICATION_NAME).$moduleToCreate;
			$extend = $this->getModuleExtend($name, APPLICATION_NAME);
			$filename = 'cls'.$moduleToCreate.'.php'; 
			$dir = ROOTFRAMEWORK.APPLICATIONS_DIR.APPLICATION_DIR.$this->baseDir;
			$dir .= ($moduleToCreate != $this->moduleKey) ? str_replace(".","/", $name).'/' : '';
			
			$created = $this->makeDir($dir); 
			if($created === true)
				print "[SUCCESS] Dir created '$dir'\n";
			elseif($created === false) 
				print "[ERROR] Unable to create dir '$dir'\n";					
			
			$tpl = file_get_contents(ROOTFRAMEWORK.INSTALLJOBS_DIR.$this->moduleKey.'/'.$this->fileTpl);
			$tpl = str_replace('#class#', $class, $tpl);
			$tpl = str_replace('#extend#', $extend, $tpl);
			$tpl = str_replace('#date#', date('Y-m-d H:i:s'), $tpl);
						
			if(!is_file($dir.$filename)){
				require_once LIBRARY_DIR.'FileSystem/clsFileSystem.php';
				$obj_FileSystem = FileSystem::singleton();
				$obj_FileSystem->setCache(false);		
				
				if($obj_FileSystem->writeFile($dir, $filename, $tpl, 'overwrite'))
					print "[SUCCESS] File '$filename' created\n";
				
				// regenerate includes
				$this->createincludes();
				
			} else
				print "[NOTICE] File already exists '$filename'\n";
				
			// what about the config?
			$this->createConfigFile($name, $moduleToCreate);			
		} else
			print "[NOTICE] $this->moduleKey already exists '$name'\n";	
	}	
	
	/**
	 * @param string $module_name
	 */
	protected function createConfigFile($module_name, $moduleToCreate){
		$dir = ROOTFRAMEWORK.APPLICATIONS_DIR.APPLICATION_DIR.$this->baseDir;
		$dir .= ($module_name != $this->moduleKey) ? str_replace(".","/", $module_name).'/' : '';
					
		if($this->fileTplConfig){
			$confFileName = $moduleToCreate.'.conf';		
			$tpl = file_get_contents(ROOTFRAMEWORK.INSTALLJOBS_DIR.$this->moduleKey.'/'.$this->fileTplConfig);
			$tpl = str_replace('#name#',$module_name, $tpl);
			if(!is_file($dir.$confFileName)){
				require_once LIBRARY_DIR.'FileSystem/clsFileSystem.php';
				$obj_FileSystem = FileSystem::singleton();	
				$obj_FileSystem->setCache(false);					
				if($obj_FileSystem->writeFile($dir, $confFileName, $tpl, 'overwrite'))
					print "[SUCCESS] File created '$dir"."$confFileName'\n";					
			} else
				print "[NOTICE] File already exists '$dir"."$confFileName'\n"; 			
		}

		// regenerate the cache for the configs
		$this->createconfigs();
	}

	
	/**
	 * @desc	Deze functie construeerd de array die we in de includes file willen zetten (gespecificeerd op type (controller, module of adapter))
	 * @param 	varchar $type
	 */
	private function getIncludeUrlsFor($type){
		$a_Includes[APPLICATION_NAME] = array();
		
		$dir['Bootstrapper'] = BOOTSTRAPPERS_DIR;
		$dir['Controller'] = CONTROLLERS_DIR;
		$dir['Module'] = MODULES_DIR;
		$dir['Library'] = LIBRARY_DIR;
		$dir['Template'] = TEMPLATES_DIR;
		$dir['Cronjob'] = CRONJOBS_DIR;
		
		$a_Packages = array();
		$url = array();
		
		/*
		 * a_ApplicationsArray is een array als volgt: (vb: example.test)
		 * 1 => dir => '', app => false
		 * 2 => dir => example/, app => example
		 * 3 => dir => example/Applications/test/, app => example_test
		 */	
		$a_Applications = $this->getApplicationArray();
		foreach($a_Applications as $app){
			$packages = ($type == 'Template') ? $this->getTemplates($app['dir'].$dir[$type]) : $this->getPackages($app['dir'].$dir[$type]);
			if($packages)
				foreach($packages as $package)
					if(!in_array($package, $a_Packages))
						$a_Packages[] = $package;			
		}
		
		// we voegen de package $type toe. Dit zorgt ervoor dat we ook ten alle tijde de basis module kunnen inladen
		// Dus 'Controller' is als het ware een Controller. Die bestaat niet, dus zal alleen de basis worden ingeladen.
		if($type != 'Template')
			$a_Packages[] = $type;
		
		/*
		 * $a_Packages is de array waarin we alle beschikbare modules hebben voor de applicatie
		 */		
		foreach($a_Packages as $package)
			$url[$package] = $this->getIncludeUrl($package, $type);
			
		return $url;		
	}	
	
	
	/**
	 * 
	 * @param string $package
	 * @param string $type
	 */
	private function getIncludeUrl($package, $type = 'Library'){
		if($type == 'Template')
			return $this->getIncludeUrlTemplate($package);
			
		$dir['Bootstrapper'] = BOOTSTRAPPERS_DIR;
		$dir['Controller'] = CONTROLLERS_DIR;
		$dir['Module'] = MODULES_DIR;
		$dir['Library'] = LIBRARY_DIR;
		$dir['Cronjob'] = CRONJOBS_DIR;
				
		$prefix['Bootstrapper'] = 'b';
		$prefix['Controller'] = 'c';
		$prefix['Module'] = 'm';
		$prefix['Library'] = false;
		$prefix['Cronjob'] = 'cron';
			
		$a_Includes = array();
		$a_Packages = array();
		
		/*
		 * $typedir is bijvoorbeeld MODULES_DIR
		 * 
		 * a_Packages is een array als volgt: (vb: Template.Php)
		 * 1 = dir => Template/, class => Template
		 * 2 = dir => Template/Php/, class = Php
		 * 
		 * We gaan eerst per applicatie de 'Template' inladen
		 * Vervolgens per applicatie de 'Template/Php' inladen.
		 */
		$packages = explode('.', $package);
		$d = '';
		foreach($packages as $pack){
			$d = $d.$pack.'/';
			$a_Packages[] = array('dir' => $d, 'class' => $pack);
		}
		
		/*
		 * We gaan eerst Template inladen:
		 * a_ApplicationsArray is een array als volgt: (vb: example.test)
		 * 1 => dir => '', app => false
		 * 2 => dir => example/, app => example
		 * 3 => dir => example/Applications/test/, app => example_test
		 */	
		$a_Applications = $this->getApplicationArray();
				
		/*
		 * De basis package uit de applicatie laden
		 */
		foreach($a_Applications as $app){
			$filename = 'cls'.$type.'.php';
			$directory = $app['dir'].$dir[$type];			
			if(is_file(ROOTFRAMEWORK.$directory.$filename)){
				$class = ($prefix[$type]) ? $prefix[$type].'_' : '';
				$class .= ($app['app']) ? $app['app'].'_'.$type : $type;
				
				$a['directory'] = ROOTFRAMEWORK.$directory;
				$a['filename'] = $filename;				
				$a['class'] = $class;
				
				$a_Includes[] = $a;			
			}
		}		
		
		foreach($a_Packages as $package){																		
			/*
			 * De module uit de applicatie laden
			 */
			foreach($a_Applications as $app){
				$filename = 'cls'.$package['class'].'.php';
				$directory = $app['dir'].$dir[$type].$package['dir'];
				if(is_file(ROOTFRAMEWORK.$directory.$filename)){	
					$class = ($prefix[$type]) ? $prefix[$type].'_' : '';
					$class .= ($app['app']) ? $app['app'].'_'.$package['class'] : $package['class'];
				
					$a_Includes[] = array('directory' => ROOTFRAMEWORK.$directory,'filename' => $filename, 'class' => $class);					
				}
			}
		}
		return $a_Includes;	
	}
	
	private function getIncludeUrlTemplate($package){
		$a_Includes = array();
		$a_Packages = array();
		
		/*
		 * $dir = TEMPLATES_DIR
		 * $package = 'includes.blocks.header'
		 * 
		 * $dir wordt TEMPLATES_DIR.includes/blocks/
		 * $filename wordt header.tpl
		 */
		$package = preg_replace("/\.tpl$/", "", $package);		
		$packages = explode('/', $package);
		$filename = array_pop($packages).'.tpl';
		$dir = TEMPLATES_DIR.join('/', $packages);
		$dir .= preg_match("/\/$/", $dir) ? '' : '/';
		
		/*
		 * We gaan eerst in de huidige applicatie kijken.
		 * a_ApplicationsArray is een array als volgt: (vb: example.test)
		 * 1 => dir => '', app => false
		 * 2 => dir => example/, app => example
		 * 3 => dir => example/Applications/test/, app => example_test
		 */	
		$a_Applications = $this->getApplicationArray();
		$a_Applications = array_reverse($a_Applications);
				
		/*
		 * De template uit de applicatie laden
		 */
		foreach($a_Applications as $app){
			$directory = $app['dir'].$dir;			
			if(is_file(ROOTFRAMEWORK.$directory.$filename)){				
				$a['directory'] = ROOTFRAMEWORK.$directory;
				$a['filename'] = $filename;								
				$a_Includes[] = $a;
				break;			
			}
		}		
		return $a_Includes;	
	}
	
	
	/**
	 * @desc	Deze functie zoekt naar directories in de dir
	 * @param 	unknown_type $dir
	 */
	private function getPackages($dir){
		require_once LIBRARY_DIR.'FileSystem/clsFileSystem.php';
		$obj_FileSystem = FileSystem::singleton();
		$obj_FileSystem->setCache(false);				
		$a_Packages = $obj_FileSystem->getDirs($dir);
		$key = array_search(CONFIG_DIR, $a_Packages);
		if($key && array_key_exists($key, $a_Packages))
			unset($a_Packages[$key]);
					
		$a_ToReturn = array();	
		foreach($a_Packages as $package){
			if(!in_array($package, $this->cfg['skippackages'])){
				$a_ToReturn[] = $package;
				$a_Subpackages = $this->getPackages($dir.$package.'/');
				if($a_Subpackages)
					foreach($a_Subpackages as $subpackage)
						$a_ToReturn[] = $package.'.'.$subpackage;
			}	
		}
		return (count($a_ToReturn)>0) ? $a_ToReturn : false; 	
	}	
	
	
	/**
	 * @name	getTemplates
	 * @desc	deze functie zoekt naar files in subdirectories
	 */
	private function getTemplates($dir){
		require_once LIBRARY_DIR.'FileSystem/clsFileSystem.php';
		$obj_FileSystem = FileSystem::singleton();
		$obj_FileSystem->setCache(false);		
		$a_Templates = $obj_FileSystem->getFiles($dir);		
		
		$a_ToReturn = array();
		foreach($a_Templates as $template)
			$a_ToReturn[] = $template;
		
		$a_SubTemplates = $obj_FileSystem->getDirs($dir);		
		foreach($a_SubTemplates as $subTemplate){
			$a_Templates = $this->getTemplates($dir.$subTemplate.'/');
			if($a_Templates)
				foreach($a_Templates as $template)
					$a_ToReturn[] = $subTemplate.'/'.$template;
		}
		return (count($a_ToReturn)>0) ? $a_ToReturn : false;				
	}
	
	
	/**
	 * @desc	return de application array
	 */
	protected function getApplicationArray($application_name = false){		
		$application_name = $application_name ? $application_name : (defined('APPLICATION_NAME') ? APPLICATION_NAME : 'framework'); 
		
		if(isset($this->a_Applications[$application_name]))
			return $this->a_Applications[$application_name];
		
		if($application_name != 'framework'){
			$applications = preg_split("/\./", $application_name);		
			$this->a_Applications[$application_name] = array();
			
			foreach($applications as $application){
				$this->a_Applications[$application_name][] = array('dir' => APPLICATIONS_DIR.join('/'.APPLICATIONS_DIR,$applications).'/', 'app' => join('_',$applications));
				array_pop($applications);
			}			
		}
		$this->a_Applications[$application_name][] = array('dir'=> '', 'app' => false);
		$this->a_Applications[$application_name] = array_reverse($this->a_Applications[$application_name]);
		return $this->a_Applications[$application_name];
	}	
	
	protected function getModulePrefix($application_name){
		$a_Applications = explode('.', $application_name);
		return join('_', $a_Applications).'_';	
	}
	
	
	protected function getModuleExtend($module_name, $application_name){
		$bin_application = ROOTFRAMEWORK.BIN_DIR.$application_name.'/';
		$includes = array();
		if(!is_file($bin_application.'includes.conf'))
			die ('[ERROR] at '.__METHOD__." :: includes.conf file not found in '$bin_application'\n");
			
		require $bin_application.'includes.conf';
				
		$a = explode(".", $module_name);		

		$a_M = array();
		$last = false;
		foreach($a as $m){
			$var = $last ? $last.'.'.$m : $m;
			$last = $var;
			$a_M[] = $var;
		}
		$a_Modules = array_reverse($a_M);
		foreach($a_Modules as $module){
			print $module." - ".$this->moduleKey." - ";
			if(array_key_exists($module, $includes[$this->moduleKey]))
				return $includes[$this->moduleKey][$module][count($includes[$this->moduleKey][$module])-1]['class'];
		}
		return $includes[$this->moduleKey][$this->moduleKey][count($includes[$this->moduleKey][$this->moduleKey])-1]['class'];
	}	
	
	protected function makeDir($dir){
		require_once LIBRARY_DIR.'FileSystem/clsFileSystem.php';
		$obj_FileSystem = FileSystem::singleton();
		$obj_FileSystem->setCache(false);				

		return $obj_FileSystem->makeDir($dir);		
	}
	
	/**
	 * @name	exists
	 * @param 	string $module_name
	 * @param 	string $application_name
	 */
	protected function exists($module_name, $application_name){
		$bin_application = ROOTFRAMEWORK.BIN_DIR.$application_name.'/';
		
		$includes = array();
		if(!is_file($bin_application.'includes.conf'))
			die ('[ERROR] at '.__METHOD__." :: includes.conf file not found in '$bin_application'\n");
		
		require $bin_application.'includes.conf';
		if(!array_key_exists($module_name, $includes[$this->moduleKey]))
			return false;
		
		# search for class
		$prefix = $this->getModulePrefix($application_name);
		$class = $this->modulePrefix.'_'.$prefix.$module_name;
		
		foreach($includes[$this->moduleKey][$module_name] as $include)
			if($include['class'] == $class)
				return true;
		return false;
	}	
}