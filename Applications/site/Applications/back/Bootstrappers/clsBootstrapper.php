<?php
/**
 * @name	b_site_back_BootstrapperException
 * @author 	wiegerjelsma
 * @version	1.0 2012-02-22 22:00:59
 */
class b_site_back_BootstrapperException extends b_site_BootstrapperException {}

/**
 * @name	b_site_back_Bootstrapper
 * @author 	wiegerjelsma
 * @version	1.0 2012-02-22 22:00:59
 */
class b_site_back_Bootstrapper extends b_site_Bootstrapper {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-02-22 22:00:59
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
}
