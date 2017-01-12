<?php
/**
 * @name	c_site_back_FormsException
 * @author 	wiegerjelsma
 * @version	1.0 2013-04-08 16:14:51
 */
class c_site_back_FormsException extends c_site_back_ControllerException {}

/**
 * @name	c_site_back_Forms
 * @author 	wiegerjelsma
 * @version	1.0 2013-04-08 16:14:51
 */
class c_site_back_Forms extends c_site_back_Controller {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2013-04-08 16:14:51
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	protected function validate($addoredit = false){
		if(FUNCTION_PARAM != 'sub')
			return parent::validate($addoredit);
			
		switch($this->a_Post['type']){
			case 'text':
				$a_Names = array('label', 'required');
				$cfg['sub']['form']['label']['required'] = true;								
			break;			
			case 'textarea':
				$a_Names = array('label', 'required');
				$this->cfg['sub']['form']['label']['required'] = true;
			break;			
			case 'pulldown':
				$a_Names = array('label', 'required','value','options');
				$this->cfg['sub']['form']['label']['required'] = true;
				$this->cfg['sub']['form']['value']['required'] = true;								
			break;						
			case 'radio':
				$a_Names = array('label', 'required','value','options');
				$this->cfg['sub']['form']['label']['required'] = true;
				$this->cfg['sub']['form']['value']['required'] = true;
			break;						
			case 'checkbox':
				$a_Names = array('label', 'required','value','sublabel');
				$this->cfg['sub']['form']['label']['required'] = true;
			break;			
			case 'date':
				$a_Names = array('label', 'required');
				$this->cfg['sub']['form']['label']['required'] = true;
			break;			
			case 'comment':
				$a_Names = array('value');
				$this->cfg['sub']['form']['value']['required'] = true;
			break;			
			case 'seperator':
			break;			
			case 'header':
				$a_Names = array('value');
				$this->cfg['sub']['form']['value']['required'] = true;			
			break;						
		}
		
		foreach($this->cfg['sub']['form']['type']['values'] as $type => $names){
			if($type != $this->a_Post['type']){
				foreach(array('label', 'required','value','sublabel','options') as $name){
					unset($this->a_Post[$name.'_'.$type]);
					unset($this->cfg['sub']['form'][$name.'_'.$type]);
				}
			} else {
				if(isset($a_Names))
					foreach($a_Names as $name){
						$this->a_Post[$name] = isset($this->a_Post[$name.'_'.$type]) ? $this->a_Post[$name.'_'.$type] : false;
						$this->cfg['sub']['form'][$name] = $this->cfg['sub']['form'][$name.'_'.$type];
						unset($this->a_Post[$name.'_'.$type]);
						unset($this->cfg['sub']['form'][$name.'_'.$type]);
					}			
			}
		}
		
		$this->a_Post['required'] = isset($this->a_Post['required']) ? $this->a_Post['required'] : false;
		$this->a_Post['fieldname'] = strToLower(str_replace(' ','_',$this->a_Post['name']));

		return parent::validate($addoredit);
	}	
	
 	protected function getDataSet($a_Dataset = array(), $id = false){
		$a_Dataset = $a_Dataset ? $a_Dataset : ($id ? ((FUNCTION_PARAM == 'sub') ? $this->obj_Module->getOne($id, array(), $this->cfg['sub']['tabelsuffix']) : $this->obj_Module->getOne($id)) : false); 	
		
		if(FUNCTION_PARAM == 'sub' && $id){
			switch($a_Dataset['type']){
				case 'text':
				case 'textarea':
				case 'date':
					$a_Names = array('label', 'required');
				break;
				case 'pulldown':
				case 'radio';
					$a_Names = array('label', 'required','value','options');
				break;
				case 'header':
				case 'comment':
					$a_Names = array('value');
				break;
				case 'checkbox':
					$a_Names = array('label', 'required','value','sublabel');	
				break;
			}	
			if(isset($a_Names))		
				foreach($a_Names as $name){
					$a_Dataset[$name.'_'.$a_Dataset['type']] = $a_Dataset[$name];
					unset($a_Dataset[$name]);
				}			
		}

		return $a_Dataset;
 	}
	
}
