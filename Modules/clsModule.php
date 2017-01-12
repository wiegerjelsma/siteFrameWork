<?php
/**
 * @name	m_ModuleException
 * @author 	wiegerjelsma
 */
class m_ModuleException extends ErrorHandler {}

/**
 * @name	m_Module
 * @author 	wiegerjelsma
 */
class m_Module {
	
	protected $cfg;
	protected static $instance;
	protected $DB;
	protected $SESSION;
	protected $TPL;
	
	protected $myname;
	
	public $_modulename;
	
	
	/**
	 * @name	pushConfig
	 * @param 	array $cfg
	 * @desc	de adapter config (incl framework / application cfg) wordt gepushed vanuit de loader
	 */
	public function pushConfig($cfg, $myname){
		$this->cfg = $cfg;
		$this->myname = $myname;
	}	
	
	/**
	 * @name init()
	 */
	public function init(){
		$this->openDB();
	}
	
	
	/**
	 * @name getNewInstance()
	 */
	public function getNewInstance(){
		return Loader::getNewInstance(get_class($this), 'Module');
	}
	
	
	/**
	 * @name	openBD
	 * @desc opens the connection
	 */
	protected function openDB(){
		$obj_DB = Loader::load('Database');
 		$this->DB = $obj_DB->connect($this->cfg['db']);
	}
	
	
	/**
   	 * @name 	setUpTemplate
   	 */
   	protected function setUpTemplate(){
		if(array_key_exists('tplengine', $this->cfg['defaults']))
			$this->TPL = Loader::loadModule($this->cfg['defaults']['tplengine']);
	} 
		
	
	/**
   	 * @name 	setUpSession
   	 */
   	protected function setUpSession(){
		if(array_key_exists('sessionengine', $this->cfg['defaults']))
			$this->SESSION = Loader::loadModule($this->cfg['defaults']['sessionengine']);		
	}  	
	
	
	/**
	 * @name	singleton
	 * @param 	string $class
	 */
	public static function singleton($class){
		if(!isset(self::$instance[$class]))			
			self::$instance[$class] = array('object' => new $class(), 'class' => $class);
		
       	return self::$instance[$class]['object'];
   	}
}