<?php
/**
 * @name	m_av_MenuException
 * @author 	wiegerjelsma
 * @version	1.0 2012-11-03 17:54:56
 */
class m_site_MenuException extends m_site_CmsException {}

/**
 * @name	m_av_Menu
 * @author 	wiegerjelsma
 * @version	1.0 2012-11-03 17:54:56
 */
class m_site_Menu extends m_site_Cms {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-11-03 17:54:56
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	/**
	 * @name	moveNode
	 * @desc	We gaan een menu item opnieuw plaatsen (new parent en volgorde_id)
	 */
	public function moveNode($a_Params){
		try {			
			$node_id = $a_Params['node_id'];
			$parent_id = $a_Params['parent_id'];
			$before_id = $a_Params['before_id'];
			$after_id = $a_Params['after_id'];
			$position = $a_Params['position'];
			
			$this->DB->beginTransaction();
			
			if($before_id)
				$beforeNode = $this->getOne($before_id);
			if($after_id)
				$afterNode = $this->getOne($after_id);
							
			$basetable = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));		

			// Het order_id ophogen met 1 voor de nodes met dezelfde parent_id			
			$a_Data = array();
			$a_Where = array();			
			$a_Data['order_id'] = new Zend_Db_Expr('order_id+1');
			$a_Where[] = $parent_id ? "parent_id = ". $parent_id : "parent_id = 0"; 
			$a_Where[] = $before_id ? "order_id >= ".$beforeNode['order_id'] : ($after_id ? "order_id > ".$afterNode['order_id'] : "order_id >= 1");
				
			$this->DB->update($basetable, $a_Data, $a_Where);										
			
			// De verplaatste node voorzien van de juiste order_id en parent_id
			$a_Data = array();
			$a_Where = array();
			$a_Where[] = "id = '".$node_id."'";
			$a_Data['order_id'] = $before_id ? $beforeNode['order_id'] : ($after_id ? $afterNode['order_id']+1 : 1);
			$a_Data['parent_id'] = $parent_id ? $parent_id : false;
			$a_Data['edit_user_id'] = $this->SESSION->read('loggedin_userid');
			$a_Data['edit'] = date('Y-m-d H:i:s');				

			$this->DB->update($basetable, $a_Data, $a_Where);

			$this->DB->commit();
		
			return true;
		} catch(Exception $e){
			$this->DB->rollBack();
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
		}	
		return false;
	}
	
	
	/**
	 * @name	getOne
 	 * @desc	generieke get method
 	 * @params	id 	 
 	 * @returns	1 result (array) or false	 	 
 	 */	  
	public function getOne($id, $a_Params = array(), $tablesuffix = false){
		try {
			$basetable = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));		
			$table = $tablesuffix ? $basetable.'-'.$tablesuffix : $basetable;
			$select = $this->DB->select();						
			$select->from(array('b' => $table), array(
				'*', 
				'edit_by_user' => "CONCAT(b.edit, '[split]', u.gebruikersnaam)")
			);	
			$select->joinLeft(array('u'=> 'm_users'), 'b.edit_user_id = u.id', array());					
			foreach($a_Params as $key => $value)
				$select->where('b.'.$key.' = ?', $value);						
			$select->where('b.id = ?', $id);
			$a_Res = $this->DB->query($select)->fetchAll();
			return (count($a_Res)>0) ? $a_Res[0] : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}	
	}	
		
	
	/**
	 * @name	get
	 * @desc	We gaan op een recursieve manier de menu items ophalen.
	 */
	public function get($a_Params = array(), $a_Result = array(), $a_Search = array(), $tablesuffix = false){
		//dump($this->myname,'$this->myname');
		$basetable = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));		
		
		$select = $this->DB->select();						
		if(array_key_exists('distinct', $a_Result) && $a_Result['distinct'])
			$select->distinct();
				
		$table = $tablesuffix ? $basetable.'-'.$tablesuffix : $basetable;	
		$select->from(array('b' => $table));
		$select->joinLeft(array('p'=> 'm_cms_pages'), 'b.page_id = p.id', array('name','type','locked_menu','is_predefined','status'));
			
		foreach($a_Params as $key => $value){
			$key = preg_match('/^[a-z]+\./', $key) ? $key : 'b.'.$key;
			$select->where($key.' = ?', $value);
		}
							
		$a_SearchString = array();
		foreach($a_Search as $key => $value){
			if($value)
				$a_SearchString[] = 'b.'.$key." LIKE '%".$value."%'";	
		}			
		if(count($a_SearchString) >= 1)
			$select->where(join(' AND ', $a_SearchString));		
			
		$count = array_key_exists('count', $a_Result) ? $a_Result['count'] : false;
		$page = array_key_exists('page', $a_Result) ? $a_Result['page'] : 1;			
		if($count)
			$select->limit($count, $page);	
				
		if(array_key_exists('order', $a_Result))
			$select->order($a_Result['order']);	
				
		if(array_key_exists('group', $a_Result))
			$select->group($a_Result['group']);	
														
		$a_Menu = $this->DB->query($select)->fetchAll();			
		$a_Items = (count($a_Menu)>0) ? $a_Menu : false;
		
		$toReturn = array();
		if($a_Items)
			foreach($a_Items as $item){
				$a_Item = array();
				$a_Item['item'] = $item;
				$a_Item['children'] = $this->get(array('parent_id' => $item['id']), array('order' => 'order_id ASC'));
				$toReturn[] = $a_Item;
			}
		return $toReturn;	
	}
	

	/**
	 * @name	formatJstreeJson
	 * @desc	We gaan de menu array omzetten naar het format dat JsTree vraagt.
	 */
	public function formatJstree($a_Menu){
		$a_Json = array();
		foreach($a_Menu as $node){
			if(!IS_ADMIN){
				if($node['item']['is_predefined'])
					continue;
				if($node['item']['menu_type'] == 'predefined')
					continue;
			}
		
			$a_Node = array();
			
			$rel = $node['item']['menu_type'] == 'page' ? $node['item']['type'] : $node['item']['menu_type'];
			$locked = $node['item']['menu_type'] == 'page' ? 0 : 1;
			if(!$locked)			
				$locked = $node['item']['locked_menu'] ? 1 : 0;
			$locked = IS_ADMIN ? 0 : $locked;
							
			$name = $node['item']['menu_type'] == 'page' ? $node['item']['name'] : $node['item']['menu_name'];
			$rel = $locked && $node['item']['menu_type'] == 'page' ? $rel.'locked' : $rel;
			if($node['item']['is_predefined'] && $node['item']['menu_type'] == 'page')
				$rel = 'predefined';

			if($node['item']['menu_type'] == 'predefined')
				$rel = 'predefinedfolder';

									
			$a_Node['data'] = $name;			
			$a_Node['attr']['id'] = $node['item']['id'];
			$a_Node['attr']['page_id'] = $node['item']['page_id'];			
			$a_Node['attr']['rel'] = $rel;
			$a_Node['attr']['text'] = $name;
			
			if($locked)
				$a_Node['attr']['locked'] = $locked;			
			
			if(isset($node['children']) && $node['children'])
				$a_Node['children'] = $this->formatJstree($node['children']);
			$a_Json[] = $a_Node;
		}
		return $a_Json;
	}
	
	
	/**
	 * @name	save
 	 * @desc	generieke save method.
 	 * @returns	id van het gewijzigde of toegevoegde record
 	 */	 
	public function save($a_Data, $tablesuffix = false){
		try {	
			$basetable = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));		
			$table = $tablesuffix ? $basetable.'-'.$tablesuffix : $basetable;
			$user_id = $this->SESSION->read('loggedin_userid');
			if(isset($a_Data['id']) && $a_Data['id']){
				$a_Data['edit_user_id'] = $user_id;
				$a_Data['edit'] = date('Y-m-d H:i:s');				
				$a_Where[] = "id = '".$a_Data['id']."'";
				$id = $a_Data['id'];
				unset($a_Data['id']);
				$action = 'edit';
				$this->DB->update($table, $a_Data, $a_Where);
			} else {
				$this->DB->insert($table, $a_Data);
				$id = $this->DB->lastInsertId();
				$action = 'add';	
			}
			
			$obj_History = Loader::LoadModule('History');
			if($user_id)
				$obj_History->trackEvent(CONTROLLER_NAME.'-'.$tablesuffix, $action, 'id :: user_id', $id.' :: '.$user_id);
			else
				$obj_History->trackEvent(CONTROLLER_NAME.'-'.$tablesuffix, $action, 'id', $id);
			return $id;
   		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}	
	}	

	
	/**
	 * @name	delete
 	 * @desc	Verwijder een record
 	 */	 
	public function deleteWhere($a_Where = array()){
		try {		
			$a_Records = $this->get($a_Where);
			if($a_Records)
				foreach($a_Records as $record)
					if(!$this->delete($record['item']['id']))
						throw new Exception('Unable to delete record ('.$record['itemw']['id'].')');
			return true;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}
	}	
	
	
	/**
	 * @name	getItemsForWysiwyg
 	 * @desc	Haal de records op waarnaar we kunnen linken in de wysiwyg editor
 	 */	 
	public function getItemsForWysiwyg(){
		try {		
			return $this->get(array('menu_type' => 'page', 'p.is_predefined' => '0'));
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}
	}
}
