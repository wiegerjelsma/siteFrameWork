<?php
/**
 * @name	m_site_ModuleException
 * @author 	wiegerjelsma
 * @version	1.0 2012-02-22 22:00:39
 */
class m_site_ModuleException extends m_ModuleException {}

/**
 * @name	m_site_Module
 * @author 	wiegerjelsma
 * @version	1.0 2012-02-22 22:00:39
 */
class m_site_Module extends m_Module {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-02-22 22:00:39
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
		$this->setUpSession();		
	}
	
	
	public function formatJstreeSequence($key, $root, $tablesuffix = false, $subkey = false, $subid = false){
		$a_Params = array();
		$a_Params['status'] = true;
		if($subid)
			$a_Params[$subkey] = $subid;
					
		$a_Items = $this->get($a_Params, array('order' => 'sequence_id ASC'), array(), $tablesuffix);		
		$a_Json = array();
		if($a_Items){					
			foreach($a_Items as $item){
				$a_Node = array();
				$a_Node['data'] = $item[$key];			
				$a_Node['attr']['id'] = $item['id'];
				$a_Node['attr']['sequence_id'] = $item['sequence_id'];			
				$a_Node['attr']['rel'] = 'content';
				$a_Node['attr']['text'] = $item[$key];
				$a_Json[] = $a_Node;	
			}
		}
		
		$a_Node = array();
		$a_Node['data'] = ucfirst($root);			
		$a_Node['attr']['id'] = 'root_node';
		$a_Node['attr']['text'] = ucfirst($root);
		$a_Node['attr']['rel'] = 'folder';
		$a_Node['id'] = 'root_node';
						
		$a_Node['children'] = $a_Json;
		$a_Json = array();
		$a_Json[] = $a_Node;
		return $a_Json;
	}
	
	public function updateSequence($a_Params, $tablesuffix = false){
		try {			
			$node_id = $a_Params['node_id'];
			$before_id = $a_Params['before_id'];
			$after_id = $a_Params['after_id'];
			$position = $a_Params['position'];
			
			$this->DB->beginTransaction();
			
			if($before_id)
				$beforeNode = $this->getOne($before_id, array(), $tablesuffix);
			if($after_id)
				$afterNode = $this->getOne($after_id, array(), $tablesuffix);
							
			$basetable = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));		
			$table = $tablesuffix ? $basetable.'-'.$tablesuffix : $basetable;			

			// Het sequence_id ophogen met 1 voor de nodes met dezelfde parent_id			
			$a_Data = array();
			$a_Where = array();			
			$a_Data['sequence_id'] = new Zend_Db_Expr('sequence_id+1');
			$a_Where[] = $before_id ? "sequence_id >= ".$beforeNode['sequence_id'] : ($after_id ? "sequence_id > ".$afterNode['sequence_id'] : "sequence_id >= 1");
				
			$this->DB->update($table, $a_Data, $a_Where);
			
			// De verplaatste node voorzien van de juiste order_id en parent_id
			$a_Data = array();
			$a_Where = array();
			$a_Where[] = "id = '".$node_id."'";
			$a_Data['sequence_id'] = $before_id ? $beforeNode['sequence_id'] : ($after_id ? $afterNode['sequence_id']+1 : 1);
			$a_Data['edit_user_id'] = $this->SESSION->read('loggedin_userid');
			$a_Data['edit'] = date('Y-m-d H:i:s');				

			$this->DB->update($table, $a_Data, $a_Where);

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
				'added_by_user' => "CONCAT(b.added, '[split]', ua.gebruikersnaam)",
				'edit_by_user' => "CONCAT(b.edit, '[split]', ue.gebruikersnaam)")
			);	
			$select->joinLeft(array('ua'=> 'm_users'), 'b.added_user_id = ua.id', array());					
			$select->joinLeft(array('ue'=> 'm_users'), 'b.edit_user_id = ue.id', array());								
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
	 * @name	getOne
 	 * @desc	generieke get method
 	 * @params	id 	 
 	 * @returns	1 result (array) or false	 	 
 	 */	  
	public function getOneByShortUrl($shorturl, $a_Params = array(), $tablesuffix = false){
		try {
			$basetable = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));		
			$table = $tablesuffix ? $basetable.'-'.$tablesuffix : $basetable;
			$select = $this->DB->select();						
			$select->from(array('b' => $table), array(
				'*', 
				'added_by_user' => "CONCAT(b.added, '[split]', ua.gebruikersnaam)",
				'edit_by_user' => "CONCAT(b.edit, '[split]', ue.gebruikersnaam)")
			);	
			$select->joinLeft(array('ua'=> 'm_users'), 'b.added_user_id = ua.id', array());					
			$select->joinLeft(array('ue'=> 'm_users'), 'b.edit_user_id = ue.id', array());								
			foreach($a_Params as $key => $value)
				$select->where('b.'.$key.' = ?', $value);						
			$select->where('b.short_url = ?', $shorturl);
			$a_Res = $this->DB->query($select)->fetchAll();
			return (count($a_Res)>0) ? $a_Res[0] : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}	
	}		
	
	
	/**
	 * @name	get
 	 * @desc	generieke get method met voorwaarden voor in de Where
 	 * @params	array()
 	 * @returns	array results or false	 
 	 */	 
	public function get($a_Params = array(), $a_Result = array(), $a_Search = array(), $tablesuffix = false){
		try {
			$basetable = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));		
		
			$select = $this->DB->select();						
			if(array_key_exists('distinct', $a_Result) && $a_Result['distinct'])
				$select->distinct();
				
			$table = $tablesuffix ? $basetable.'-'.$tablesuffix : $basetable;	
			$select->from(array('b' => $table));			
			
			foreach($a_Params as $key => $value)
				$select->where('b.'.$key.' = ?', $value);
							
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
				$select->limit($count, $page-1);	
				
			if(array_key_exists('order', $a_Result)){
				$a_Elements = (!is_array($a_Result['order'])) ? explode(',', $a_Result['order']) : $a_Result['order'];
				$select->order($a_Elements);	
			}
				
			if(array_key_exists('group', $a_Result))
				$select->group($a_Result['group']);	
		
			$a_Res = $this->DB->query($select)->fetchAll();
			
			return (count($a_Res)>0) ? $a_Res : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}	
	}
	
	
	/**
	 * @name	save
 	 * @desc	generieke save method.
 	 * @returns	id van het gewijzigde of toegevoegde record
 	 */	 
	public function registerImageForTrash($imagename, $controller){
		try {	
			$a_Data['image'] = $imagename;
			$a_Data['controller'] = $controller;
				
			$this->DB->insert($this->cfg['db']['tables']['images'], $a_Data);
			$id = $this->DB->lastInsertId();
			return $id;
   		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}	
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
				unset($a_Data['id']);
				$a_Data['added_user_id'] = $user_id;	
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
 	 * @desc	Deze functie wordt vanuit de frontend aangeroepen om een record toe te voegen
 	 *			Dus zonder de back end properties (added, addedByUser etc)
 	 */	 
	public function add($a_Data){
		try {	
			$table = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));		
			if(isset($a_Data['id']) && $a_Data['id']){
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
			$obj_History->trackEvent(CONTROLLER_NAME, $action, 'id', $id);
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
	public function delete($id, $a_Images = array()){

		try {
			$table = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));				
				
			if(count($a_Images)> 0){
				$a_Record = $this->getOne($id);
				foreach($a_Images as $image){
					$key = $image['key'];
					$directorykey = isset($image['directorykey']) ? $image['directorykey'] : $this->myname;
					if(isset($a_Record[$key]) && $a_Record[$key])
						$this->registerImageForTrash($a_Record[$key], $directorykey);
				}
			}
		
			$this->DB->delete($table, array("id = '".$id."'"));			
						
			$obj_History = Loader::LoadModule('History');
			$user_id = $this->SESSION->read('loggedin_userid');
			if($user_id)
				$obj_History->trackEvent(CONTROLLER_NAME, 'delete', 'id :: user_id', $id.' :: '.$user_id);
			else
				$obj_History->trackEvent(CONTROLLER_NAME, 'delete', 'id', $id);	
			return true;	
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}
	}
	
	public function deleteSubRecords($id, $tablesuffix, $deleteforsubtabelid = false, $a_Images = array()){
	
		try {
			$basetable = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));				
				
			$table = $basetable.'-'.$tablesuffix;
			
			if(count($a_Images)> 0){
				$a_SubRecords = $this->get(array($deleteforsubtabelid => $id), array(), array(), $tablesuffix);
				foreach($a_SubRecords as $a_Record){						
					foreach($a_Images as $image){
						$key = $image['key'];
						$directorykey = isset($image['directorykey']) ? $image['directorykey'] : $this->myname;
						if(isset($a_Record[$key]) && $a_Record[$key])
							$this->registerImageForTrash($a_Record[$key], $directorykey);
					}
				}
			}
			if($deleteforsubtabelid)		
				$this->DB->delete($basetable.'-'.$tablesuffix, array($deleteforsubtabelid." = '".$id."'"));
						
			$obj_History = Loader::LoadModule('History');
			$user_id = $this->SESSION->read('loggedin_userid');
			if($user_id)
				$obj_History->trackEvent(CONTROLLER_NAME.'-'.$tablesuffix, 'delete subrecords', 'id :: user_id', $id.' :: '.$user_id);
			else
				$obj_History->trackEvent(CONTROLLER_NAME.'-'.$tablesuffix, 'delete subrecords', 'id', $id);	
			return true;	
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}	
	}
	
	
	public function deleteSubRecord($id, $tablesuffix, $a_Images = array()){
		try {
			$basetable = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));				
				
			$table = $basetable.'-'.$tablesuffix;
			
			if(count($a_Images)> 0){
				$a_Record = $this->getOne($id, array(), $tablesuffix);
				
				foreach($a_Images as $image){
					$key = $image['key'];
					$directorykey = isset($image['directorykey']) ? $image['directorykey'] : $this->myname;
					if(isset($a_Record[$key]) && $a_Record[$key])
						$this->registerImageForTrash($a_Record[$key], $directorykey);
				}
			}
		
			$this->DB->delete($basetable.'-'.$tablesuffix, array("id = '".$id."'"));
						
			$obj_History = Loader::LoadModule('History');
			$user_id = $this->SESSION->read('loggedin_userid');
			if($user_id)
				$obj_History->trackEvent(CONTROLLER_NAME.'-'.$tablesuffix, 'delete subrecords', 'id :: user_id', $id.' :: '.$user_id);
			else
				$obj_History->trackEvent(CONTROLLER_NAME.'-'.$tablesuffix, 'delete subrecords', 'id', $id);	
			return true;	
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
					if(!$this->delete($record['id']))
						throw new Exception('Unable to delete record ('.$record['id'].')');
			return true;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}
	}
	
		
	
	/**
	 * @name	getDataHeader
 	 * @desc	Haal de DB gegevens op
 	 */	 
	public function getColumns($tablesuffix = false){
		$basetable = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));				
	
		$table = $tablesuffix ? $basetable.'-'.$tablesuffix : $basetable;
		$stmt = $this->DB->query('SHOW COLUMNS FROM `'.$table.'`');
		return $stmt->fetchAll();	
	}
	
	/**
	 * @name	getItemsForWysiwyg
 	 * @desc	Haal de records op waarnaar we kunnen linken in de wysiwyg editor
 	 */	 
	public function getItemsForWysiwyg(){
		try {		
			return $this->get();
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}
	}			
}
