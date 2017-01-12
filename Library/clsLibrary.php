<?php
class LibraryException extends ErrorHandler {}

class Library {

	protected static $instance;
	
	protected $cfg;
	protected $myname;	
	
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
	public function pushConfig($cfg, $myname){
		$this->cfg = $cfg;
		$this->myname = $myname;
	}	
	
	
	
	public function arrayCombine($a_Arr1, $a_Arr2){
    	$a_Arr1 = is_array($a_Arr1) ? $a_Arr1 : array();
   	 	$a_Arr2 = is_array($a_Arr2) ? $a_Arr2 : array();
   	 	if(is_array($a_Arr2) && count($a_Arr2)){

			// loopen over alle keys van $a_Arr2
			foreach($a_Arr2 as $key => $value){

				// als de key niet bestaat in $a_Arr1 voegen we hem toe aan de $a_Arr1
				if(!array_key_exists($key, $a_Arr1)){
					$a_Arr1[$key] = $value;

				// Als de key wel bestaat in $a_Arr1
				} else {

					// als de value een array betreft gaan we de functie 'merge' aanroepen voor die value
					if(is_array($value) && count($value)){
						$a_Arr1[$key] = arrayCombine($a_Arr1[$key], $value);
					} else {

						// nieuwe key aanmaken
						array_push($a_Arr1, $value);
						//$a_Arr1[$key] = $value;
					}
				}
			}
		}
		return $a_Arr1;
	}	 	
}