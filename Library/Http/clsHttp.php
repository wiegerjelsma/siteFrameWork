<?php
require_once 'Zend/Http/Client.php';

class Http extends Zend_Http_Client {
	
	protected static $instance;
	
	protected $cfg;
	
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
}