<?php
/**
 * @name	b_site_BootstrapperException
 * @author 	wiegerjelsma
 * @version	1.0 2012-02-22 22:00:39
 */
class b_site_BootstrapperException extends b_BootstrapperException {}

/**
 * @name	b_site_Bootstrapper
 * @author 	wiegerjelsma
 * @version	1.0 2012-02-22 22:00:39
 */
class b_site_Bootstrapper extends b_Bootstrapper {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-02-22 22:00:39
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
}
