<?php
/**
 * @name	m_TemplateException
 * @author 	wiegerjelsma
 */
class m_SessionException extends m_ModuleException {}

/**
 * @name	m_Session
 * @author 	wiegerjelsma
 */
class m_Session extends m_Module {		
	
	protected $a_Vars = array();
	
	
	/**
	 * @name	init 
	 */
	public function init(){
		parent::init();
	}
	
	public function getID(){
		return session_id();
	}
	
	
	/**
	 * @name 	write
	 * @param 	str $key
	 * @param 	str $value
	 */
    public function write($key,$value){	
    	$_SESSION[$key] = $value;
    }

    
    /**
     * @name	writeByArray
     * @param 	array $array
     */
   	public function writeByArray($array){
   		foreach($array as $key => $value)
   			$this->write($key,$value);
   	}

   	
   	/**
   	 * @name	read
   	 * @param 	string $key
   	 */
   	public function read($key){
   		if(array_key_exists($key,$_SESSION))
   			return $_SESSION[$key];
   		return false;
   	}
   	
   	
   	/**
   	 * @name	exists
   	 * @param 	str $key
   	 */
   	public function exists($key){
   		return isset($_SESSION[$key]);
   	}

   	
	/**
	 * @name	reset
	 */
   	public function reset(){
   		$_SESSION = array();
   	}   	
   	
   	
   	/**
   	 * @name	clean
   	 * @param 	str $key
   	 */
   	public function clean($key){   		
   		if(array_key_exists($key,$_SESSION))
   			unset($_SESSION[$key]);
   		return true;
   	}

   	
   	/**
   	 * @name	readByArray
   	 * @param 	array $array
   	 */
   	public function readByArray($array){
   		foreach($array as $key)
   			$return[] = $this->read($key);
   		return $return;
   	}   	
}