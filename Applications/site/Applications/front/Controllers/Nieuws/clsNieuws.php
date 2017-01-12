<?php
/**
 * @name	c_site_front_NieuwsException
 * @author 	wiegerjelsma
 * @version	1.0 2013-10-14 13:25:39
 */
class c_site_front_NieuwsException extends c_site_front_ControllerException {}

/**
 * @name	c_site_front_Nieuws
 * @author 	wiegerjelsma
 * @version	1.0 2013-10-14 13:25:39
 */
class c_site_front_Nieuws extends c_site_front_Controller {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2013-10-14 13:25:39
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	public function overzicht(){
		$obj_Nieuws = Loader::loadModule('Nieuws');
		$a_Nieuws = $obj_Nieuws->get(array('status' => true), array('order' => 'datum DESC'));	
		
		$this->TPL->assign('Nieuws', $a_Nieuws);		
		$this->TPL->display('nieuws.tpl');			
	}
	
	public function detail(){
		$obj_Nieuws = Loader::loadModule('Nieuws');
		$a_Nieuws = $obj_Nieuws->get(array('short_url' => ID_NAME, 'status' => true));				
				
		if(!$a_Nieuws){
			$obj_Url = Loader::load('Url');					
			$obj_Url->Redirect();
			return;
		}
		if($a_Nieuws[0]['meta_title'])
			$this->TPL->assign('Meta_Title', $a_Nieuws[0]['meta_title']);		
		if($a_Nieuws[0]['meta_description'])
			$this->TPL->assign('Meta_Description', $a_Nieuws[0]['meta_description']);
		if($a_Nieuws[0]['meta_keywords'])
			$this->TPL->assign('Meta_Keywords', $a_Nieuws[0]['meta_keywords']);

		if($a_Nieuws[0]['fb_sharer_title'])
			$this->TPL->assign('Fb_Sharer_Title', $a_Nieuws[0]['fb_sharer_title']);
		else
			$this->TPL->assign('Fb_Sharer_Title', $a_Nieuws[0]['titel']);

		if($a_Nieuws[0]['fb_sharer_description']){
			$this->TPL->assign('Fb_Sharer_Description', $a_Nieuws[0]['fb_sharer_description']);
		} else {
			if($a_Nieuws[0]['body_teaser'])
				$this->TPL->assign('Fb_Sharer_Description', $a_Nieuws[0]['body_teaser']);
			else
				$this->TPL->assign('Fb_Sharer_Description', substr($a_Nieuws[0]['body'], 0, 128));
		}
					
					
		$a_Nieuws[0]['body'] = $this->parseBodyTekst($a_Nieuws[0]['body']);					
		
		$this->pushKruimelpad($a_Nieuws[0]['titel']);
		$this->TPL->assign('Nieuws', $a_Nieuws[0]);
		$this->TPL->display('nieuws_detail.tpl');			
	}
	
	
	public function view(){
		if(!ID_NAME){
			$this->overzicht();
		} else {
			$this->detail();			
		}	
	}
}
