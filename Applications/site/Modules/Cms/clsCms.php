<?php
/**
 * @name	m_av_CmsException
 * @author 	wiegerjelsma
 * @version	1.0 2012-11-03 17:54:14
 */
class m_site_CmsException extends m_site_ModuleException {}

/**
 * @name	m_av_Cms
 * @author 	wiegerjelsma
 * @version	1.0 2012-11-03 17:54:14
 */
class m_site_Cms extends m_site_Module {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-11-03 17:54:14
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	public function getMenu($name = false, $forceActiveNode = false, $active = false, $status = false){
		$a_Menus = $this->_getMenu($name, $status);
	
		$a_MenuAssign = array();
		$activeNode = false;
		if($a_Menus)
			foreach($a_Menus as $menu){
		
				$a = array();
				$a['menu'] = $menu['menu'];
				$a['nodes'] = array();
				foreach($menu['nodes'] as $node){				
					$nodeFormatted = $this->_formatNode($node, false, $active);
					$activeNode = $activeNode ? true : (isset($nodeFormatted['active']) && $nodeFormatted['active'] ? true : false);
					$a['nodes'][] = $nodeFormatted;
				}
				if($forceActiveNode && !$activeNode){
				
					$a = array();
					$a['menu'] = $menu['menu'];
					$a['nodes'] = array();
					foreach($menu['nodes'] as $node){
						$nodeFormatted = $this->_formatNode($node);
						$nodeFormatted['active'] = $activeNode ? false : true;
						$activeNode = true;
						$a['nodes'][] = $nodeFormatted;				
					}
				}
				$a_MenuAssign[] = $a;
			}
		if($name && count($a_MenuAssign) >= 1)
			return $a_MenuAssign[0];
		return $a_MenuAssign;
	}
	
	public function getSubMenu($n, $forceActiveNode = false, $active = false, $status = false){
		if(!isset($n['id']))
			return array();
		$a_Nodes = $this->getMenuItems($n['id'], $status);
	
		$a_MenuAssign = array();
		$activeNode = false;
		
		$a_Submenu = array();

		foreach($a_Nodes as $node){
			$nodeFormatted = $this->_formatNode($node, false, $active);
			
			$activeNode = $activeNode ? true : (isset($nodeFormatted['active']) && $nodeFormatted['active'] ? true : false);
			$a_Submenu[] = $nodeFormatted;
		}
			
		if($forceActiveNode && !$activeNode){
			$a_Submenu = array();
			foreach($a_Nodes as $node){
				$nodeFormatted = $this->_formatNode($node);

				$nodeFormatted['active'] = $activeNode ? false : true;
				$activeNode = true;
				$a_Submenu[] = $nodeFormatted;				
			}
		}
		
		return $a_Submenu;
	}
	
	private function _formatNode($menu, $forceActiveNode = false, $active = false){
		$controller = strToLower(CONTROLLER_NAME);		
		$function = strToLower(FUNCTION_NAME);
		$id = strToLower(ID_NAME);
			
		switch($menu['type']){
			default:
				return array();
			break;		
			case 'content':
			case 'template':
				$a_Node = array();
				$a_Node['active'] = false;
				$a_Node['name'] = $menu['name'];				
				$a_Node['comment'] = $menu['comment'];
				$a_Node['id'] = $menu['menu_id'];
				$a_Node['page_id'] = $menu['page_id'];
				$a_Node['visible_in_menu'] = $menu['visible_in_menu'];

								
				$a_Node['target'] = '_self';				
			//	$a_Node['page_id'] = $menu['page_id'];		
				$a_Node['url_controller'] = 'page';
				$a_Node['url_function'] = 'view';
				$a_Node['url_id'] = $menu['page_id'];
//				$a_Node['url'] = APPLICATION_URL.'/'.$a_Node['url_controller'].'/'.$a_Node['url_function'].'/'.$a_Node['url_id'];
				$a_Node['url'] = PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.$menu['short_url'];
											
				if($active == $a_Node['id'])
					$a_Node['active'] = true;
					
				if(!$a_Node['active'])
					if($controller == strToLower($a_Node['url_controller']) && $function == strToLower($a_Node['url_function']) && $id == strToLower($a_Node['url_id']))
						$a_Node['active'] = true;
	
				if(!$a_Node['active'])
					if(preg_match('#^'.$a_Node['url'].'$#i', THIS_URL))
						$a_Node['active'] = true;
																	
			break;

			case 'virtual':
				if($menu['virtual_page_id']){
					$obj_Pages = Loader::loadModule('Cms.Pages');
					$a_Page = $obj_Pages->getOne($menu['virtual_page_id']);
					$a_Page['menu_id'] = $menu['menu_id'];
					$a_Page['short_url'] = $menu['short_url'];

					
					$a_Node = $this->_formatNode($a_Page, $forceActiveNode, $active);
					$a_Node['name'] = $menu['name'];
					$a_Node['comment'] = $menu['comment'];
					$a_Node['page_id'] = $menu['page_id'];
				$a_Node['visible_in_menu'] = $menu['visible_in_menu'];					
					
				} else {
					$a_Node = array();
					$a_Node['active'] = false;
					$a_Node['id'] = $menu['menu_id'];				
					$a_Node['page_id'] = isset($menu['page_id']) ? $menu['page_id'] : 0 ;
					
					$a_Node['name'] = $menu['name'];
					$a_Node['comment'] = $menu['comment'];				
					$a_Node['target'] = '_self';
				//	$a_Node['page_id'] = $menu['page_id'];										
					$a_Node['url_controller'] = $menu['virtual_int_url_controller'];
					$a_Node['url_function'] = $menu['virtual_int_url_function'];
					$a_Node['url_id'] = $menu['virtual_int_url_id'];
					//$a_Node['url'] = APPLICATION_URL.'/'.$a_Node['url_controller'].'/'.$a_Node['url_function'].'/'.$a_Node['url_id'];
					$a_Node['url'] = PROTOCOL.'://'.APPLICATION_DOMAIN.'/'.$menu['short_url'];	
				$a_Node['visible_in_menu'] = $menu['visible_in_menu'];							

					if($active == $a_Node['id'])
						$a_Node['active'] = true;
					
					if(!$a_Node['active'])
						if($controller == strToLower($a_Node['url_controller']) && $function == strToLower($a_Node['url_function']) && $id == strToLower($a_Node['url_id']))
							$a_Node['active'] = true;
						
					if(!$a_Node['active'] && !$a_Node['url_id'])						
						if($controller == strToLower($a_Node['url_controller']) && $function == strToLower($a_Node['url_function']))
							$a_Node['active'] = true;
							
					if(!$a_Node['active'] && !$a_Node['url_id'] && !$a_Node['url_function'])						
						if($controller == strToLower($a_Node['url_controller']))
							$a_Node['active'] = true;
							
					if(!$a_Node['active'])
						if(preg_match('#^'.$a_Node['url'].'$#i', THIS_URL))
							$a_Node['active'] = true;					
				}			
			break;				
			case 'redirect':
				$a_Node = array();
				$a_Node['name'] = $menu['name'];
				$a_Node['comment'] = $menu['comment'];					
				$a_Node['id'] = $menu['menu_id'];
				$a_Node['page_id'] = $menu['page_id'];
											
				
				$menu['redirect_ext_url'] = preg_replace('/^http:\/\//','', $menu['redirect_ext_url']);
				$a_Node['url'] = 'http://'.$menu['redirect_ext_url'];
				$a_Node['target'] = '_blank';			
				$a_Node['visible_in_menu'] = $menu['visible_in_menu'];					
			break;
			case 'alias':
				$obj_Pages = Loader::loadModule('Cms.Pages');
				$page = $obj_Pages->getOne($menu['alias_page_id']);
			
				$a_Node = array();
				$a_Node['active'] = false;
				$a_Node['name'] = $menu['name'];
				$a_Node['comment'] = $menu['comment'];					
				$a_Node['id'] = $menu['menu_id'];
				$a_Node['page_id'] = $menu['page_id'];

				$a_Node['url'] = $page ? APPLICATION_URL_SHORT.'/'.$page['short_url'] : '';
				$a_Node['target'] = '_self';
				$a_Node['visible_in_menu'] = $menu['visible_in_menu'];
	
				if($active == $a_Node['id'])
					$a_Node['active'] = true;																
			break;							
		}		
		
		return $a_Node;
	}
	
	private function _getMenu($name = false, $status = false){
		try {
			$a_toReturn = array();
			if($name){
				$select = $this->DB->select();
				$select->from(array('m' => 'm_cms_menu'), '*');
//				if($status)
//				$select->joinLeft(array('p'=> 'm_cms_pages'), 'm.page_id = p.id', array());
				$select->where('m.menu_name = ?', $name);
				$res = $this->DB->query($select)->fetchAll();
				if(!$res)
					return array();
				$a_toReturn[] = array('menu' => $res[0], 'nodes' => $this->getMenuItems($res[0]['id'], $status));
				
			} else {
				$a_Menus = $this->getMenus();										
				foreach($a_Menus as $menu){
					$a = array();
					$a['menu'] = $menu;
					$a['nodes'] = $this->getMenuItems($menu['id'], $status);				
					$a_toReturn[] = $a;
				}
			}
			return $a_toReturn;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}	
	}
	
	public function getMenus(){
		try {
			$select = $this->DB->select();						
			$select->from(array('m' => 'm_cms_menu'), '*');				
			$select->where('m.menu_type = ?', 'menu');
			$select->order('m.order_id ASC');			
			return $this->DB->query($select)->fetchAll();
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}	
	}
	
	public function getMenuItems($rootnode, $status = false){
		try {
			$select = $this->DB->select();						
			$select->from(array('m' => 'm_cms_menu'), array('*', 'menu_id' => 'id'));
			$select->joinLeft(array('p'=> 'm_cms_pages'), 'm.page_id = p.id', '*');									
			$select->where('m.parent_id = ?', $rootnode);
			if($status)
				$select->where('p.status = ?', true);
			$select->where('p.is_predefined <> ?', true);							
			$select->order('m.order_id ASC');	
					
			return $this->DB->query($select)->fetchAll();
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}	
	}	
	
	public function getActiveNode($menu){
		$nodes = isset($menu['nodes']) ? $menu['nodes'] : $menu;
		foreach($nodes as $node)
			if(isset($node['active']) && $node['active'])
				return $node;
		return false;
	}
	
	public function getActiveNodeByShortUrl($shorturl){
		try {
			$select = $this->DB->select();						
			$select->from(array('p' => 'm_cms_pages'), '*');
			$select->joinLeft(array('m'=> 'm_cms_menu'), 'm.page_id = p.id', array('*', 'menu_id' => 'id'));									
			$select->where('p.short_url = ?', $shorturl);
			$select->where('p.status = ?', true);
			
			$res = $this->DB->query($select)->fetchAll();
			return $res ? $res[0] : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}	
	}

	public function getActiveNodeById($id){
		try {
			$select = $this->DB->select();						
			$select->from(array('p' => 'm_cms_pages'), '*');
			$select->joinLeft(array('m'=> 'm_cms_menu'), 'm.page_id = p.id', array('*', 'menu_id' => 'id'));									
			$select->where('p.id = ?', $id);
			$select->where('p.status = ?', true);
			
			$res = $this->DB->query($select)->fetchAll();
			return $res ? $res[0] : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}	
	}
		
}

