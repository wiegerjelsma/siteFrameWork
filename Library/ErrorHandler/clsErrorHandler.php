<?php
class ErrorHandler extends Exception {
	
    protected $message = 'Unknown exception';     // Exception message
    private   $string;                            // Unknown
    protected $code    = 0;                       // User-defined exception code
    protected $file;                              // Source filename of exception
    protected $line;                              // Source line of exception
    private   $trace;                             // Unknown	
   	    
	public function __construct($message, $code = 0){
		$this->message = $message;
		$this->code = $code;
		
//				dump("'".$message."' in ".$this->file.' at line '.$this->line, 0, $code);

	}
	
	public static function handleError($e){
			/*
			EMERG   = 0;  // Emergency: system is unusable
			ALERT   = 1;  // Alert: action must be taken immediately
			CRIT    = 2;  // Critical: critical conditions
			ERR     = 3;  // Error: error conditions
			WARN    = 4;  // Warning: warning conditions
			NOTICE  = 5;  // Notice: normal but significant condition
			INFO    = 6;  // Informational: informational messages
			DEBUG   = 7;  // Debug: debug messages
		*/		
		switch($e->code){
			default:
				dump("'".$e->message."' in ".$e->file.' at line '.$e->line, 0);
				dump("\n\n".$e->getTraceAsString());
			break;			
		}		
	}
}