<?php
/**
 * @name	c_av_back_CmsException
 * @author 	wiegerjelsma
 * @version	1.0 2012-11-03 17:54:44
 */
class c_site_back_CmsException extends c_site_back_ControllerException {}

/**
 * @name	c_av_back_Cms
 * @author 	wiegerjelsma
 * @version	1.0 2012-11-03 17:54:44
 */
class c_site_back_Cms extends c_site_back_Controller {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-11-03 17:54:44
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	
	/**
	 * @name	view
	 * @desc	Hier gaan we links de tree tonen met het menu en rechts de grid met de pagina's
	 */
	public function view($a_Params = array()){
		$obj_Menu = Loader::loadModule('Cms.Menu'); 		
		$a_Menu = $obj_Menu->get(array('parent_id' => false), array('order' => 'order_id ASC'));
		$draft = $obj_Menu->get(array('menu_type' => 'draft'));
		if($draft){
			$this->TPL->assign('Menu', json_encode($obj_Menu->formatJstree($a_Menu)));
			$this->TPL->assign('MenuDraftID', $draft[0]['item']['id']);
		} else {
			$this->TPL->assign('Menu', json_encode(array()));		
		}
		
 		$this->readPost();
 		$this->setUpModule('Cms.Pages');
 		
 		$a_Search = $this->setUpSearch();
 		$a_Search = $a_Search ? $a_Search : array();
 		
 		$a_Order = $this->setUpOrder();
 		$a_Order = $a_Order ? array('order' => $a_Order['key'].' '.$a_Order['direction']) : array('order' => 'id ASC'); 		
 		
		$obj_Pages = Loader::loadModule('Cms.Pages');
	
		$a_Result = $obj_Pages->get($a_Params, $a_Order, $a_Search);
		$a_Header = $this->getDataHeader();
		$a_HeaderKeys = array_keys($a_Header);
		
		$a_Dataset = false;
		
		if($a_Result)
			foreach($a_Result as $result){
				if(!IS_ADMIN){
					if($result['is_predefined'])
						continue;
				}
				$a = array();
				foreach($a_HeaderKeys as $key)
					 $a[$key] = $result[$key];
					
				$a['id'] = $result['id'];
				$a_Dataset[] = $a;
			}
		
 		$a_Buttons = false;
 		if(isset($this->cfg['access']['actions']['add']) && $this->cfg['access']['actions']['add'])
	 		$a_Buttons[] = array('url' => APPLICATION_URL.'/'.(isset($this->cfg['controller']['add']) ? $this->cfg['controller']['add'] : CONTROLLER_NAME).'/add', 'value' => 'add');
 		
		$this->TPL->assign('Buttons', $a_Buttons); 			 		
		$this->TPL->assign('DataHeader', $a_Header);
		$this->TPL->assign('DataSet', $a_Dataset);
		
		$this->TPL->assign('EditController', isset($this->cfg['controller']['edit']) ? $this->cfg['controller']['edit'] : false);
		$this->TPL->assign('DeleteController', isset($this->cfg['controller']['delete']) ? $this->cfg['controller']['delete'] : false);			
			
		$this->TPL->display('cms.tpl');		
	}
	
	public function ajax_updateMenu(){
 		$this->readPost();
 		
 		$obj_Menu = Loader::loadModule('Cms.Menu');
 		$obj_Menu->moveNode($this->a_Post);
 		
 		// Hier nog goed of fout retourneren
	}
}
