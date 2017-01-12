<?php
/**
 * @name	m_av_PagesException
 * @author 	wiegerjelsma
 * @version	1.0 2012-11-03 17:55:01
 */
class m_site_PagesException extends m_site_CmsException {}

/**
 * @name	m_av_Pages
 * @author 	wiegerjelsma
 * @version	1.0 2012-11-03 17:55:01
 */
class m_site_Pages extends m_site_Cms {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-11-03 17:55:01
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	/**
	 * @name	save
 	 * @desc	generieke save method.
 	 * @returns	id van het gewijzigde of toegevoegde record
 	 */	 
	public function save($a_Data, $tablesuffix = false){
		try {				
			if(isset($a_Data['id']) && $a_Data['id'])
				return parent::save($a_Data, $tablesuffix);				
			else {			
				$this->DB->beginTransaction();
				$page_id = parent::save($a_Data, $tablesuffix);
			
				$obj_Menu = Loader::loadModule('Cms.Menu');
				if(isset($a_Data['is_predefined']) && $a_Data['is_predefined'])
					$box = $obj_Menu->get(array('menu_type' => 'predefined'));
				else
					$box = $obj_Menu->get(array('menu_type' => 'draft'));
			
				$a_Menu = array();
				$a_Menu['page_id'] = $page_id;
				$a_Menu['parent_id'] = $box[0]['item']['id'];								
			
				$obj_Menu->save($a_Menu);
				$this->DB->commit();
			}
			return true;
		} catch(Exception $e){
			$this->DB->rollBack();
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
		}	
		return false;
	}
 	
 	
	/**
	 * @name	delete
 	 * @desc	Verwijder een record
 	 */	 
	public function delete($id, $tablesuffix = false, $deleteforsubtabel = false, $subid = false){
		try {
			$basetable = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));				
				
			// kijken of we ook een image tabel hebben..
			$table = $tablesuffix ? $basetable.'-'.$tablesuffix : $basetable;
						
			$a_Record = $this->getOne($id, array(), $tablesuffix);
			if(isset($a_Record['image']))
				$this->registerImageForTrash($a_Record['image'], $this->myname);
		
			$this->DB->delete($table, array("id = '".$id."'"));			
			if($deleteforsubtabel)
				$this->DB->delete($this->cfg['db']['tables']['basetable'].'-'.$deleteforsubtabel, array($subid." = '".$id."'"));
				
			$obj_Menu = Loader::loadModule('Cms.Menu');
 			$obj_Menu->deleteWhere(array('page_id' => $id));							
						
/*			$obj_History = Loader::LoadModule('History');
			$user_id = $this->SESSION->read('loggedin_userid');
			if($user_id)
				$obj_History->trackEvent(CONTROLLER_NAME.'-'.$tablesuffix, 'delete', 'id :: user_id', $id.' :: '.$user_id);
			else
				$obj_History->trackEvent(CONTROLLER_NAME.'-'.$tablesuffix, 'delete', 'id', $id);					
*/				
			return true;	
		} catch(Exception $e){
			$this->DB->rollBack();
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}
	} 	
	
	public function getVirtualPageByShortUrl($short_url){
		$virtual_page = $this->get(array('short_url' => $short_url, 'status' => true));
		if($virtual_page)
			return $this->getOne($virtual_page[0]['virtual_page_id'], array('status' => true));
	}
	
	public function getVirtualPageByVirtualPageId($virtual_page_id){
		$a_Res = $this->get(array('virtual_page_id' => $virtual_page_id, 'status' => true));
		return (count($a_Res)>0) ? $a_Res[0] : false;
	}	
	
	public function getPredefinedPage($controller, $function){
		$a_Res = $this->get(array('virtual_int_url_controller' => $controller, 'virtual_int_url_function' => $function, 'type' => 'virtual', 'status' => true));
		return (count($a_Res)>0) ? $a_Res[0] : false;
	}			
}
