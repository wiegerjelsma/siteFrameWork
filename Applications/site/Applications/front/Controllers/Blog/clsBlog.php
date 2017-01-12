<?php
/**
 * @name	c_site_front_BlogException
 * @author 	wiegerjelsma
 * @version	1.0 2013-03-10 14:32:56
 */
class c_site_front_BlogException extends c_site_front_ControllerException {}

/**
 * @name	c_site_front_Blog
 * @author 	wiegerjelsma
 * @version	1.0 2013-03-10 14:32:56
 */
class c_site_front_Blog extends c_site_front_Controller {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2013-03-10 14:32:56
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	public function overzicht(){
		$obj_Blog = Loader::loadModule('Blog');
		$a_Blog = $obj_Blog->get(array('status' => true), array('order' => 'datum DESC'));	
		
		$this->TPL->assign('Blog', $a_Blog);		
		$this->TPL->display('blog.tpl');			
	}
	
	public function detail(){
		$obj_Blog = Loader::loadModule('Blog');
		$a_Blog = $obj_Blog->get(array('short_url' => ID_NAME, 'status' => true));				
		
		if(!$a_Blog){
			$obj_Url = Loader::load('Url');					
			$obj_Url->Redirect();
			return;
		}		
		if($a_Blog[0]['meta_title'])
			$this->TPL->assign('Meta_Title', $a_Blog[0]['meta_title']);	
		if($a_Blog[0]['meta_description'])
			$this->TPL->assign('Meta_Description', $a_Blog[0]['meta_description']);
		if($a_Blog[0]['meta_keywords'])
			$this->TPL->assign('Meta_Keywords', $a_Blog[0]['meta_keywords']);				
			
		if($a_Blog[0]['fb_sharer_title'])
			$this->TPL->assign('Fb_Sharer_Title', $a_Blog[0]['fb_sharer_title']);
		else
			$this->TPL->assign('Fb_Sharer_Title', $a_Blog[0]['titel']);

		if($a_Blog[0]['fb_sharer_description'])
			$this->TPL->assign('Fb_Sharer_Description', $a_Blog[0]['fb_sharer_description']);
		else {
			
			if($a_Blog[0]['body_teaser'])
				$this->TPL->assign('Fb_Sharer_Description', $a_Blog[0]['body_teaser']);
			else
				$this->TPL->assign('Fb_Sharer_Description', substr($a_Blog[0]['body'], 0, 128));
		}
			
			
		$a_Blog[0]['body'] = $this->parseBodyTekst($a_Blog[0]['body']);	
			
		$this->pushKruimelpad($a_Blog[0]['titel']);
		$this->TPL->assign('Blog', $a_Blog[0]);
		$this->TPL->display('blog_detail.tpl');			
	}
	
	
	public function view(){
		if(!ID_NAME){
			$this->overzicht();
		} else {
			$this->detail();			
		}	
	}
}
