<?php
define("FWPREFIX", 'site');
// define("SITENAME", 'osteovitaal');  STAAT NU VERDER NAAR BENEDEN
//define("FTPUSERLIVE",'osvtfwlive');
define("FTPUSERLIVE",'osvt2fwlive');

define("EOL","\n");

define("LOG_DIR","Log/");

define("MODULES_DIR","Modules/");
define("CONFIG_DIR","Config/");
define("CACHE_DIR","Cache/");
define("LIBRARY_DIR","Library/");
define("TEMPLATES_DIR","Templates/");
define("CONTROLLERS_DIR","Controllers/");
define("BOOTSTRAPPERS_DIR","Bootstrappers/");
define("APPLICATIONS_DIR","Applications/");
define("CRONJOBS_DIR","Cronjobs/");
define("INSTALLJOBS_DIR","Installjobs/");

define("BIN_DIR","_bin/");
define("INPUT_DIR","_input/");
define("OUTPUT_DIR","_output/");
define("ROOT_DIR","Webroot/");

define('IP_MACBOOK','127.0.0.1');
define('IP_MACBOOK_2','10.0.1.5');

define('IP_VPSMEDOT','79.170.89.132');
define('IP_VPSMEDOT_2','79.170.89.251');
define('IP_VPSCCMI','79.170.93.197');
define('IP_VPSCCMI_SSL1','79.170.93.103');
define('IP_VPSCCMI_SSL2','79.170.93.104');


# SERVER ADDR
$ip[IP_MACBOOK] = 'MACBOOK';
$ip[IP_MACBOOK_2] = 'MACBOOK';
$ip[IP_VPSMEDOT] = 'VPSMEDOT';
$ip[IP_VPSMEDOT_2] = 'VPSMEDOT';
$ip[IP_VPSCCMI] = 'VPSCCMI';
$ip[IP_VPSCCMI_SSL1] = 'VPSCCMI';
$ip[IP_VPSCCMI_SSL2] = 'VPSCCMI';

#
# User (ivm server herkenning vanaf de commandline)
# Dit is de user waaruit de crontabs worden uitgevoerd
$usr['wiegerjelsma'] = 'MACBOOK';
$usr['root'] = 'MACBOOK';
$usr[FTPUSERLIVE] = 'VPSMEDOT';

# Define Server name
if(array_key_exists('SERVER_ADDR',$_SERVER)){
	$_server = $ip[$_SERVER['SERVER_ADDR']];
	define('SERVER_NAME', $_server);
} elseif(array_key_exists('USER',$_SERVER)){
	$_server = $usr[$_SERVER['USER']];
	define('SERVER_NAME', $_server);
}
switch(SERVER_NAME){
	default:
		trigger_error("SERVER ".SERVER_NAME." not implemented");
	break;
	case 'VPSMEDOT':
		define("SITENAME", 'osvt2');		
		//define("SITENAME", 'osvt');
		define("SERVER",$_server);
		if(array_key_exists('USER', $_SERVER))
			define("SERVER_USER",$_SERVER['USER']);		
		else
			define("SERVER_USER",SITENAME.'fwlive');		
		
		define("ROOTFRAMEWORK","/var/www/".SITENAME."/".FWPREFIX."FrameWork/");
		
		define("SMTP_POSTMARK", false);
//		define("SMTP_POSTMARK_APIKEY", '5efbf4b4-2402-4de7-b7c4-253cce5a8c18');
	break;		
	case 'MACBOOK':
		define("SITENAME", 'osvt');
		define("SERVER",$_server);
		if(array_key_exists('USER', $_SERVER))
			define("SERVER_USER",$_SERVER['USER']);		
		else		
			define("SERVER_USER",'www');
			
		define("SMTP",'mail.wgsframework.com');
		define("SMTP_USER",'wieger@wgsframework.com');
		define("SMTP_PASS",'krabpaal');
		
		define("ROOTFRAMEWORK","/Library/WebServer/Documents/".SITENAME."/".FWPREFIX."FrameWork/");	
	break;
}

$include_path['MACBOOK'][] = '.';
$include_path['MACBOOK'][] = '/Library/WebServer/Documents/'.SITENAME.'/'.FWPREFIX.'FrameWork/';
$include_path['MACBOOK'][] = '/Library/WebServer/Documents/'.SITENAME.'/'.FWPREFIX.'FrameWork/Library/';
$include_path['MACBOOK'][] = '/Library/WebServer/Documents/'.SITENAME.'/'.FWPREFIX.'FrameWork/Library/Zend/';
#
$include_path['VPSMEDOT'][] = '.';
$include_path['VPSMEDOT'][] = '/var/www/'.SITENAME."/".FWPREFIX.'FrameWork/';
$include_path['VPSMEDOT'][] = '/var/www/'.SITENAME."/".FWPREFIX.'FrameWork/Library/';
$include_path['VPSMEDOT'][] = '/var/www/'.SITENAME."/".FWPREFIX.'FrameWork/Library/Zend/';


$include_path = join(":",$include_path[SERVER_NAME]);
ini_set('include_path',$include_path);

# set some ini vars
date_default_timezone_set("Europe/Amsterdam");
setlocale(LC_TIME, 'nl_NL');
ini_set('html_errors','0');