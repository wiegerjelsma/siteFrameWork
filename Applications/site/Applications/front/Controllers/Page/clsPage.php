<?php
/**
 * @name	c_site_front_PageException
 * @author 	wiegerjelsma
 * @version	1.0 2013-02-16 13:33:39
 */
class c_site_front_PageException extends c_site_front_ControllerException {}

/**
 * @name	c_site_front_Page
 * @author 	wiegerjelsma
 * @version	1.0 2013-02-16 13:33:39
 */
class c_site_front_Page extends c_site_front_Controller {
	
	
	protected $a_Post;
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2013-02-16 13:33:39
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	public function view(){
		$this->short();		
	}
	
	
	public function short(){
		if(!ID_NAME){
			$obj_Menu = Loader::loadModule('Cms.Menu'); 		
			$a_Menu = $obj_Menu->get(array('menu_type' => 'menu'), array('order' => 'id ASC'));
			if($a_Menu){
				$menu = $a_Menu[0]['item'];
				$pages = $a_Menu[0]['children'];
				
				foreach($pages as $page){
					if($page['item']['menu_type'] == 'page' && $page['item']['status']){
						$firstPageInMenu = $page['item'];
						break;
					}
				} 
				if(isset($firstPageInMenu)){
					$obj_Pages = Loader::loadModule('Cms.Pages');
					$page = $obj_Pages->getOne($firstPageInMenu['page_id']);				
					$this->handlePage($page);
				}
			}
		} else {
			$obj_Pages = Loader::loadModule('Cms.Pages');
			$page = $obj_Pages->getOneByShortUrl(ID_NAME);
			if(!$page){
				$obj_Url = Loader::load('Url');					
				$obj_Url->Redirect();
			}
			$this->handlePage($page);
		}
	}
	
	private function handlePage($page){	
		switch($page['type']){
			case 'content':
				$this->TPL->assign('Content_Title', $page['content_title']);				
				$this->TPL->assign('Content_Body', $this->parseBodyTekst($page['content_body']));
				
				$this->handleForm($page, 'page_id');				
				$this->TPL->display('page.tpl');				
			break;
			case 'virtual':
				$obj_Url = Loader::load('Url');					
				$obj_Url->RedirectToShortUrl($page['short_url']);
			break;
			case 'template':	
				$this->TPL->assign('Content_Title', $page['name']);												
				$this->TPL->display(preg_replace('/\.tpl$/','',$page['custom_template']).'.tpl');				
			break;
		}
	}		
}
