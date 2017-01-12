<?php
require_once 'Zend/Db.php';
require_once 'Zend/Db/Adapter/Pdo/Mysql.php';

class Database extends Zend_Db {
	
	private static $a_Connections = array();
	
	
	protected static $instance;
	
	
	public static function singleton(){
    	if (!isset(self::$instance)) {
     		$c = __CLASS__;
       		self::$instance = new $c;
    	}
       	return self::$instance;
   	}		
   	
	/**
	 * @name	connect
	 * @desc	zet een conncetie op met de db
	 * @param 	array $cfg, str $db
	 */
	public function connect($cfg, $db = false){
		$db = $db ? $db : (array_key_exists(APPLICATION_NAME, $cfg['names']) ? $cfg['names'][APPLICATION_NAME] : SITENAME.'_'.APPLICATION_NAME);
			
		if(array_key_exists($db, self::$a_Connections))
			return self::$a_Connections[$db];
			
		// deze db is nog niet geconnect
		try {
			$cfg['dsn']['params'][SERVER]['dbname'] = $db;
			$cfg['dsn']['params'][SERVER]['charset'] = 'utf8';
						
	   		self::$a_Connections[$db] = new Zend_Db_Adapter_Pdo_Mysql($cfg['dsn']['params'][SERVER]);
	   		
	   		self::$a_Connections[$db]->query('SET sql_mode = \'\'');
	   		
	   		return self::$a_Connections[$db];
			
		} catch (Zend_Db_Adapter_Exception $e) {
			dump(__METHOD__.'[DATABASE ERROR] : perhaps a failed login credential, or perhaps the RDBMS is not running');
			dump($e->getMessage());
		} catch (Zend_Exception $e) {
			dump(__METHOD__.'[DATABASE ERROR] : perhaps factory() failed to load the specified Adapter class');
		}			
	} 
	
	
	/**
	 * @name	connect
	 * @desc	zet een conncetie op met de db
	 * @param 	array $cfg, str $db
	 */
	/*public function connect($cfg, $db = false){
		$db = $db ? $db : (array_key_exists(APPLICATION_NAME, $cfg['names']) ? $cfg['names'][APPLICATION_NAME] : FWPREFIX.'_'.APPLICATION_NAME);
				
		if(array_key_exists($db, self::$a_Connections))			
			return self::$a_Connections[$db];
			
		// deze db is nog niet geconnect
		try {
			$cfg['dsn']['params'][SERVER]['dbname'] = $db;
			$cfg['dsn']['params'][SERVER]['charset'] = 'utf8';
			
			self::$a_Connections[$db] = self::factory($cfg['dsn']['type'], $cfg['dsn']['params'][SERVER]);
	   		return self::$a_Connections[$db];
			
		} catch (Zend_Db_Adapter_Exception $e) {
			dump(__METHOD__.'[DATABASE ERROR] : perhaps a failed login credential, or perhaps the RDBMS is not running');
			dump($e->getMessage());
		} catch (Zend_Exception $e) {
			dump(__METHOD__.'[DATABASE ERROR] : perhaps factory() failed to load the specified Adapter class');
		}			
	} */	  	
}