<?php
/**
 * @name	c_site_back_ControllerException
 * @author 	wiegerjelsma
 * @version	1.0 2012-02-22 22:00:59
 */
class c_site_back_ControllerException extends c_site_ControllerException {}

/**
 * @name	c_site_back_Controller
 * @author 	wiegerjelsma
 * @version	1.0 2012-02-22 22:00:59
 */
class c_site_back_Controller extends c_site_Controller {
	
	protected $obj_Module = false;
	
	protected $a_Post;
	
	protected $a_Files;
	
	protected $a_Get;
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-02-22 22:00:59
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
		
		$this->setUpSession();		
		$this->assignBasicVars();
		$this->checkAccess();		
		$this->setNavigation();
		$this->setTitles();						
	}
	

	/**
	 * @name	assignBasicVars
 	 * @desc	
 	 */	 
	protected function assignBasicVars(){
		if($this->SESSION->read('loggedin')){
			$loggedInUser = $this->SESSION->read('loggedin_user');
			if($loggedInUser['userlevel'] == 'user'){
				define("IS_USER", true);
				define("IS_ADMIN", false);
				define("USER_ID", $this->SESSION->read('loggedin_userid'));
			} else {
				define("IS_ADMIN", true);
				define("IS_USER", false);
			}
			$this->TPL->assign('loggedInUserName', $loggedInUser['gebruikersnaam']);
		}
		
		$this->TPL->assign('Access', isset($this->cfg['access']) ? $this->cfg['access'] : false);
	}
	
	
	/**
	 * @name	setUpModule
 	 * @desc	Als we de bijpassende module willen gebruiken (in de form controllers) dan laden we die hier in.
 	 */	 
	protected function setUpModule($module = false){
		$this->obj_Module = $this->obj_Module ? $this->obj_Module : Loader::loadModule($module ? $module : CONTROLLER_NAME);
	}
	

	/**
	 * @name	setTitles
 	 * @desc	
 	 */	 
	protected function setTitles(){
		$this->TPL->assign('meta_title', $this->cfg['meta_title']);
	}
	
	
	/**
	 * @name	readPost
 	 * @desc	Als we de bijpassende module willen gebruiken (in de form controllers) dan laden we die hier in.
 	 */	 
	protected function readPost(){
		$obj_Tools = Loader::load('Tools');
		$this->a_Post = $obj_Tools->ReadPost();
		$this->a_Files = $obj_Tools->ReadFiles();
		$this->a_Get = $obj_Tools->ReadGet();				
	}
	
	
	
	/**
	 * @name	setTabs
 	 * @desc	We gaan de Tabs aan de TPL hangen
 	 */	 
	private function setNavigation(){
		if(!$this->SESSION->read('loggedin'))
			return;
	
		$a_Leftmenu = array();
		$activeTab = false;
		
		// set the actieve tab
		if(count($this->cfg['tabs']) == 1)
			$activeTab = $this->cfg['tabs'][0];
			
		if(!$activeTab)
			foreach($this->cfg['leftmenu'] as $tab => $a_Links){
				if($activeTab)
					break;
				foreach($a_Links as $link){
					if(strToLower(CONTROLLER_NAME) == $link['controller']){
						foreach($this->cfg['tabs'] as $_tab)
							if($tab == $_tab['name'])					
								$activeTab = $_tab;
						break;
					}	
				}
			}
		
		if(!$activeTab)
			foreach($this->cfg['tabs'] as $tab){
				if(				
					strToLower(CONTROLLER_NAME) == $tab['controller'] &&
					(isset($tab['function']) && strToLower(FUNCTION_NAME) == $tab['function']) &&
					(isset($tab['id']) && strToLower(ID_NAME) == $tab['id']))
				{
					$activeTab = $tab;
					break;
				}	
			}	
		
		if(!$activeTab)
			foreach($this->cfg['leftmenu'] as $tab => $a_Links){
				if($activeTab)
					break;
				foreach($a_Links as $link){
					if($activeTab)
						break;
					$a_Cntr = explode('.', CONTROLLER_NAME);
					$a_Controller = array();
					for($i=0; $i<count(explode('.', CONTROLLER_NAME)); $i++){
						$a_Controller[] = join('.', $a_Cntr);
						array_pop($a_Cntr);
					}
					
					foreach($a_Controller as $controller){
						if(strToLower($controller) == $link['controller']){
							foreach($this->cfg['tabs'] as $_tab)
								if($tab == $_tab['name'])					
									$activeTab = $_tab;
							break;
						}					
					}
				}
			}						

		if($activeTab){
			foreach($this->cfg['leftmenu'][$activeTab['name']] as $link){	
				$a_Cntr = explode('.', CONTROLLER_NAME);
				$a_Controller = array();
				for($i=0; $i<count(explode('.', CONTROLLER_NAME)); $i++){
					$a_Controller[] = join('.', $a_Cntr);
					array_pop($a_Cntr);
				}
				$link['active'] = false;
				if(defined('LEFTMENU_ACTIVE') && $link['name'] == LEFTMENU_ACTIVE){
					$link['active'] = true;
				} else {				
					foreach($a_Controller as $controller){
						if(strToLower($controller) == $link['controller']){
							if(!isset($link['function']) or (isset($link['function']) && $link['function'] == FUNCTION_NAME)){		
								if(!isset($link['id']) or (isset($link['id']) && $link['id'] == ID_NAME)){
									if(isset($link['notactive_controller']) && strToLower(CONTROLLER_NAME) == $link['notactive_controller'])
										continue;
									$link['active'] = true;
									break;
								}
							}
						}
					}				
				}
				$a_Leftmenu[] = $link;				
			}
			$this->TPL->assign('ActiveTab', $activeTab);
		}
		
		$this->TPL->assign('Leftmenu', $a_Leftmenu);		 

		$a_Tabs = array();
		if(count($this->cfg['tabs']) > 1)
			foreach($this->cfg['tabs'] as $tab){
				$tab['active'] = $activeTab['name'] == $tab['name'] ? true : false;
				$a_Tabs[] = $tab;	
			}
		
		$this->TPL->assign('Tabs', $a_Tabs);
	}
	
	
	/**
	 * @name	checkAccess
 	 * @desc	We gaan kijken of we ingelogd zijn (welk level) en of we access hebben
 	 */	 
	protected function checkAccess(){
		// Hier kijken of we ingelogd zijn
		if(!$this->SESSION->read('loggedin')){
			$obj_Url = Loader::load('Url');					
			$obj_Url->Redirect('login');		
		}
		
		if(IS_ADMIN && APPLICATION_NAME != FWPREFIX.'.back.admin'){
			$obj_Url = Loader::load('Url');					
			$obj_Url->RedirectToApplication(FWPREFIX.'.back.admin');		
		}
		
		if(IS_USER && APPLICATION_NAME != FWPREFIX.'.back.user'){
			$obj_Url = Loader::load('Url');					
			$obj_Url->RedirectToApplication(FWPREFIX.'.back.user');		
		}
		
		
		// Hier kijken of we access hebben in de CONTROLLER_NAME en in de FUNCTION_NAME
//		if(!IS_AJAX && !$this->cfg['access']['functions'][FUNCTION_NAME]){
//			$obj_Url = Loader::load('Url');					
//			$obj_Url->Redirect(CONTROLLER_NAME);
//		}
		
		// Hier kijken of we access hebben in de CONTROLLER_NAME en in de FUNCTION_NAME
//		if(IS_AJAX && !$this->cfg['ajax_access']['functions'][FUNCTION_NAME]){
//			$obj_Url = Loader::load('Url');					
//			$obj_Url->Redirect(CONTROLLER_NAME);
//		}
	}	
	
	
	
	/**
	 * @name	getDataHeader
 	 * @desc	We willen de kolomnamen weten
 	 */	 
 	protected function getDataHeader($cfg = false, $tablesuffix = false){		
		$viewCfg = $cfg ? $cfg : (isset($this->cfg['view']) ? $this->cfg['view'] : false);
		if($viewCfg)
			return $viewCfg;
		else {
			$a_Columns = $this->obj_Module->getColumns($tablesuffix);		
			foreach($a_Columns as $column)
				$a_Header[$column['Field']] = $column;
			return $a_Header;
		}
	}
	
	
	/**
	 * @name	setUpSearch
	 */
	protected function setUpSearch(){
		if(isset($this->cfg['search']) && !$this->cfg['search'])
			return;

		$a_Form['action'] = APPLICATION_URL.'/'.CONTROLLER_NAME.'/view';
		$a_Form['id'] = 'search';
		$a_Form['name'] = 'search';
		
		
		$searchFields = (isset($this->cfg['search']) && $this->cfg['search']) ? $this->cfg['search'] : false;
		if(!$searchFields){
			$a_Columns = $this->obj_Module->getColumns();	
			foreach($a_Columns as $column)
				$searchFields[$column['Field']] = array('placeholder' => $column['Field'], 'type' => 'text', 'size' => 'three');
		}
		
		$this->TPL->assign('SearchForm', $a_Form);
		$this->TPL->assign('SearchFields', $searchFields);
		
		$a_SessionSearch = $this->SESSION->read('SearchValues::'.CONTROLLER_NAME);
		
 		if((isset($this->a_Post['submitted-search']) && $this->a_Post['submitted-search']) or ($a_SessionSearch)){
 			
 			$a_Search = array();
 			if(isset($this->a_Post['submitted-search']) && $this->a_Post['submitted-search']) {
				unset($this->a_Post['submitted-search']); 			 			
	 			foreach($this->a_Post as $key => $value)
 					$a_Search[$key] = $value != $this->cfg['search'][$key]['placeholder'] ? $value : '';		
			} else 
				$a_Search = $a_SessionSearch;
			
			$this->TPL->assign('SearchValues', $a_Search);
			$this->SESSION->write('SearchValues::'.CONTROLLER_NAME, $a_Search);			
			
			return $a_Search;		
		}
		return false;		
	}	 
	
	protected function setUpOrder(){
		$a_Order = $this->SESSION->read('OrderValues::'.CONTROLLER_NAME);
		if(array_key_exists('order', $this->a_Get)){
			$direction = $a_Order['key'] == $this->a_Get['order'] ? ($a_Order['direction'] == 'ASC' ? 'DESC' : 'ASC') : 'ASC';
			$key = $this->a_Get['order'];
			$a_Order = array('key' => $key, 'direction' => $direction);
			$this->SESSION->write('OrderValues::'.CONTROLLER_NAME, $a_Order);
		}
		$this->TPL->assign('OrderValues', $a_Order);
		return $a_Order;			
	}	
	
	protected function formatResult($a_Result, $a_HeaderKeys){
		foreach($a_Result as $result){
			$a = array();
			foreach($a_HeaderKeys as $key)
				 $a[$key] = $result[$key];
					
			$a['id'] = $result['id'];
			$a_Dataset[] = $a;
		}
		return $a_Dataset;	
	}
	
	
	/**
	 * @name	view
 	 * @desc	We gaan een grid page laden met daarin de records (generiek)
 	 */	 
 	public function view($a_Params = array()){
 		$this->readPost();
 		$this->setUpModule();
 		
 		$a_Search = $this->setUpSearch();
 		$a_Search = $a_Search ? $a_Search : array();
 		
 		$a_Order = $this->setUpOrder();
 		$a_Order = $a_Order ? array('order' => $a_Order['key'].' '.$a_Order['direction']) : array('order' => 'b.id ASC');
 			
		$a_Result = $this->obj_Module->get($a_Params, $a_Order, $a_Search);
		$a_Header = $this->getDataHeader();
		$a_HeaderKeys = array_keys($a_Header);
		
		if($a_Result)
			$a_Dataset = $this->formatResult($a_Result, $a_HeaderKeys);
		else
			$a_Dataset = false; 		
 		
 		$a_Buttons = false;
 		if(isset($this->cfg['access']['actions']['add']) && $this->cfg['access']['actions']['add'])
	 		$a_Buttons[] = array('url' => APPLICATION_URL.'/'.(isset($this->cfg['controller']['add']) ? $this->cfg['controller']['add'] : CONTROLLER_NAME).'/add', 'value' => 'add');
 		
 		$sequenceManagement = isset($this->cfg['sequencemanagement']) && $this->cfg['sequencemanagement'] ? true : false; 
 		if($sequenceManagement){
 			$sequence = $this->obj_Module->formatJstreeSequence($this->cfg['sequence']['key'], $this->cfg['sequence']['root']);
			$this->TPL->assign('Sequence', json_encode($sequence)); 		
		}
 		$this->TPL->assign('SequenceManager', $sequenceManagement); 		
 		 		
		$this->TPL->assign('Buttons', $a_Buttons); 			 		
		$this->TPL->assign('DataHeader', $a_Header);
		$this->TPL->assign('DataSet', $a_Dataset);
		$this->TPL->assign('EditController', isset($this->cfg['controller']['edit']) ? $this->cfg['controller']['edit'] : false);
		$this->TPL->assign('DeleteController', isset($this->cfg['controller']['delete']) ? $this->cfg['controller']['delete'] : false);			
		
			
		$this->TPL->assign('Titel', isset($this->cfg['titel']) ? $this->cfg['titel'] : CONTROLLER_NAME);
		$this->TPL->assign('Subtitel', isset($this->cfg['subtitel']) ? $this->cfg['subtitel'] : false);
				
				
				
		$this->TPL->display('view.tpl');
 	}
 	
 	
 	/**
	 * @name	form
 	 * @desc	We gaan een formulier tonen met daarin een formulier
 	 */	 
 	protected function form($subtitel, $id = false, $a_Dataset = false, $display = true){
 		$this->setUpModule();
 		
 		if(FUNCTION_PARAM == 'sub')
 			$formCfg = (isset($this->cfg['sub']['form'])) ? $this->cfg['sub']['form'] : false;
 		else
 			$formCfg = (isset($this->cfg['form'])) ? $this->cfg['form'] : false; 	
 		
		$wysiwyg = true;
		if($formCfg){
			$a_Fields = $formCfg;
			foreach($a_Fields as $field => $a_Params){	
				if($a_Params['type'] == 'fileupload')
	 				$a_Form['enctype'] = 'multipart/form-data';
	 				
	 			if($a_Params['type'] == 'wysiwyg')
	 				$wysiwyg = true;
	 		}
		} else {
 	 		$a_Columns = (FUNCTION_PARAM == 'sub') ? $this->obj_Module->getColumns($this->cfg['sub']['tabelsuffix']) : $this->obj_Module->getColumns();
	 		foreach($a_Columns as $field)
	 			$a_Fields[$field['Field']] = array();
		} 	
		
		if($wysiwyg){
			$a_Controllers = array();
			if(isset($this->cfg['wysiwyg']['mylink_modules']) && is_array($this->cfg['wysiwyg']['mylink_modules'])){
				foreach($this->cfg['wysiwyg']['mylink_modules'] as $module){
					$obj_Module = Loader::loadModule($module);
					$a_Controllers[$module] = $obj_Module->getItemsForWysiwyg(); 
				}
				$this->TPL->assign('WysiwygControllers', $a_Controllers);
			}
			if(isset($this->cfg['wysiwyg']['mylink_files']) && $this->cfg['wysiwyg']['mylink_files']){
				$obj_Files = Loader::loadModule('Files');
				$a_Files = $obj_Files->getFiles(); 
				$this->TPL->assign('WysiwygFiles', $a_Files);
			}
						
		}
		
		$a_Dataset = $this->getDataSet($a_Dataset, $id);
		$subid = false;
		if(isset($this->cfg['sub']['tabelid']) && !isset($a_Dataset[$this->cfg['sub']['tabelid']]) && (FUNCTION_PARAM == 'sub')){
			$a_Dataset[$this->cfg['sub']['tabelid']] = $this->a_Get['subid'];
			$subid = $this->a_Get['subid'];
		} else
			if(isset($this->cfg['sub']['tabelid']) && isset($a_Dataset[$this->cfg['sub']['tabelid']]))
				$subid = $a_Dataset[$this->cfg['sub']['tabelid']];
		
		$action = (FUNCTION_PARAM == 'sub') ? APPLICATION_URL.'/'.CONTROLLER_NAME.'/'.FUNCTION_NAME.':sub'.($id ? '/'.$id : '') : APPLICATION_URL.'/'.CONTROLLER_NAME.'/'.FUNCTION_NAME.($id ? '/'.$id : '');
		$a_Form['action'] = $action;
		$a_Form['id'] = 'formgeneral';
		$a_Form['name'] = 'formgeneral';
		$a_Form['addoredit'] = FUNCTION_NAME;
		
		$this->TPL->assign('Form', $a_Form);		
		$this->TPL->assign('Fields', $a_Fields);
		
		$this->TPL->assign('DataSet', $a_Dataset);		
		$this->TPL->assign('Buttons', $this->getFormButtons($subid)); 
//		$this->TPL->assign('Formheader', 'Form header'); 					 				
		$this->TPL->assign('Titel', isset($this->cfg['titel']) ? $this->cfg['titel'] : CONTROLLER_NAME);		
		$this->TPL->assign('Subtitel', $subtitel);		
		
		if($display)
	 		$this->TPL->display('form.tpl'); 	
 	}
 	
 	protected function getDataSet($a_Dataset = array(), $id = false){
		return $a_Dataset ? $a_Dataset : ($id ? ((FUNCTION_PARAM == 'sub') ? $this->obj_Module->getOne($id, array(), $this->cfg['sub']['tabelsuffix']) : $this->obj_Module->getOne($id)) : false); 	
 	}
 	
 	
 	/**
 	 * @name	getFormButtons
 	 */
 	protected function getFormButtons($subid = false){
 		if((isset($this->cfg['access']['actions']['add']) && $this->cfg['access']['actions']['add']) or (isset($this->cfg['access']['actions']['edit']) && $this->cfg['access']['actions']['edit']))
	 		$a_Buttons[] = array('type' => 'submit', 'value' => 'save');
 		
 		if(FUNCTION_PARAM == 'sub')
	 		$a_Buttons[] = array('url' => APPLICATION_URL.'/'.CONTROLLER_NAME.'/edit/'.$subid, 'value' => 'cancel');
 		else {
 			if((isset($this->cfg['access']['actions']['add']) && $this->cfg['access']['actions']['add']) or (isset($this->cfg['access']['actions']['edit']) && $this->cfg['access']['actions']['edit']))
	 			$a_Buttons[] = array('url' => APPLICATION_URL.'/'.(isset($this->cfg['controller']['view']) ? $this->cfg['controller']['view'] : CONTROLLER_NAME), 'value' => 'cancel');
		 		
 			if(isset($this->cfg['access']['actions']['view']) && $this->cfg['access']['actions']['view'])
		 		$a_Buttons[] = array('url' => APPLICATION_URL.'/'.CONTROLLER_NAME, 'value' => 'back');
		}
 		return $a_Buttons; 		
 	}
 	
 	
 	/**
	 * @name	validate
 	 * @desc	We gaan het formulier valideren op basis van de form config.
 	 */	  	 	
 	protected function validate($addoredit = false){
 		if(FUNCTION_PARAM == 'sub')
 			$formCfg = (isset($this->cfg['sub']['form'])) ? $this->cfg['sub']['form'] : false;
 		else
 			$formCfg = (isset($this->cfg['form'])) ? $this->cfg['form'] : false;  	
 	
 		if(!$formCfg)
 			return true;
 			
 		$a_ErrorFields = array();
 		
 		
 		foreach($formCfg as $field => $a_Params){
	 		
 			$fieldLabel = isset($formCfg[$field]['label']) && $formCfg[$field]['label'] ? $formCfg[$field]['label'] : ucfirst($field);
 			$fieldType = isset($formCfg[$field]['type']) && $formCfg[$field]['type'] ? $formCfg[$field]['type'] : false;
 			
 			if(isset($a_Params['type']) && $a_Params['type'] == 'wysiwyg'){
 				$this->a_Post[$field] = htmlspecialchars_decode($this->a_Post[$field]);
 				$this->a_Post[$field] = strip_tags($this->a_Post[$field], '<code><span><div><label><a><br><p><b><i><del><strike><u><img><video><audio><iframe><object><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><code><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>');
 			}
 			
 			// format data
 			switch($fieldType){
 				case 'date':
 					$day = $this->a_Post[$field.'_day'] > 9 ? $this->a_Post[$field.'_day'] : '0'.$this->a_Post[$field.'_day'];
 					$month = $this->a_Post[$field.'_month'] > 9 ? $this->a_Post[$field.'_month'] : '0'.$this->a_Post[$field.'_month'];
 					$this->a_Post[$field] = $this->a_Post[$field.'_year'].'-'.$month.'-'.$day;
 				break;
 				case 'checkbox':
 					$this->a_Post[$field] = isset($this->a_Post[$field]) ? $this->a_Post[$field] : false;
 				break;
 			} 		
 		
 			// validate op required
			if((isset($a_Params['required']) && $a_Params['required']) or (isset($a_Params['required_on']) && $a_Params['required_on'] == $addoredit))
				if((!isset($this->a_Post[$field]) or !$this->a_Post[$field]) && $a_Params['type'] != 'fileupload'){
					$this->MESSAGES->push("Het veld '$fieldLabel' is nog leeg.", 'error', 'form');
					$a_ErrorFields[$field] = true;
					continue;
				} else {				
					if($a_Params['type'] == 'fileupload' && ((!isset($this->a_Files[$field]) or !$this->a_Files[$field]['name']) && !isset($this->a_Post['image']))){
						$this->MESSAGES->push("Het veld '$fieldLabel' is nog leeg.", 'error', 'form');
						$a_ErrorFields[$field] = true;
						continue;
					}				
				}
				
			// validate op specifieke validatie
			require_once 'Zend/Validate.php';
			
			if(isset($a_Params['validation']) && $a_Params['validation'])
				switch($a_Params['validation']){
				
					case 'shorturl':
						if(isset($this->a_Post[$field]) && $this->a_Post[$field]){
						
							$this->a_Post[$field] = preg_replace('/_/',' ', $this->a_Post[$field]);							
//							$this->a_Post[$field] = preg_replace('/\s/','-', $this->a_Post[$field]);
//							$this->a_Post[$field] = str_replace('/*/','', $this->a_Post[$field]);
							$this->a_Post[$field] = preg_replace('/\W/','-', $this->a_Post[$field]);
							$this->a_Post[$field] = strToLower($this->a_Post[$field]);
						
							$obj_Pages = loader::loadModule('Cms.Pages');
							$page = $obj_Pages->get(array('short_url' => $this->a_Post[$field]));
					
							if($page){
								if(($addoredit == 'edit' && $page[0]['id'] != $this->a_Post['id']) or ($addoredit == 'add')){
									$this->MESSAGES->push("Deze shorturl is al eens gebruikt.", 'error', 'form');
									$a_ErrorFields[$field] = true;
								}				
							}
						}
					
					break;		
					case 'date':
						$day = $this->a_Post[$field.'_day'];
						$month = $this->a_Post[$field.'_month'];
						$year = $this->a_Post[$field.'_year'];
						
						unset($this->a_Post[$field.'_day']); // Deze hebben we niet nodig, kolom bestaat niet in de DB..
						unset($this->a_Post[$field.'_month']); // Deze hebben we niet nodig, kolom bestaat niet in de DB..
						unset($this->a_Post[$field.'_year']); // Deze hebben we niet nodig, kolom bestaat niet in de DB..
						
						if(!checkdate($month, $day, $year)){
							$this->MESSAGES->push("Er is geen geldige datum ingevuld in veld '$fieldLabel'.", 'error', 'form');
							$a_ErrorFields[$field] = true;
						}						
					break;
					case 'website':
						if($this->a_Post[$field] != ''){
							if(!preg_match('/^http/', $this->a_Post[$field])){				
								$this->a_Post[$field] = preg_replace('/^http\:\/\//', '', $this->a_Post[$field]);						
								$this->a_Post[$field] = 'http://'.$this->a_Post[$field];
							}
							if (!preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $this->a_Post[$field])) {
								$this->MESSAGES->push("Er is geen geldige website ingevuld in veld '$fieldLabel'.", 'error', 'form');
								$a_ErrorFields[$field] = true;
							}
						}
					break;					
					case 'email':
						if($this->a_Post[$field])									
							if(!Zend_Validate::is($this->a_Post[$field], 'EmailAddress')) {
							$this->MESSAGES->push("Er is geen geldig emailadres ingevuld in veld '$fieldLabel'.", 'error', 'form');
							$a_ErrorFields[$field] = true;
						}					
					break;
					case 'password':
						if($this->a_Post[$field] != $this->a_Post[$field.'_confirm']){
							$this->MESSAGES->push("De wachtwoorden kwamen niet overeen.", 'error', 'form');
							$a_ErrorFields[$field] = true;
							$a_ErrorFields[$field.'_confirm'] = true;							
						}
						unset($this->a_Post[$field.'_confirm']); // Deze hebben we niet nodig, kolom bestaat niet in de DB..
					break;
					case 'file':
						
						if(!isset($this->a_Files[$field]) or !$this->a_Files[$field]['name'])
							continue;					
						
						if(!in_array($this->a_Files[$field]['type'], $a_Params['allowed'])){
							$this->MESSAGES->push("Het is niet toegestaan om een bestand van dit type te uploaden", 'error', 'form');
							$a_ErrorFields[$field] = true;
						}	
						
						if($this->a_Files[$field]["size"] > ($a_Params['maxfilesize']*1000)){
							$this->MESSAGES->push("Het bestand mag niet groter zijn dan ".$a_Params['maxfilesize']."kb (".($a_Params['maxfilesize']/1024)."MB)", 'error', 'form');
							$a_ErrorFields[$field] = true;
						}	
						
						if(!isset($a_ErrorFields[$field])){	
						
							$systemError = '';
							if($this->a_Files[$field]["error"] > 0 or !$this->a_Files[$field]["size"] or $this->a_Files[$field]["size"] <= 0){
								switch($this->a_Files[$field]["error"]){
									case '1':
										$systemError = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
										$this->MESSAGES->push("Het bestand is te groot!", 'error', 'form');
									break;
									case '2':
										$systemError = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
										$this->MESSAGES->push("Er is een algemene fout opgetreden bij het uploaden van de file.", 'error', 'form');
									break;
									case '3':
										$this->MESSAGES->push("Er is een algemene fout opgetreden bij het uploaden van de file.", 'error', 'form');
										$systemError = 'The uploaded file was only partially uploaded.';
									break;
									case '4':
										$this->MESSAGES->push("Er is een algemene fout opgetreden bij het uploaden van de file.", 'error', 'form');
										$systemError = 'No file was uploaded.';
									break;
									case '6':
										$this->MESSAGES->push("Er is een algemene fout opgetreden bij het uploaden van de file.", 'error', 'form');
										$systemError = 'Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.';
									break;
									case '7':
										$this->MESSAGES->push("Er is een algemene fout opgetreden bij het uploaden van de file.", 'error', 'form');
										$systemError = 'Failed to write file to disk. Introduced in PHP 5.1.0.';
									break;							
								}
															
							//	$this->MESSAGES->push("Er is een algemene fout opgetreden bij het uploaden van de file.", 'error', 'form');
								dump("[FILEUPLOAD ERROR] :: ".__METHOD__.' '.$systemError);
//								dump($this->a_Files,'a_Files');
								$a_ErrorFields[$field] = true;
							}
						}								
					break;
					case 'image':
						if(!isset($this->a_Files[$field]) or !$this->a_Files[$field]['name'])
							continue;					
						
						if(!in_array($this->a_Files[$field]['type'], array('image/jpeg','image/pjpeg'))){
							$this->MESSAGES->push("Het is niet toegestaan om een bestand van dit type te uploaden", 'error', 'form');
							$a_ErrorFields[$field] = true;
						}	
						
						if($this->a_Files[$field]["size"] > ($a_Params['maxfilesize']*1000)){
							$this->MESSAGES->push("Het bestand mag niet groter zijn dan ".$a_Params['maxfilesize']."kb (".($a_Params['maxfilesize']/1024)."MB)", 'error', 'form');
							$a_ErrorFields[$field] = true;
						}	
						
						if($this->a_Files[$field]["error"] > 0 or !$this->a_Files[$field]["size"] or $this->a_Files[$field]["size"] <= 0){
							$this->MESSAGES->push("Er is een algemene fout opgetreden bij het uploaden van de file.", 'error', 'form');
							dump("[FILEUPLOAD ERROR] :: ".__METHOD__);
//							dump($this->a_Files,'a_Files');
							$a_ErrorFields[$field] = true;
						}								
					
					break;
					
					case 'galerieimage':
						if(isset($this->a_Post[$field.'_stored']) && !$this->a_Files[$field]['name']){
							$this->a_Post[$field] = $this->a_Post[$field.'_stored'];							
							continue;
						}
						
						if(!isset($this->a_Files[$field]) or !$this->a_Files[$field]['name']){
							continue;					
						}

						if(($this->a_Files[$field]['type'] != "image/jpeg") && ($this->a_Files[$field]['type'] != "image/pjpeg")){
							$this->MESSAGES->push("U kunt alleen jpg files uploaden.", 'error', 'form');
							$a_ErrorFields[$field] = true;
						}	
										
						if($this->a_Files[$field]["size"] > ($a_Params['maxfilesize']*1000)){
							$this->MESSAGES->push("Het bestand mag niet groter zijn dan ".$a_Params['maxfilesize']."kb (".($a_Params['maxfilesize']/1024)."MB)", 'error', 'form');
							$a_ErrorFields[$field] = true;
						}	
						
						if($this->a_Files[$field]["error"] > 0){
							$this->MESSAGES->push("Er is een algemene fout opgetreden bij het uploaden van de image.", 'error', 'form');
							dump("[FILEUPLOAD ERROR] :: ".__METHOD__);
							dump($this->a_Files,'a_Files');
							$a_ErrorFields[$field] = true;
						}	
								
						if(!isset($a_ErrorFields[$field])){							
							// als we geen problemen hebben met de image dan kunnen we die verplaatsen naar een temp directory.						
							$tmpdir = BIN_APPLICATION.$this->cfg['filesystem']['tmpdir'].'/'.$this->SESSION->read('loggedin_userid').'/';
							$obj_FileSystem = Loader::load('FileSystem');
							if($obj_FileSystem->makeDir($tmpdir) === false){
								$this->MESSAGES->push("Er is een algemene fout opgetreden bij het uploaden van de image. (makedir)", 'error', 'form');
								$a_ErrorFields[$field] = true;
							} else {		
								if(!move_uploaded_file($this->a_Files[$field]["tmp_name"], $tmpdir . $field.'-'.$this->a_Files[$field]["name"])){
									$this->MESSAGES->push("Er is een algemene fout opgetreden bij het uploaden van de image.", 'error', 'form');
									dump("[FILEUPLOAD ERROR] :: ".__METHOD__." :: Unable to move uploaded file");
									dump($this->a_Files,'a_Files');
									$a_ErrorFields[$field] = true;
								} else {								
									$this->a_Post[$field] = $this->a_Files[$field]['name'];
									$this->a_Post[$field.'_stored'] = $this->a_Files[$field]['name'];
									$this->a_Post[$field.'_displayed'] = $this->a_Files[$field]['name'];																												
								}
							}						
						}
					break;					
				}
 		}
 		
 		if(count($a_ErrorFields)>=1){
			$this->TPL->assign('ErrorFields', $a_ErrorFields);		
 			return false;
 		} else
 			return true; 			
 	}
 	
 	
	/**
	 * @name	handleFileUpload
	 * @desc	We gaan de file vanuit de tmp directory naar de src directory verplaatsen. Vanuit de src wordt hij door de cron opgepakt en geresized.
 	 */	  	
 	protected function handleFileUpload($edit = false){ 	
 	 	
 		// We hebben een file in de temp directory staan
 		$a_ErrMsg = array();
 		$formCfg = (FUNCTION_PARAM == 'sub') ? $this->cfg['sub']['form'] : $this->cfg['form'];
 		
 		foreach($formCfg as $field => $a_Params){
 			if(isset($a_Params['type']) && $a_Params['type'] == 'fileupload'){
 			
				try {
					if(isset($this->a_Files[$field]['name']) && $this->a_Files[$field]['name']){	
						$obj_Files = Loader::loadModule('Files');
						if($edit && isset($this->a_Post[$field.'_displayed'])){
							$obj_Files->deleteImageByNameAndGroup($this->a_Post[$field.'_displayed'], CONTROLLER_NAME);
							unset($this->a_Post[$field.'_displayed']);
						}
									
						$a_Image['filename'] = $this->a_Files[$field]['name'];
						$a_Image['type'] = $this->a_Files[$field]['type'];
						$a_Image['size'] = $this->a_Files[$field]['size'];	
						$a_Image['resize'] = ($this->a_Files[$field]['type'] == 'image/jpeg' or $this->a_Files[$field]['type'] == 'image/pjpeg') ? true : false;							
						$a_Image['src'] = file_get_contents($this->a_Files[$field]['tmp_name']);
						unset($this->a_Post[$field.'_name']);
						$a_Image['group'] = CONTROLLER_NAME;						
						
						$image_id = $obj_Files->save($a_Image);
						
						if($image_id){
							$name = $obj_Files->getImageFileName($image_id);
							$this->a_Post[$field] = $name;
						}
					} 			
				} catch(Exception $e){
					$a_ErrMsg[] = 'Unable to save image';				
				} 			
 			}		 	 	 			
 		}
 		if(count($a_ErrMsg) >= 1){
 			foreach($a_ErrMsg as $msg)
	 			dump("[FILEMOVE ERROR] :: ". $msg);
	 		return false;
	 	}							 	
	 	return true;
 	}
 	
 	
 	
 	/**
	 * @name	add
 	 * @desc	We gaan een formulier tonen met daarin een formulier
 	 */	  	
 	public function add(){
 		$this->readPost();
 	
 		if(isset($this->a_Post['submitted']) && $this->a_Post['submitted']){
 			unset($this->a_Post['submitted']);
 			
 			if($this->validate('add')){
				unset($this->a_Post['edit_by_user']);
		 		unset($this->a_Post['added_by_user']);
 			 	$this->setUpModule();		 	
 			 	
 			 	$fileUpload = $this->handleFileUpload();
 			 	$tablesuffix = FUNCTION_PARAM == 'sub' ? $this->cfg['sub']['tabelsuffix'] : false;
 			 	
 			 	if(!isset($this->a_Post['sequence_id'])){
 			 		if(FUNCTION_PARAM == 'sub'){
 			 			if(isset($this->cfg['sub']['sequencemanagement']) && $this->cfg['sub']['sequencemanagement']){
 			 				$a_Elements = $this->obj_Module->get(array($this->cfg['sub']['tabelid'] => $this->a_Post[$this->cfg['sub']['tabelid']]), array('order' => 'sequence_id DESC'), array(), $tablesuffix);
 			 				if($a_Elements)
 			 					$this->a_Post['sequence_id'] = $a_Elements[0]['sequence_id']+1;
 			 			}
 			 		} else {
 			 			if(isset($this->cfg['sequencemanagement']) && $this->cfg['sequencemanagement']){
 			 				$a_Elements = $this->obj_Module->get(array(), array('order' => 'sequence_id DESC'));
 			 				if($a_Elements)
 			 					$this->a_Post['sequence_id'] = $a_Elements[0]['sequence_id']+1;
 			 			}
 			 		}
 			 	}
 			 	
 			 	$id = $this->obj_Module->save($this->a_Post, $tablesuffix);

	 			if($fileUpload && $id){
 				 	$this->MESSAGES->push('De gegevens zijn succesvol opgeslagen', 'success');
 				 	
 					if(FUNCTION_PARAM == 'sub'){
 						$obj_Url = Loader::load('Url');					
						$obj_Url->Redirect(CONTROLLER_NAME.'/edit/'.$this->a_Post[$this->cfg['sub']['tabelid']]);
 					} else 						
	 					$this->view();
 				} else {
 				 	$this->MESSAGES->push('De gegevens konden niet worden opgeslagen', 'error');
 					$this->form('Toevoegen', false, $this->a_Post);
 				} 			
 			} else
 			 	$this->form('Toevoegen', false, $this->a_Post);			 			
 		} else
 	 		$this->form('Toevoegen');	
 	}
 	
 	
 	/**
	 * @name	edit
 	 * @desc	We gaan een formulier tonen met daarin een formulier en de data
 	 */	  	
 	public function edit($display = true){
 		$this->readPost();
 		
 		if(isset($this->a_Post['submitted']) && $this->a_Post['submitted']){
 			unset($this->a_Post['submitted']);
 			
 			if($this->validate('edit')){

				unset($this->a_Post['edit_by_user']);
		 		unset($this->a_Post['added_by_user']); 			
		 		$this->setUpModule();

 			 	$fileUpload = $this->handleFileUpload(true);
 			 	$tablesuffix = FUNCTION_PARAM == 'sub' ? $this->cfg['sub']['tabelsuffix'] : false; 			 	
 			 	$id = $this->obj_Module->save($this->a_Post, $tablesuffix);
 			 	
 				if($fileUpload && $id){
 					$this->MESSAGES->push('De gegevens zijn succesvol gewijzigd', 'success');
 				 	 
 				 	if(FUNCTION_PARAM != 'sub'){
	 					$this->view();
	 					return;
	 				} else {
						$obj_Url = Loader::load('Url');					
						$obj_Url->Redirect(CONTROLLER_NAME.'/edit/'.$this->a_Post[$this->cfg['sub']['tabelid']]);	 				
	 				}
 				} else {
 					$this->MESSAGES->push('De gegevens konden niet worden gewijzigd', 'error');
 					$this->form('Bewerken', $this->a_Post['id'], $this->a_Post);
 				} 			
 			} else {
 				$this->form('Bewerken', $this->a_Post['id'], $this->a_Post, false);	
 				$id = $this->a_Post['id']; 					
 			}
 		} else {
 			$this->form('Bewerken', ID_NAME, false, false); 		
			$id = ID_NAME;	
		}
 		
 		if($display){
	 		if(isset($this->cfg['sub']['tabelsuffix']) && $this->cfg['sub']['tabelsuffix'] && $this->cfg['access']['actions']['subadd']){
 				if(FUNCTION_PARAM != 'sub'){
 					$this->viewSubGrid(array($this->cfg['sub']['tabelid'] => $id));
 					
 					$subSequenceManagement = isset($this->cfg['sub']['sequencemanagement']) && $this->cfg['sub']['sequencemanagement'] ? true : false; 
 					$subSequence = $this->obj_Module->formatJstreeSequence($this->cfg['sub']['sequence']['key'], $this->cfg['sub']['sequence']['root'], $this->cfg['sub']['tabelsuffix'], $this->cfg['sub']['tabelid'], ID_NAME);
					
					$this->TPL->assign('SubSequenceManager', $subSequenceManagement); 		
					$this->TPL->assign('SubSequence', json_encode($subSequence)); 					 					
 				}
 					
		 		$this->TPL->display('form.tpl');		 		
 			} else
		 		$this->TPL->display('form.tpl');
		 }
 	}
 	
 	
 	/**
	 * @name	edit
 	 * @desc	We gaan het record deleten (Hier zit geen confirm)
 	 */	  	
 	public function delete(){
 		$this->setUpModule();
 		$this->readPost();

		$tablesuffix = isset($this->cfg['sub']['tabelsuffix']) && $this->cfg['sub']['tabelsuffix'] ? $this->cfg['sub']['tabelsuffix'] : false;
		$deleteforsubtabelid = isset($this->cfg['sub']['tabelid']) ? $this->cfg['sub']['tabelid'] : false;

		$a_Images = array();
		$a_SubImages = array();

		/*
		
		foreach($this->cfg['form'] as $field => $a_Params){
			if($a_Params['type'] == 'fileupload'){
				$a_Image = array();
				$a_Image['key'] = $field;				
				$a_Image['directorykey'] = isset($a_Params['directorykey']) ? $a_Params['directorykey'] : false;
				$a_Images[] = $a_Image;
			}
		}
		
		if(isset($this->cfg['sub']['form'])){
			foreach($this->cfg['sub']['form'] as $field => $a_Params){
				if($a_Params['type'] == 'fileupload'){
					$a_Image = array();
					$a_Image['key'] = $field;				
					$a_Image['directorykey'] = isset($a_Params['directorykey']) ? $a_Params['directorykey'] : false;
					$a_SubImages[] = $a_Image;
				}
			}		
		} */
		
 		if(FUNCTION_PARAM == 'sub'){
 			if($this->obj_Module->deleteSubRecord(ID_NAME, $tablesuffix, $a_SubImages))
	 			$this->MESSAGES->push('De gegevens zijn succesvol verwijderd', 'success'); 
			else
 				$this->MESSAGES->push('De gegevens konden niet worden verwijderd', 'error');	 			 	 		
 		} else {
	 		if($this->obj_Module->deleteSubRecords(ID_NAME, $tablesuffix, $deleteforsubtabelid, $a_SubImages)){ 		
	 			if($this->obj_Module->delete(ID_NAME, $a_Images)) 		
 		 			$this->MESSAGES->push('De gegevens zijn succesvol verwijderd', 'success'); 			
			} else
 				$this->MESSAGES->push('De gegevens konden niet worden verwijderd', 'error');
 		}
 		
 		if(FUNCTION_PARAM != 'sub'){
	 		$this->view();
	 	} else {
			$obj_Url = Loader::load('Url');					
			$obj_Url->Redirect(CONTROLLER_NAME.'/edit/'.$this->a_Get['subid']);	 				
	 	}
 	}	
 	
 	
	/**
	 * @name	view
 	 * @desc	We gaan een grid page laden met daarin de records (generiek)
 	 */	 
 	public function viewSubGrid($a_Params = array()){
 		$this->readPost();
 		$this->setUpModule();
 		
 		$a_Search = $this->setUpSearch();
 		$a_Search = $a_Search ? $a_Search : array();
 		
 		$a_Order = $this->setUpOrder();
 		$a_Order = $a_Order ? array('order' => $a_Order['key'].' '.$a_Order['direction']) : array('order' => 'b.id ASC');
	
		$a_Result = $this->obj_Module->get($a_Params, $a_Order, $a_Search, $this->cfg['sub']['tabelsuffix']);
		$viewCfg = isset($this->cfg['sub']['view']) ? $this->cfg['sub']['view'] : false;
		$a_Header = $this->getDataHeader($viewCfg, $this->cfg['sub']['tabelsuffix']);
		$a_HeaderKeys = array_keys($a_Header);
		
		if($a_Result)
			foreach($a_Result as $result){
				$a = array();
				foreach($a_HeaderKeys as $key)
					 $a[$key] = $result[$key];
					
				$a['id'] = $result['id'];
				$a_Dataset[] = $a;
			}
		else
			$a_Dataset = false; 		
 		
 		$a_Buttons = false;
 		if(isset($this->cfg['access']['actions']['subadd']) && $this->cfg['access']['actions']['subadd'])
	 		$a_Buttons[] = array('url' => APPLICATION_URL.'/'.CONTROLLER_NAME.'/add:sub/?subid='.ID_NAME, 'value' => 'toevoegen');
 		
		$this->TPL->assign('SubButtons', $a_Buttons); 			 		
		$this->TPL->assign('SubDataHeader', $a_Header);
		$this->TPL->assign('SubDataSet', $a_Dataset);
		if(isset($this->cfg['sub']['title']))
			$this->TPL->assign('SubTitle', $this->cfg['sub']['title']);
		else			
			$this->TPL->assign('SubTitle', CONTROLLER_NAME.' '.$this->cfg['sub']['tabelsuffix']);
			
		$this->TPL->assign('Titel', isset($this->cfg['titel']) ? $this->cfg['titel'] : CONTROLLER_NAME);
		$this->TPL->assign('Subtitel', isset($this->cfg['subtitel']) ? $this->cfg['subtitel'] : false);		
 	} 	
 	
	public function ajax_updateSequence(){
 		$this->readPost();
 		$this->setUpModule();
 		
 		$this->obj_Module->updateSequence($this->a_Post);
 		
 		// Hier nog goed of fout retourneren
	} 	

	public function ajax_updateSubSequence(){
 		$this->readPost();
 		$this->setUpModule();
 		
 		$this->obj_Module->updateSequence($this->a_Post, $this->cfg['sub']['tabelsuffix']);
 		
 		// Hier nog goed of fout retourneren
	} 	
	
	public function ajax_getRedactorImages(){
		$a_RedactorImages = array();
		$obj_Files = Loader::loadModule('Files');
		$a_Images = $obj_Files->getImages();
		if($a_Images){
			foreach($a_Images as $image){
				if($image['group'] != '')
					continue;

				$image['filename'] = preg_replace('/\.jpg/ ','', $image['filename']);
				$a_Image = array();
				$a_Image['thumb'] = APPLICATION_URL.'/file/image/'.$image['filename'].'-s.jpg';
				$a_Image['image'] = 'http://'.APPLICATION_DOMAIN.'/'.ORIG_FWPREFIX.'/'.FWPREFIX.'.front/file/imagebyid/'.$image['id'].':-s';
				$a_Image['title'] = $image['name'];
				$a_Image['folder'] = 'Small';
				$a_RedactorImages[] = $a_Image;				
			}

			foreach($a_Images as $image){
				if($image['group'] != '')
					continue;
					
				$image['filename'] = preg_replace('/\.jpg/ ','', $image['filename']);
				$a_Image = array();
				$a_Image['thumb'] = APPLICATION_URL.'/file/image/'.$image['filename'].'-m.jpg';
				$a_Image['image'] = 'http://'.APPLICATION_DOMAIN.'/'.ORIG_FWPREFIX.'/'.FWPREFIX.'.front/file/imagebyid/'.$image['id'].':-m';
				$a_Image['title'] = $image['name'];
				$a_Image['folder'] = 'Medium';
				$a_RedactorImages[] = $a_Image;				
			}
			foreach($a_Images as $image){
				if($image['group'] != '')
					continue;

				$image['filename'] = preg_replace('/\.jpg/ ','', $image['filename']);
				$a_Image = array();
				$a_Image['thumb'] = APPLICATION_URL.'/file/image/'.$image['filename'].'-l.jpg';
				$a_Image['image'] = 'http://'.APPLICATION_DOMAIN.'/'.ORIG_FWPREFIX.'/'.FWPREFIX.'.front/file/imagebyid/'.$image['id'].':-l';
				$a_Image['title'] = $image['name'];
				$a_Image['folder'] = 'Large';
				$a_RedactorImages[] = $a_Image;				
			}
			
			
		}
		
		echo json_encode($a_RedactorImages);
	}	
}