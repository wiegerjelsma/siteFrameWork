<?php
/**
 * @name	Application (install script)
 * @author 	wiegerjelsma
 *
 */
class Application extends Install {
	
	protected $fileTplConfig = 'application.conf.filetpl';
		
	/**
	 * @desc	creates the directories and files for an new application
	 * @param 	string $name
	 */
	public function create($name){		
		if(!$name)
			die("[ERROR] Missing argument 'p' : No application name given\n");
		if(defined('APPLICATION_NAME'))
			die("[ERROR] Argument 'a' should not be used with this command : -p sets the application name\n\nCreate an subapplication this way:\n<path to php> <rootframework>install.php -c application:create -p app.subapp\n\n");
		
		# set the right app name and make the defines.
		# normaal hebben we de -a param, maar niet als we een applicatie gaan creeeren.
		$application_name = (defined('APPLICATION_NAME')) ? APPLICATION_NAME : $name; 
		$this->defineApplicationVars($name);
					
		$a_Apps = explode(".",$application_name);
		$appToCreate = array_pop($a_Apps);
		
		if(count($a_Apps) > 0){
			$inApp = join('.', $a_Apps);
			if(!$this->exists($inApp))
				die("[ERROR] Unable to create aplication '$appToCreate' :: Application '$inApp' doesn't exists\n");
		}		
		
		# create the dirs
		$this->createDirs();
		
		#create the config file for this application
		$this->createConfigFile();		
		
		// we hebben een include file nodig bij het creeeren van de files.
		$this->createincludes();		
		
		// creeer de bootstrappers, controllers en modules
		$this->createBaseModules();
		
		// create the basetemplates
		$this->createBaseTemplates();

		// we gaan de includes file updaten met de templates
		$this->createincludes();		
	}
		
	
	/**
	 * 
	 */
	private function createDirs(){
		$cfg = array();		
		require 'Application.conf';
		
		foreach($cfg['dirs'] as $dir){
			$created = $this->makeDir($dir);			
			if($created === true)
				print "[SUCCESS] Dir created :: '$dir'\n";
			elseif($created === false)				 
				print "[ERROR] Unable to create dir '$dir'\n";		
		}	
	}
	
	
	/**
	 * @name createFiles
	 */
	private function createBaseModules(){
		require ROOTFRAMEWORK.INSTALLJOBS_DIR.'Module/clsModule.php';
		$obj_Module = new Module();
		$obj_Module->create('Module');

		require ROOTFRAMEWORK.INSTALLJOBS_DIR.'Controller/clsController.php';
		$obj_Module = new Controller();
		$obj_Module->create('Controller');
		
		require ROOTFRAMEWORK.INSTALLJOBS_DIR.'Bootstrapper/clsBootstrapper.php';
		$obj_Module = new Bootstrapper();
		$obj_Module->create('Bootstrapper');
		
	}
	
	
	/**
	 * @name	createConfigFile
	 * @param 	string $application_name
	 */
	protected function createConfigFile(){		
		$dir = ROOTFRAMEWORK.APPLICATIONS_DIR.APPLICATION_DIR.CONFIG_DIR;
		
		$confFileName = 'Application.conf';		
		$ml = array_key_exists('multilingual', $this->options) ? $this->options['multilingual'] : 'false';
		$tpl = file_get_contents(ROOTFRAMEWORK.INSTALLJOBS_DIR.'Application/'.$this->fileTplConfig);
		$tpl = str_replace('#name#',APPLICATION_NAME, $tpl);
		$tpl = str_replace('#multilingual#',$ml, $tpl);
		
		if(!is_file($dir.$confFileName)){
			require_once LIBRARY_DIR.'FileSystem/clsFileSystem.php';
			$obj_FileSystem = FileSystem::singleton();	
			$obj_FileSystem->setCache(false);		
			if($obj_FileSystem->writeFile($dir, $confFileName, $tpl, 'overwrite'))
				print "[SUCCESS] File created :: '$confFileName'\n";					
		} else
			print "[NOTICE] File already exists :: '$confFileName'\n"; 								
	}	
	
	
	/**
	 * @name	createBaseTemplates
	 * @desc	creates a header, footer and an home.tpl
	 */	
	protected function createBaseTemplates(){
		require ROOTFRAMEWORK.INSTALLJOBS_DIR.'Template/clsTemplate.php';
		$obj_Module = new Template();
		$obj_Module->createBaseTemplates();
	}
		
	
	/**
	 * @param unknown_type $application_name
	 */
	private function defineApplicationVars($application_name){
		$application_dir = ($application_name) ? str_replace(".", "/".APPLICATIONS_DIR, $application_name) : false;
		$application_dir = (($application_dir) && !preg_match("#/$#", $application_dir)) ? $application_dir.'/' : $application_dir;
		define("APPLICATION_NAME", $application_name);
		define("APPLICATION_DIR", $application_dir);		

		define("INPUT_APPLICATION", ROOTFRAMEWORK.INPUT_DIR.APPLICATION_NAME.'/');
		define("OUTPUT_APPLICATION", ROOTFRAMEWORK.OUTPUT_DIR.APPLICATION_NAME.'/');	
		define("BIN_APPLICATION", ROOTFRAMEWORK.BIN_DIR.APPLICATION_NAME.'/');
		define("ROOT_APPLICATION", ROOTFRAMEWORK.APPLICATIONS_DIR.APPLICATION_DIR.ROOT_DIR);		
	}
	
	
	/**
	 * @desc	checks if the application exists
	 * @param 	string $application_name
	 */
	public function exists($application_name){
		$application_dir = ($application_name) ? str_replace(".", "/".APPLICATIONS_DIR, $application_name) : false;
		$application_dir = (($application_dir) && !preg_match("#/$#", $application_dir)) ? $application_dir.'/' : $application_dir;
		
		return is_dir(ROOTFRAMEWORK.APPLICATIONS_DIR.$application_dir);
	}
	
	
	/**
	 * @name	validate
	 * @desc	checks the available and writable directories
	 */
	public function validate(){}
	
	/**
	 * @name	update
	 * @desc	makes the includes and configs
	 */
	public function update(){
		$this->createincludes();
		$this->createconfigs();
	}
	
	
	/**
	 * @name	compress
	 */
	public function compress($param = false){
	
		require_once LIBRARY_DIR.'Config/clsConfig.php';
		require_once LIBRARY_DIR.'FileSystem/clsFileSystem.php';
		require 'Application.conf';
	
		$obj_Config = Config::singleton();		
		$cfgApp = $obj_Config->getConfigApplication();
			
		$obj_FileSystem = FileSystem::singleton();
		$obj_FileSystem->setCache(false);
		
		if($param)
			$a_Types = array($param);
		else
			$a_Types = array('js','css');				

		foreach($a_Types as $type){
			$source = '';
			// Get the framework type
			if(isset($cfgApp['compress'][$type]['framework']))
				foreach($cfgApp['compress'][$type]['framework'] as $file){
					if(preg_match('/^\./', $file) or !preg_match('/\.'.$type.'$/',$file))
						continue;
				
					if(!is_file(ROOTFRAMEWORK.ROOT_DIR.$type.'/'.$file))
						die("[ERROR] Unable to read $type '$file' from '".ROOT_DIR.$type.$file."' : No such file\n");
					
					print "[SUCCESS] Reading $type framework file '$file'\n";
					$source .= file_get_contents(ROOTFRAMEWORK.ROOT_DIR.$type.'/'.$file);
				}

			// get the application js
			if(isset($cfgApp['compress'][$type]['application']))
				foreach($cfgApp['compress'][$type]['application'] as $file){
					if(preg_match('/^\./', $file) or !preg_match('/\.'.$type.'$/',$file))
						continue;
				
					if(!is_file(ROOT_APPLICATION.$type.'/'.$file))
						die("[ERROR] Unable to read application $type file '$file' from '".ROOT_APPLICATION.ROOT_DIR.$type.'/'.$file."' : No such file or directory\n");
					
					print "[SUCCESS] Reading $type application file '$file'\n";
					$source .= file_get_contents(ROOT_APPLICATION.$type.'/'.$file);
				}
		
			// schrijf de source weg
			$source = str_replace("\r","\n",$source);
			
			if($obj_FileSystem->writeFile(ROOT_APPLICATION.$type.'/'.$cfgApp['compress']['dir'], $cfgApp['compress']['filename'].'.'.$type, $source, 'overwrite'))
				print "[SUCCESS] Writing $type minified file '".$cfgApp['compress']['dir'].$cfgApp['compress']['filename'].'.'.$type."'\n";
			else
				die("[ERROR] Writing $type minified file '".$cfgApp['compress']['dir'].$cfgApp['compress']['filename'].'.'.$type."'\n");
		}
		
		foreach($a_Types as $type){
			// ga dat java ding aanschreeuwen
			$outputfile = ROOT_APPLICATION.$type.'/'.$cfgApp['compress']['dir'].$cfgApp['compress']['filename'].'-'.$cfgApp['compress']['version'].'.min.'.$type;
			$inputfile = ROOT_APPLICATION.$type.'/'.$cfgApp['compress']['dir'].$cfgApp['compress']['filename'].'.'.$type;
						
			if(is_file($outputfile))
				unlink($outputfile);
			print "[COMPRESSING] ".$inputfile."'\n";						
			print "[WRITING] ".$outputfile."'\n";			
						
			$cmd = 'java -jar '.ROOTFRAMEWORK.INSTALLJOBS_DIR.'_yuiCompressor/build/yuicompressor-2.4.6.jar --charset utf-8 -o '.$outputfile.' '.$inputfile;
			print  shell_exec($cmd);					
		}
	}
}
































