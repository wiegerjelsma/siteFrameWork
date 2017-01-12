<?php
/**
 * @name	c_site_back_FilesException
 * @author 	wiegerjelsma
 * @version	1.0 2012-12-15 11:08:47
 */
class c_site_back_FilesException extends c_site_back_ControllerException {}

/**
 * @name	c_site_back_Files
 * @author 	wiegerjelsma
 * @version	1.0 2012-12-15 11:08:47
 */
class c_site_back_Files extends c_site_back_Controller {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-12-15 11:08:47
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	
 	public function view($a_Params = array()){
 		parent::view(array('group' => ''));
 	}	
	
	/**
	 * @name	handleFileUpload
	 * @desc	We gaan de file vanuit de tmp directory naar de src directory verplaatsen. Vanuit de src wordt hij door de cron opgepakt en geresized.
 	 */	  	
 	protected function handleFileUpload(){
 	
 		unset($this->a_Post['filename_displayed']); 

 		// We hebben een file geupload 		
 		$a_ErrMsg = array();
 		foreach($this->cfg['form'] as $field => $a_Params){ 		
 			if(isset($a_Params['type']) && $a_Params['type'] == 'fileupload'){ 			 			
				if(isset($this->a_Files[$field]['name']) && $this->a_Files[$field]['name']){					
					$this->a_Post['filename'] = str_replace(' ','_', md5(date('Ymdhis')).'-'.$this->a_Files[$field]['name']);
					$this->a_Post['type'] = $this->a_Files[$field]['type'];
					$this->a_Post['size'] = $this->a_Files[$field]['size'];	
					$this->a_Post['resize'] = ($this->a_Files[$field]['type'] == 'image/jpeg' or $this->a_Files[$field]['type'] == 'image/pjpeg') ? true : false;							
					$this->a_Post['src'] = file_get_contents($this->a_Files[$field]['tmp_name']);
				}
 			}		 	 	 			
 		}
 		if(count($a_ErrMsg) >= 1){
 			foreach($a_ErrMsg as $msg)
	 			dump("[FILEUPLOAD ERROR] :: ". $msg);
	 		return false;
	 	}							 	
	 	return true;
 	}	
}
