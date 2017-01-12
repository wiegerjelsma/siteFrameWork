<?php
/**
 * @name	m_site_back_admin_ModuleException
 * @author 	wiegerjelsma
 * @version	1.0 2012-03-27 11:26:42
 */
class m_site_back_admin_ModuleException extends m_site_back_ModuleException {}

/**
 * @name	m_site_back_admin_Module
 * @author 	wiegerjelsma
 * @version	1.0 2012-03-27 11:26:42
 */
class m_site_back_admin_Module extends m_site_back_Module {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-03-27 11:26:42
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	
	/**
	 * @name	getOne
 	 * @desc	generieke get method
 	 * @params	id 	 
 	 * @returns	1 result (array) or false	 	 
 	 */	  	
	public function getOne($id, $a_Params = array(), $tablesuffix = false){
		try {
			$table = $tablesuffix ? $this->cfg['db']['tables']['basetable'].'-'.$tablesuffix : $this->cfg['db']['tables']['basetable'];
			$select = $this->DB->select();						
			$select->from(array('b' => $table), array(
				'*', 
				'added_by_user' => "CONCAT(b.added, '[split]', u.gebruikersnaam)",
				'edit_by_user' => "CONCAT(b.edit, '[split]', u.gebruikersnaam)")
			);	
			$select->joinLeft(array('u'=> 'm_users'), 'b.added_user_id = u.id', '*');					
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
 	 * @desc	generieke get method met voorwaarden voor in de Where
 	 * @params	array()
 	 * @returns	array results or false	 
 	 */	 
	public function get($a_Params = array(), $a_Result = array(), $a_Search = array(), $tablesuffix = false){
		try {
			$select = $this->DB->select();						
			if(array_key_exists('distinct', $a_Result) && $a_Result['distinct'])
				$select->distinct();
			$table = $tablesuffix ? $this->cfg['db']['tables']['basetable'].'-'.$tablesuffix : $this->cfg['db']['tables']['basetable'];	
			$select->from(array('b' => $table));							
			
			foreach($a_Params as $key => $value)
				$select->where($key.' = ?', $value);
				
			if(defined('IS_USER') && IS_USER)
				$select->where('user_id = ?', USER_ID);			
			
			$a_SearchString = array();
			foreach($a_Search as $key => $value){
				if($value)
					$a_SearchString[] = $key." LIKE '%".$value."%'";	
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
								
			$a_Res = $this->DB->query($select)->fetchAll();
			return (count($a_Res)>0) ? $a_Res : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}	
	}		
}
