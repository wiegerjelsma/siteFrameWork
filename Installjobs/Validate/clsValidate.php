<?php
/**
 * @name	Module (install script)
 * @author 	wiegerjelsma
 *
 */
class Validate extends Install {
	
	
	public function __construct($options = false){
		parent::__construct($options);			
		
		$this->validate();
	}
	
	private function validate(){
		print "validate";
	}
}