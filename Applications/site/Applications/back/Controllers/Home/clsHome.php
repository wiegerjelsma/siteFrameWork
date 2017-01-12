<?php
/**
 * @name	c_site_back_HomeException
 * @author 	wiegerjelsma
 * @version	1.0 2012-03-27 12:03:49
 */
class c_site_back_HomeException extends c_site_back_ControllerException {}

/**
 * @name	c_site_back_Home
 * @author 	wiegerjelsma
 * @version	1.0 2012-03-27 12:03:49
 */
class c_site_back_Home extends c_site_back_Controller {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-03-27 12:03:49
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	/**
	 * @name	view
 	 * @desc	this is the homepage
 	 */	 
	public function view(){
		$a_Tabs = array();
		if(count($this->cfg['tabs']) > 1)
			foreach($this->cfg['tabs'] as $tab){
				$a_Submenu = array();
				foreach($this->cfg['leftmenu'][$tab['name']] as $link){
					$link['active'] = (strToLower(CONTROLLER_NAME) == $link['controller']) ? true : false;
					$a_Submenu[] = $link;				
				}			
				$a_Tabs[$tab['name']] = $a_Submenu;	
			}
		
		$this->TPL->assign('HomeTabs', $a_Tabs);			
		$this->TPL->display('home.tpl');
	}

	
	/**
	 * @name	view
 	 * @desc	this is the homepage
 	 */	 
	public function tab(){
		$this->TPL->display('tabs/'.ID_NAME.'.tpl');
	}	
}
