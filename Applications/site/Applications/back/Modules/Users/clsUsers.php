<?php
/**
 * @name	m_site_back_UsersException
 * @author 	wiegerjelsma
 * @version	1.0 2012-02-22 23:26:44
 */
class m_site_back_UsersException extends m_site_back_ModuleException {}

/**
 * @name	m_site_back_Users
 * @author 	wiegerjelsma
 * @version	1.0 2012-02-22 23:26:44
 */
class m_site_back_Users extends m_site_back_Module {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-02-22 23:26:44
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	
	/**
	 * @name	save
 	 * @desc	
 	 * @returns	id van het gewijzigde of toegevoegde record
 	 */	 
	public function save($a_Data){	
		if(isset($a_Data['wachtwoord']) && $a_Data['wachtwoord'])
			$a_Data['wachtwoord'] = md5($a_Data['wachtwoord']);
		else
			unset($a_Data['wachtwoord']); // We gaan het wachtwoord niet overschrijven (het kan bij edit leeggelaten worden)
			
		return parent::save($a_Data);
	}
	
	/**
	 * @name	get
 	 * @desc	generieke get method met voorwaarden voor in de Where
 	 * @params	array()
 	 * @returns	array results or false	 
 	 */	 
	public function get($a_Params = array(), $a_Result = array(), $a_Search = array()){
		try {
			$select = $this->DB->select();						
			if(array_key_exists('distinct', $a_Result) && $a_Result['distinct'])
				$select->distinct();
			$select->from(array('b' => $this->cfg['db']['tables']['basetable']));			
			
			foreach($a_Params as $key => $value)
				$select->where('b.'.$key.' = ?', $value);
				
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