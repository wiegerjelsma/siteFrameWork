<?php
/**
 * @name	m_site_ImagesException
 * @author 	wiegerjelsma
 * @version	1.0 2012-03-27 15:56:03
 */
class m_site_ImagesException extends m_site_ModuleException {}

/**
 * @name	m_site_Images
 * @author 	wiegerjelsma
 * @version	1.0 2012-03-27 15:56:03
 */
class m_site_Images extends m_site_Module {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-03-27 15:56:03
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	/**
	 * @name	delete
 	 * @desc	Verwijder een record
 	 */	 
	public function delete($id){
		try {		
			$this->DB->delete($this->cfg['db']['tables']['basetable'], array("id = '".$id."'"));		
			return true;	
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}
	}	
}
