<?php
/**
 * @name	m_TemplateException
 * @author 	wiegerjelsma
 */
class m_HistoryException extends m_ModuleException {}

/**
 * @name	m_Messages
 * @author 	wiegerjelsma
 */
class m_History extends m_Module {			
	
	protected $_clientip;
	
	
	/**
	 * @name	init 
	 */
	public function init(){
		parent::init();
		$this->setUpSession();
		
		$this->_clientip = $this->SESSION->read('_clientip') ? $this->SESSION->read('_clientip') : false;
		if(!$this->_clientip){
			$obj_Client = Loader::Load('Client');
			$this->_clientip = $obj_Client->getIp();
			$this->SESSION->write('_clientip', $this->_clientip);
		} 
	}
	
	
	/**
	 * @name	trackPageView
	 */
	public function trackPageView($a_PageView = false){
		try {
			if(!$a_PageView){
				$a_PageView['session_id'] = $this->SESSION->getID();
				$a_PageView['url_protocol'] = defined('PROTOCOL') ? PROTOCOL : '';
				$a_PageView['url_subdomain'] = defined('SUBDOMAIN') ? SUBDOMAIN : '';
				$a_PageView['url_domain'] = defined('DOMAIN') ? DOMAIN : '';
				$a_PageView['url_tld'] = defined('TLD') ? TLD : '';
				$a_PageView['url_controller'] = defined('CONTROLLER_NAME') ? CONTROLLER_NAME : '';
				$a_PageView['url_function'] = defined('FUNCTION_NAME') ? FUNCTION_NAME : '';
				$a_PageView['url_id'] = defined('ID_NAME') ? ID_NAME : '';
				$a_PageView['url_qs'] = defined('QUERYSTRING') ? QUERYSTRING : '';
				$a_PageView['client_ip'] = defined("IS_CRON") && IS_CRON ? '' : ($this->_clientip ? $this->_clientip : '');
				$a_PageView['client_language'] = defined('LANGUAGE_NAME') ? LANGUAGE_NAME : '';
				$a_PageView['added'] = date('Y-m-d H:i:s');
				$a_PageView['http_referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : false;
				
			}				
							
//			$this->DB->insert($this->cfg['db']['tables']['M_history_pageviews'], $a_PageView);
			return true;
   		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($a_PageView,'$a_PageView');
			return false;
		}
	}

	
	/**
	 * @name	trackEvent
	 */
	public function trackEvent($category, $action, $label = false, $value = false, $user_id = false){
		try {
		
			$a_User = $this->SESSION->read('User');
			$a_Event['session_id'] = $this->SESSION->getID();
			$a_Event['event_category'] = ucfirst($category);
			$a_Event['event_action'] = ucfirst($action);
			$a_Event['event_label'] = ucfirst($label);
			$a_Event['event_value'] = $value;			
			$a_Event['user_id'] = $user_id ? $user_id : ($this->SESSION->read('loggedin_userid') ? $this->SESSION->read('loggedin_userid') : ($a_User ? $a_User['id'] : false));
			$a_Event['client_ip'] = $this->_clientip;
			$a_Event['added'] = date('Y-m-d H:i:s');		
				
//			$this->DB->insert($this->cfg['db']['tables']['M_history_events'], $a_Event);
			return true;
   		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($a_Event,'$a_Event');
			
			return false;
		}
	} 	
}