<?php
/**
 * @name	m_site_back_admin_PagesException
 * @author 	wiegerjelsma
 * @version	1.0 2012-12-14 11:28:30
 */
class m_site_back_admin_PagesException extends m_site_PagesException {}

/**
 * @name	m_site_back_admin_Pages
 * @author 	wiegerjelsma
 * @version	1.0 2012-12-14 11:28:30
 */
class m_site_back_admin_Pages extends m_site_Pages {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-12-14 11:28:30
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
			$basetable = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));		
			$table = $tablesuffix ? $basetable.'-'.$tablesuffix : $basetable;
			$select = $this->DB->select();						
			$select->from(array('b' => $table), array(
				'*', 
				'added_by_user' => "CONCAT(b.added, '[split]', u.gebruikersnaam)",
				'edit_by_user' => "CONCAT(b.edit, '[split]', u.gebruikersnaam)")
			);	
			$select->joinLeft(array('u'=> 'm_users'), 'b.added_user_id = u.id', array());					
			foreach($a_Params as $key => $value)
				$select->where('b.'.$key.' = ?', $value);						
			$select->where('b.id = ?', $id);
			$a_Res = $this->DB->query($select)->fetchAll();
			$page = (count($a_Res)>0) ? $a_Res[0] : false;
			
			if(!$page)
				return $page;
				
			if($page['is_predefined']){
				$page['type'] = 'predefined';
				
				$page['predefined_int_url_controller'] = $page['virtual_int_url_controller'];
				$page['predefined_int_url_function'] = $page['virtual_int_url_function'];
				$page['predefined_int_url_id'] = $page['virtual_int_url_id'];
			
				$page['virtual_int_url_controller'] = '';
				$page['virtual_int_url_function'] = '';
				$page['virtual_int_url_id'] = '';			
			}
			
			return $page;			
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}	
	}	
}
