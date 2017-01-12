<?php
/**
 * @name	m_TemplateException
 * @author 	wiegerjelsma
 */
class m_MessagesException extends m_ModuleException {}

/**
 * @name	m_Messages
 * @author 	wiegerjelsma
 */
class m_Messages extends m_Module {		
	
	protected $a_Messages = array();
	
	
	/**
	 * @name	init 
	 */
	public function init(){
		parent::init();
		$this->setUpSession();
		
		$this->a_Messages = $this->SESSION->read('_messages') ? $this->SESSION->read('_messages') : array();
		$this->assign();
	}
	
	
	/**
	 * @name	assign
	 */
	private function assign(){
		$this->setUpTemplate();
		$this->TPL->assign('Messages', $this->a_Messages);
		$this->SESSION->write('_messages', $this->a_Messages);			
	}
	
	
	/**
	 * @name	push 
	 */
	public function push($msg, $key = 'general', $subkey = false){
   		if($subkey)
   			$this->a_Messages[$subkey][$key][] = $msg;
   		else	   
   			$this->a_Messages[$key][] = $msg;
   		$this->assign();		
	}
	
	
	/**
	 * @name	clean 
	 */
	public function clean(){
		$this->SESSION->clean('_messages');
	}
	
	public function get(){
		return $this->a_Messages;
	}  	
}