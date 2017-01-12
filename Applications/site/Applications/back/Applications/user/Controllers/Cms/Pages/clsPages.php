<?php
/**
 * @name	c_av_back_user_PagesException
 * @author 	wiegerjelsma
 * @version	1.0 2012-12-02 17:51:01
 */
class c_site_back_user_PagesException extends c_site_back_PagesException {}

/**
 * @name	c_av_back_user_Pages
 * @author 	wiegerjelsma
 * @version	1.0 2012-12-02 17:51:01
 */
class c_site_back_user_Pages extends c_site_back_Pages {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-12-02 17:51:01
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	
	/**
	 * @name	form
	 */
	protected function form($subtitel, $id = false, $a_Dataset = false, $display = true){
		$this->setUpModule();
		$a_Dataset = $a_Dataset ? $a_Dataset : ($id ? ((FUNCTION_PARAM == 'sub') ? $this->obj_Module->getOne($id, array(), $this->cfg['sub']['tabelsuffix']) : $this->obj_Module->getOne($id)) : false);
		
		if(isset($a_Dataset['locked_content']) && $a_Dataset['locked_content']){
			$a_Keys = array('type','content_title','content_body','virtual_page_id','virtual_int_url_controller','virtual_int_url_function','virtual_int_url_id','redirect_ext_url','redirect_int_shorturl','virtual_page_id');
			foreach($a_Keys as $key)
				if(isset($this->cfg['form'][$key]))
					$this->cfg['form'][$key]['readonly'] = true;
		}
		
		// Als de pagina Virtual is
		if($a_Dataset['virtual_int_url_controller']){
			unset($this->cfg['form']['block_virtual']);			
			unset($this->cfg['form']['divider3']);
			$this->cfg['form']['virtual_page_id']['type'] = 'hidden';
		}		

		parent::form($subtitel, $id, $a_Dataset, $display);
	}
	
	
	protected function validate($addoredit = false){
		switch($this->a_Post['type']){
			case 'content':
				$this->cfg['form']['virtual_int_url_controller']['required'] = false;
				$this->cfg['form']['virtual_int_url_function']['required'] = false;
				$this->cfg['form']['virtual_int_url_id']['required'] = false;
				$this->cfg['form']['virtual_page_id']['required'] = false;
				$this->cfg['form']['redirect_ext_url']['required'] = false;
				$this->cfg['form']['alias_page_id']['required'] = false;
				
			break;
			case 'virtual':
				$this->cfg['form']['alias_page_id']['required'] = false;
				$this->cfg['form']['content_title']['required'] = false;
				$this->cfg['form']['content_body']['required'] = false;
				$this->cfg['form']['redirect_ext_url']['required'] = false;
				
				if($this->a_Post['virtual_int_url_controller'])			
					$this->cfg['form']['virtual_page_id']['required'] = false;
					
				$this->cfg['form']['short_url']['required'] = false;				
			break;
			case 'redirect':
				$this->cfg['form']['alias_page_id']['required'] = false;
				$this->cfg['form']['virtual_int_url_controller']['required'] = false;
				$this->cfg['form']['virtual_int_url_function']['required'] = false;
				$this->cfg['form']['virtual_int_url_id']['required'] = false;
				$this->cfg['form']['virtual_page_id']['required'] = false;
				$this->cfg['form']['content_title']['required'] = false;
				$this->cfg['form']['content_body']['required'] = false;	
				$this->cfg['form']['short_url']['required'] = false;
			break;
			case 'alias':
				$this->cfg['form']['virtual_int_url_controller']['required'] = false;
				$this->cfg['form']['virtual_int_url_function']['required'] = false;
				$this->cfg['form']['virtual_int_url_id']['required'] = false;
				$this->cfg['form']['virtual_page_id']['required'] = false;
				$this->cfg['form']['content_title']['required'] = false;
				$this->cfg['form']['content_body']['required'] = false;	
				$this->cfg['form']['redirect_ext_url']['required'] = false;				
				$this->cfg['form']['short_url']['required'] = false;								
			break;
			case 'template':
				$this->cfg['form']['alias_page_id']['required'] = false;
				$this->cfg['form']['virtual_int_url_controller']['required'] = false;
				$this->cfg['form']['virtual_int_url_function']['required'] = false;
				$this->cfg['form']['virtual_int_url_id']['required'] = false;
				$this->cfg['form']['virtual_page_id']['required'] = false;
				$this->cfg['form']['redirect_ext_url']['required'] = false;

				$this->cfg['form']['custom_template']['required'] = true;								
			break;					
		}
		return parent::validate($addoredit);	
	}
}
