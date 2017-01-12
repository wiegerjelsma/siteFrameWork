<?php
/**
 * php install.php -c <class>:<method> -p <params> -a <applicatie>
 * 
 * @desc Applicatie
 * # creeer een applicatie genaamd 'wellxone'
 * php install.php -c application:create -p wellxone
 * 
 * #creeer een applicatie genaamd 'cli' in de applicatie 'wellxone'
 * php install.php -c application:create -p wellxone.cli
 * 
 * #creeer een applicatie genaamd 'wellxone' met als optie 'multilingual' is true
 * php install.php -c application:create -p wellxone -o multilingual
 * 
 * @desc Controller
 * #creeer een controller genaamd 'Template' in de applicatie 'wellxone'
 * php install.php -c controller:create -p template -a wellxone
 * 
 * #creeer een controller genaamd 'Template.Php' in de applicatie 'wellxone'
 * php install.php -c controller:create -p template.php -a wellxone
 * 
 * @desc Template
 * creeer een tpl in een subdir in applicatie 'wellxone'
 * php install.php -c template:create -p <tpl subdir>/<tpl> -a wellxone
 * 
 * 
 * @desc Module
 * Zelfde verhaal als module, maar dan -c adapter:create
 * 
 * @desc Bootstrapper
 * Zelfde verhaal als module, maar dan -c bootstrapper:create
 * 
 * @desc Library
 * Zelfde verhaal als module, maar dan -c library:create
 */
session_start();
define("LOGFILE_PREFIX", 'install');
define("IS_INSTALL", true);
ini_set('display_errors', true);

require_once 'Config/defines.php';
require_once ROOTFRAMEWORK.LIBRARY_DIR.'ErrorHandler/clsErrorHandler.php';

# error reporting
error_reporting(E_ALL);
# set errorhandler
# switch SERVER
# case LIVE: via getopt de application achterhalen
# case TEST: via de _GET methode de application achterhalen

try {
	$get = getopt("a:c:p:o:");
	
	$application_name = (array_key_exists('a', $get)) ? $get['a'] : false;
	$application_dir = ($application_name) ? str_replace(".", "/".APPLICATIONS_DIR, $application_name) : false;
	$application_dir = (($application_dir) && !preg_match("#/$#", $application_dir)) ? $application_dir.'/' : $application_dir;

	$command = (array_key_exists('c', $get)) ? $get['c'] : false;
	if(!$command)
		die("Missing argument 'c' : No command given\n");
	$param = (array_key_exists('p', $get)) ? $get['p'] : false;
	$options = (array_key_exists('o', $get)) ? $get['o'] : false;
		
	require_once ROOTFRAMEWORK.INSTALLJOBS_DIR.'clsInstall.php';
	if($application_name){
		define("APPLICATION_NAME", $application_name);
		define("APPLICATION_DIR", $application_dir);
		
		define("INPUT_APPLICATION", ROOTFRAMEWORK.INPUT_DIR.APPLICATION_NAME.'/');
		define("OUTPUT_APPLICATION", ROOTFRAMEWORK.OUTPUT_DIR.APPLICATION_NAME.'/');	
		define("BIN_APPLICATION", ROOTFRAMEWORK.BIN_DIR.APPLICATION_NAME.'/');
		define("ROOT_APPLICATION", ROOTFRAMEWORK.APPLICATIONS_DIR.APPLICATION_DIR.ROOT_DIR);

		// check if the application exists
		require ROOTFRAMEWORK.INSTALLJOBS_DIR.'Application/clsApplication.php';
		$obj_Application = new Application();
		if(!$obj_Application->exists(APPLICATION_NAME))
			die("[ERROR] Application '".APPLICATION_NAME."' doesn't exist\n");		
	}
	
	if($command != 'application:create' && !defined('APPLICATION_NAME') && $command != 'validate')
		die("[ERROR] Missing argument '-a' (application)\n");	
	
//	if(strpos(':', $command))
		list($class, $method) = explode(':', $command);	
//	else {
//		$class = $command;
//		$method = false;	
//	}
	$class = ucfirst($class);
	if(!is_file(ROOTFRAMEWORK.INSTALLJOBS_DIR.$class.'/cls'.$class.'.php'))
		die("[ERROR] Unable to load class '$class' from '".INSTALLJOBS_DIR.$class.'/cls'.$class.'.php'."' : No such file\n");
		
	if(!$method)	
		die("[ERROR] Missing 'method'\n");
	
	require_once ROOTFRAMEWORK.INSTALLJOBS_DIR.$class.'/cls'.$class.'.php';

	$obj_Class = new $class($options);
	if($method){
		if(!method_exists($obj_Class, $method))
			die("[ERROR] Unable to call method '$method' :: Method doesn't exists\n");
		
		$obj_Class->{$method}($param);	
	}
} catch (Exception $e){
	ErrorHandler::handleError($e);
}
