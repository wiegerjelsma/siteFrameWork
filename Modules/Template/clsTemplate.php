<?php
/**
 * @name	m_TemplateException
 * @author 	wiegerjelsma
 */
class m_TemplateException extends m_ModuleException {}

/**
 * @name	m_Template
 * @author 	wiegerjelsma
 */
class m_Template extends m_Module {		
	
	protected $a_Vars = array();
	
	public function init(){
		parent::init();
		
		$this->assign('cfg', $this->cfg);
		$this->setUpSession();
	}
	
	
	/**
	 * @name	display()
	 */
	public function display($tpl){		
		// hier zouden we de cache kunnen implementeren..
		if(!defined('TEMPLATE_NAME'))
			define('TEMPLATE_NAME', str_replace('.tpl','',$tpl));
		try {
			$source = $this->renderTemplate($tpl);
		} catch(Exception $e){
			dump("[TEMPLATE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			exit;
		}
		
		$obj_Messages = Loader::loadModule('Messages');
		$obj_Messages->clean();
		
/*		$obj_History = Loader::LoadModule('History');
		$obj_History->trackPageView();*/

		$obj_Statistics = Loader::LoadModule('Statistics');
		$obj_Statistics->savePageView();
		
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past				
		
		echo $source;
	}
	
	
	
	/**
	 * @name	getSource
	 * @desc	returns the rendered tpl
	 */
	public function getSource($tpl){
		try {
			$source = $this->renderTemplate($tpl);
			return $source;
		} catch(Exception $e){
			dump("[TEMPLATE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			throw new Exception('Unable to render template');
		}	
		return false;
	}
	
	
	/**
	 * @name	assign()
	 */
	public function assign($key, $value){
		$this->a_Vars[$key] = $value;		
	}	
	

	/**
	 * @name	get()
	 */
	public function isAssigned($key){
		return isset($this->a_Vars[$key]);
	}	
	
	
	
	/**
	 * @name isTrue
	 */
	protected function isTrue($key){
		if(isset($this->a_Vars[$key]) && $this->a_Vars[$key])
			return true;
		return false;
	}	
	
	public function getVar($key){
		if($this->isTrue($key))
			return $this->a_Vars[$key];
		return false;
	}
	
	
	/**
	 * @name	getTranslation
	 */
	public function getTranslation($key, $a_Replace = false){
		$obj_Translate = Loader::loadModule('Translate');
		$string = $obj_Translate->get($key);
		$string = $string ? $string : '#'.$key.'#';		

		$a_UbbCode['[strong]'] = '<strong>';
		$a_UbbCode['[/strong]'] = '</strong>';
		$a_UbbCode['[italic]'] = '<em>';
		$a_UbbCode['[/italic]'] = '</em>';
		$a_UbbCode['[underline]'] = '<u>';
		$a_UbbCode['[/underline]'] = '</u>';		
		$a_UbbCode['[highlight]'] = '<span class="highlight">';
		$a_UbbCode['[/highlight]'] = '</span>';	
		$a_UbbCode['[link]'] = '<a href="%link%" target="_blank">';	
		$a_UbbCode['[/link]'] = '</a>';
		$a_UbbCode['[linkself]'] = '<a href="%linkself%">';	
		$a_UbbCode['[/linkself]'] = '</a>';

		$a_Search = array();
		$a_Replacement = array();
		foreach($a_UbbCode as $_key => $_value){
			$a_Search[] = $_key;
			$a_Replacement[] = $_value;
		}
		$string = trim(str_replace($a_Search, $a_Replacement, $string));		
		
		
		if($a_Replace){
			$a_Search = array();
			$a_Replacement = array();
			foreach($a_Replace as $_key => $_value){
				$a_Search[] = '%'.$_key.'%';
				$a_Replacement[] = $_value;
			}
			$string = trim(str_replace($a_Search, $a_Replacement, $string));
		}
		return $string;		
	}	
	
	
	/**
	 * @name	format
	 */
	public function format($value, $type, $format = false){
		switch($type){
			default:
				return $value;
			break;
			case 'datetime':
				list($date, $time) = explode(' ', $value);
				list($hour, $min, $sec) = explode(':', $time);										
				list($year, $month, $day) = explode('-', $date);
				$format = $format ? $format : '%d %b %Y %H:%M:%S';				
				return strftime($format, mktime($hour, $min, $sec, $month, $day, $year));
			break;
			case 'date':
				list($year, $month, $day) = explode('-', $value);
				$format = $format ? $format : '%d %b %Y';				
				return strftime($format, mktime(0, 0, 0, $month, $day, $year));			
			break;
			case 'status_online-offline':
				return $value ? 'online' : 'offline';
			break;
			case 'status_actief-inactief':
				return $value ? 'actief' : 'inactief';
			break;					
		}	
	}	
	
	public function trimText($string, $countChars){
		return strlen($string) > $countChars ? substr($string, 0, $countChars).'.'.'..' : $string;
	}	
}