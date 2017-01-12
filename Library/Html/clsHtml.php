<?php
require_once 'Html/simple_html_dom.php';

class Html {
	
	protected static $instance;
	
	protected $cfg;
	protected $dom;
	
	/**
	 * @name	singleton
	 * @param 	string $class
	 */
	public static function singleton($class){		
		if(!isset(self::$instance)){
			self::$instance[$class] = array('object' => new $class(), 'class' => $class);
		}
       	return self::$instance[$class]['object'];
   	}
   	
   	
	/**
	 * @name getNewInstance()
	 */
	public function getNewInstance(){
		return Loader::getNewInstance(get_class($this), 'Library');
	}
	   	
	   	
	/**
	 * @name	pushConfig
	 * @param 	array $cfg
	 * @desc	de adapter config (incl framework / application cfg) wordt gepushed vanuit de loader
	 */
	public function pushConfig($cfg){
		$this->cfg = $cfg;
	} 
	
	public function __call($fn, $a_Arguments){		
		return call_user_func_array(array($this->dom, $fn), $a_Arguments);		
	}
	
	public function str_get_html($str, $lowercase=true, $forceTagsClosed=true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN=true, $defaultBRText=DEFAULT_BR_TEXT,$defaultSpanText=DEFAULT_SPAN_TEXT){
		$this->dom = str_get_html($str, $lowercase=true, $forceTagsClosed=true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN=true, $defaultBRText=DEFAULT_BR_TEXT,$defaultSpanText=DEFAULT_SPAN_TEXT);		
		return $this->dom;
	}	
}