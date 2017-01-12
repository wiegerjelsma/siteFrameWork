<?php
/**
 * @name	m_PhpException
 * @author 	wiegerjelsma
 */
class m_PhpException extends m_TemplateException {}

/**
 * @name	m_Php
 * @author 	wiegerjelsma
 */
class m_Php extends m_Template {
	
	
	/**
	 * @name	init()
	 */
	public function init(){
		parent::init();
	}
	
	
	/**
	 * @name	renderTemplate()
	 */
	protected function renderTemplate($tpl){        
//		dump($this->a_Vars);
		foreach($this->a_Vars as $key => $value)
        	$this->$key = $value; 
        
        ob_start();	 
		eval("?>" . Loader::loadTemplate($tpl));
        $ob_contents = ob_get_contents();
        ob_end_clean();
        return $ob_contents;		
	}
	
	
	/**
	 * @name	includeTpl()
	 */
	protected function includeTpl($tpl, $a_Vars = false){
		if($a_Vars)
			foreach($a_Vars as $__key => $__value)
        		$$__key = $__value; 	
        		 		        		
		eval("?>" . Loader::loadTemplate($tpl));
		return;
	}
}