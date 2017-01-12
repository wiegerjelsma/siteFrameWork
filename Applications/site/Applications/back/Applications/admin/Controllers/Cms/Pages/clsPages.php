<?php
/**
 * @name	c_av_back_admin_PagesException
 * @author 	wiegerjelsma
 * @version	1.0 2012-12-12 15:00:28
 */
class c_site_back_admin_PagesException extends c_site_back_PagesException {}

/**
 * @name	c_av_back_admin_Pages
 * @author 	wiegerjelsma
 * @version	1.0 2012-12-12 15:00:28
 */
class c_site_back_admin_Pages extends c_site_back_Pages {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-12-12 15:00:28
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	protected function validate($addoredit = false){	
				
		if($this->a_Post['type'] == 'predefined'){
			$this->a_Post['is_predefined'] = true;
			$this->a_Post['locked_content'] = true;
			$this->a_Post['type'] = 'virtual';
			$this->a_Post['short_url'] = '';
			
			$this->a_Post['virtual_int_url_controller'] = $this->a_Post['predefined_int_url_controller'];
			$this->a_Post['virtual_int_url_function'] = $this->a_Post['predefined_int_url_function'];
			$this->a_Post['virtual_int_url_id'] = $this->a_Post['predefined_int_url_id'];			
		}
			
		unset($this->a_Post['predefined_int_url_controller']);
		unset($this->a_Post['predefined_int_url_function']);
		unset($this->a_Post['predefined_int_url_id']);		
		
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
				if($this->a_Post['virtual_int_url_controller'])
					$this->cfg['form']['virtual_page_id']['required'] = false;
				
				$this->cfg['form']['content_title']['required'] = false;
				$this->cfg['form']['content_body']['required'] = false;
				$this->cfg['form']['redirect_ext_url']['required'] = false;								
				$this->cfg['form']['alias_page_id']['required'] = false;
			break;
			case 'redirect':
				$this->cfg['form']['virtual_int_url_controller']['required'] = false;
				$this->cfg['form']['virtual_int_url_function']['required'] = false;
				$this->cfg['form']['virtual_int_url_id']['required'] = false;
				$this->cfg['form']['virtual_page_id']['required'] = false;
				$this->cfg['form']['content_title']['required'] = false;
				$this->cfg['form']['content_body']['required'] = false;
				$this->cfg['form']['alias_page_id']['required'] = false;	
			break;
			case 'template':
				$this->cfg['form']['virtual_int_url_controller']['required'] = false;
				$this->cfg['form']['virtual_int_url_function']['required'] = false;
				$this->cfg['form']['virtual_int_url_id']['required'] = false;
				$this->cfg['form']['virtual_page_id']['required'] = false;
				$this->cfg['form']['redirect_ext_url']['required'] = false;
				$this->cfg['form']['alias_page_id']['required'] = false;
				$this->cfg['form']['custom_template']['required'] = true;								
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
		}
		return parent::validate($addoredit);	
	}		
}
