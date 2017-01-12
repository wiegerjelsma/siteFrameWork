<?php
function dump($var, $name=false, $error = false, $printing = false){
	$output = ($name) ? ''.$name.": " : '';  	
	if(!is_string($var))
  		$output .= print_r($var, true);
  	else
  		$output = $output.$var;	
	
  	if($printing)
  		print $output;	
  	else
	  	logger($output, $error);
}

function logger($data, $error = Zend_Log::INFO)
{
	$registry = Zend_Registry::getInstance();
  	$logger = $registry->get('logger');
  	$logger->log($data, $error);	
}


		
