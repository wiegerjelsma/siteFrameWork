<?php

function curry($func, $arity) {
    	return create_function('', "
        	\$args = func_get_args();
        	if(count(\$args) >= $arity)
            	return call_user_func_array('$func', \$args);
        	\$args = var_export(\$args, 1);
        	return create_function('','
            	\$a = func_get_args();
            	\$z = ' . \$args . ';
            	\$a = array_merge(\$z,\$a);
            	return call_user_func_array(\'$func\', \$a);
   	    	');
   	 	");
	}

	function on_match($transformation, $matches){
 	 	return $transformation[strtolower($matches[1])];
	}

class Tools {


	/**
	 * @name	ReadGet
	 */
	public static function ReadGet(){
		$queryString = array();
		$aElements = explode("&", $_SERVER['QUERY_STRING']);
		if(is_array($aElements) && count($aElements)){
			foreach($aElements as $element){
				if(stripos($element,'=')){
					list($key, $value) = explode("=",$element, 2);
					if($key)
						$queryString[$key] = htmlspecialchars(urldecode($value));
				}
			}
		}
		return $queryString;
	}

	
	/**
	 * @name	ReadPost
	 */
	public static function ReadPost(){			
		$toReturn = array();
		foreach($_POST as $key => $value)
			$toReturn[$key] = (is_string($value)) ? htmlspecialchars(str_replace('&#39;',"'",$value)) : $value;
		return $toReturn;
	}

	/**
	 * @name	ReadFiles
	 */
	public static function ReadFiles(){			
		return $_FILES;
	}
	

	
	/**
	 * @name	generatePassword
	 */
	public function generatePassword($length){
    		$pass = '';
    		$a_Chars = array();
    	
    		$a_Int = range(0,9);
    		$a_Chars = array_merge($a_Int, $a_Chars);    	
    	
    		$a_AlfabetUC = range('A','Z');
    		$a_Chars = array_merge($a_AlfabetUC, $a_Chars);

    		$a_AlfabetLC = range('a','z');
    		$a_Chars = array_merge($a_AlfabetLC, $a_Chars);
    	
     		$count = count($a_Chars) - 1;
     		srand((double)microtime()*1000000);

     		for($i = 0; $i < $length; $i++) 
     				$pass .= $a_Chars[rand(0, $count)];

     		return $pass;
	}	
}
	