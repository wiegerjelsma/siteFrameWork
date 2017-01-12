<?php
/**
 * @name	c_cms_front_ControllerException
 * @author 	wiegerjelsma
 * @version	1.0 2012-02-22 22:00:54
 */
class c_site_front_ControllerException extends c_site_ControllerException {}

/**
 * @name	c_cms_front_Controller
 * @author 	wiegerjelsma
 * @version	1.0 2012-02-22 22:00:54
 */
class c_site_front_Controller extends c_site_Controller {
	
	protected $a_User;
	
	protected $a_Menus = array();
	
	protected $activeNode;
	
	protected $shorturl;
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-02-22 22:00:54
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
				
		$this->setUpSession();
		$this->setUpTemplate();
		$this->setUpMenu();	
		$this->setUpPage();
	}
		
	protected function setUpPage(){	
		$obj_Cms = Loader::loadModule('Cms');
		$obj_Menu = Loader::loadModule('Cms.Menu');		
		$obj_Pages = Loader::loadModule('Cms.Pages');
		
		$left = isset($_SESSION['left']) ? $_SESSION['left'] : rand(1,9);
		$right = isset($_SESSION['right']) ? $_SESSION['right'] : rand(1,9);
		$_SESSION['left'] = $left;
		$_SESSION['right'] = $right;
		
		$_SESSION['sh'] = $right + $left;				
		
		$this->TPL->assign('captchaLeft', $left);
		$this->TPL->assign('captchaRight', $right);		
		
		$node = $obj_Menu->getOne($this->activeNode['id']);
		$page = $obj_Pages->getOne($node['page_id']);
		$submenu = $obj_Cms->getSubmenu($this->activeNode, false, false, true);
		
		$this->TPL->assign('Meta_Title', $page['meta_title']);
		$this->TPL->assign('Meta_Description', $page['meta_description']);
		$this->TPL->assign('Meta_Keywords', $page['meta_keywords']);
		
		if($page['fb_sharer_title'])
			$this->TPL->assign('Fb_Sharer_Title', $page['fb_sharer_title']);
		else 
			$this->TPL->assign('Fb_Sharer_Title', $page['meta_title']);
			
		if($page['fb_sharer_description'])
			$this->TPL->assign('Fb_Sharer_Description', $page['fb_sharer_description']);
		else 
			$this->TPL->assign('Fb_Sharer_Description', $page['meta_description']);
					
		$this->TPL->assign('Content_Title', $page['name']);				

		$this->TPL->assign('Page_Name', $page['name']);	
		
		$this->TPL->assign('Menu_ActiveNode', $this->activeNode);
		$this->TPL->assign('Menu_SubMenu', $submenu);	
	}
	
	protected function assignMenu($activeNode){
		$obj_Cms = Loader::loadModule('Cms');
		$obj_Menu = Loader::loadModule('Cms.Menu');
				
		$parentMenu = $obj_Menu->getOne($activeNode['parent_id']);
		
		if($parentMenu['menu_type'] == 'menu'){
			$a_menu = $obj_Cms->getMenu($parentMenu['menu_name'], false, $activeNode['id'], true);
			
			$this->a_Menus[] = $a_menu;
		} else {
			$a_menu = $obj_Cms->getSubMenu($parentMenu, false, $activeNode['id'], true);
			
			$this->a_Menus[] = array('menu' => '','nodes' => $a_menu);
			$this->assignMenu($parentMenu);
		}
	}
	
	protected function setUpMenu($activeNode = false){
		$obj_Cms = Loader::loadModule('Cms');
		$obj_Menu = Loader::loadModule('Cms.Menu');

//		$invisiblePage = false;				
		
		if(!$activeNode){
			if(defined('SHORT_URL'))
				$activeNode = SHORT_URL ? $obj_Cms->getActiveNodeByShortUrl(SHORT_URL) : false;
			else	
				$activeNode = ID_NAME ? $obj_Cms->getActiveNodeByShortUrl(ID_NAME) : false;
		}

		
	/*	if($activeNode){
			if(isset($activeNode['parent_id']) && $activeNode['parent_id']){
				$parent = $obj_Menu->getOne($activeNode['parent_id']);
				if($parent){
					if($parent['menu_type'] == 'menu' && $parent['menu_name'] != 'Hoofdmenu'){
						$activeNode = false;
						$invisiblePage = true;				
					}
				}
			}
		} */
		
				
		if($activeNode){
		
			$this->assignMenu($activeNode);
			
			$a_Menu = array();
			$a_SubMenu = array();
			$a_SubSubMenu = array();
						
			
			if($activeNode['id'] == '154' or $activeNode['parent_id'] == '154')
				$this->TPL->assign('PublicatiesImage', true);		
						
			switch(count($this->a_Menus)){
				case 1:
				
					// assign het hoofdmenu en het submenu voor de active one
					$this->TPL->assign('Hoofdmenu', $this->a_Menus[0]);
					$activeNode = $obj_Cms->getActiveNode($this->a_Menus[0]);
					$this->activeNode = $activeNode;	

					if($activeNode)
						$a_SubMenu = $obj_Cms->getSubMenu($activeNode, false, false, true);		
				
					$this->TPL->assign('Submenu', $a_SubMenu);
					$activeNode = $obj_Cms->getActiveNode($a_SubMenu);
					if($activeNode)
						$a_SubSubMenu = $obj_Cms->getSubMenu($activeNode, false, false, true);			
					$this->TPL->assign('SubSubmenu', $a_SubSubMenu);					
					
				break;
				case 2;				
					
					$this->TPL->assign('Submenu', $this->a_Menus[0]['nodes']);
					$this->TPL->assign('Hoofdmenu', $this->a_Menus[1]);
					$activeNode = $obj_Cms->getActiveNode($this->a_Menus[0]['nodes']);
					
					
					$this->activeNode = $activeNode;
					if($activeNode)		
						$a_SubSubMenu = $obj_Cms->getSubMenu($activeNode, false, false, true);			
					$this->TPL->assign('SubSubmenu', $a_SubSubMenu);					
					
				break;
				case 3;
				
					$this->activeNode = $obj_Cms->getActiveNode($this->a_Menus[0]['nodes']);
					$this->TPL->assign('SubSubmenu', $this->a_Menus[0]['nodes']);
					$this->TPL->assign('Submenu', $this->a_Menus[1]['nodes']);
					$this->TPL->assign('Hoofdmenu', $this->a_Menus[2]);
				break;
			}
			
		} else {
				
			// We hebben nog geen actieve node!
			
			$a_Menu = array();
			$a_SubMenu = array();
			$a_SubSubMenu = array();
		
			$a_Menu = $obj_Cms->getMenu('Hoofdmenu', true, false, true);
			$this->TPL->assign('Hoofdmenu', $a_Menu);
			
/*			if($invisiblePage){
				if(defined('SHORT_URL'))
					$activeNode = SHORT_URL ? $obj_Cms->getActiveNodeByShortUrl(SHORT_URL) : false;
				else	
					$activeNode = ID_NAME ? $obj_Cms->getActiveNodeByShortUrl(ID_NAME) : false;
				$a_Parent = $obj_Menu->getOne($activeNode['parent_id']);			
				$a_Menu = $obj_Cms->getMenu($a_Parent['menu_name'], false, false, true);
			} */
			
			$activeNode = $obj_Cms->getActiveNode($a_Menu);
			$this->activeNode = $activeNode;	

			if($activeNode){		
				$a_SubMenu = $obj_Cms->getSubMenu($activeNode, false, false, true);		
				$this->activeNode = $activeNode;
			}
				
			$this->TPL->assign('Submenu', $a_SubMenu);
			$activeNode = $obj_Cms->getActiveNode($a_SubMenu);
			
			if($activeNode){
				$a_SubSubMenu = $obj_Cms->getSubMenu($activeNode, false, false, true);			
				$this->activeNode = $activeNode;
			}
			$this->TPL->assign('SubSubmenu', $a_SubSubMenu);
		}	
		
		$this->setUpKruimelpad();	
		
		$a_Menus = $obj_Cms->getMenus();
		foreach($a_Menus as $menu)
			if($menu['menu_name'] != 'Hoofdmenu'){
				$_menu = $obj_Cms->getMenu($menu['menu_name'], false, false, true);
				$this->TPL->assign($menu['menu_name'], $_menu['nodes']);
			}
	}
	
	
	
	protected function replacelinks($a_Input){
		$module = $a_Input[1];
		$id = $a_Input[2];
				
		switch($module){
			default:				
				$obj_Module = Loader::loadModule(ucfirst($module));
				$a_Item = $obj_Module->getOne($id);
				return APPLICATION_URL_SHORT.'/'.$module.'/'.$a_Item['short_url'];
			break;
			case 'page':
				$obj_Module = Loader::loadModule('Cms.Menu');
				$item = $obj_Module->getOne($id);
				$obj_Module = Loader::loadModule('Cms.Pages');
				$item = $obj_Module->getOne($item['page_id']);
				return $item ? APPLICATION_URL_SHORT.'/'.$item['short_url'] : false;
			break;
			case 'file':		
				return APPLICATION_URL.'/'.$module.'/view/'.$id;
			break;	
		}		
	}
	
	
	
	
	protected function striptags($input){
		$obj_Html = Loader::load('Html');
		$obj_Html->str_get_html($input);
		
		foreach($obj_Html->find('p') as $element)
			if(!preg_match('/\w/', $element->innertext))
				$element->outertext = '';			
			
		foreach($obj_Html->find('p.comment') as $element)
			$element->outertext = '';					
					
		foreach($obj_Html->find('p.dummy') as $element)
			$element->outertext = '';					 
		
		return $obj_Html->save();
	}
		
	protected function parseBodyTekst($text){
		$parsed = preg_replace_callback("/\{APPLICATION_URL\/(\w*)\/([^\s]*)\}/u", array($this, 'replacelinks'), $text);	
		return $this->striptags($parsed);
	}
	
	protected function pushKruimelpad($name){
		$a_Kruimelpad = $this->setUpKruimelpad();
		$a_Kruimelpad[] = array('name' => $name);
		$this->TPL->assign('Kruimelpad', $a_Kruimelpad);
	}
	
	protected function setUpKruimelpad($invisiblePage = false){
		$obj_Cms = Loader::loadModule('Cms');
		$hoofdmenu = $this->TPL->getVar('Hoofdmenu');		
		$submenu = $this->TPL->getVar('Submenu');
		$subsubmenu = $this->TPL->getVar('SubSubmenu');
		
		
		$a_Kruimelpad = array();
		if($hoofdmenu){			
			$hoofdnode = $obj_Cms->getActiveNode($hoofdmenu);
			if($hoofdnode)
				$a_Kruimelpad[] = $hoofdnode;
		}
		
		if($submenu){
			$subnode = $obj_Cms->getActiveNode($submenu);
			if($subnode)
				$a_Kruimelpad[] = $subnode;
		}
		
		if($subsubmenu){
			$subsubnode = $obj_Cms->getActiveNode($subsubmenu);
			if($subsubnode)
				$a_Kruimelpad[] = $subsubnode;
		}
		
		$this->TPL->assign('Kruimelpad', $a_Kruimelpad);
		return $a_Kruimelpad;
	}
	
	protected function validateForm($form){
		$obj_Forms = Loader::loadModule('Forms');
		$a_Elements = $obj_Forms->get(array('form_id' => $form['id'], 'status' => true), array('order' => 'sequence_id DESC'), array(), 'elements');
		$a_ErrorFields = array();
		foreach($a_Elements as $element){
			
			$name = $element['fieldname'];
			$label = $element['label'];
			if($element['type'] == 'date'){
				$postedValue = $this->a_Post[$name.'_year'].'-'.$this->a_Post[$name.'_month'].'-'.$this->a_Post[$name.'_day']. ' (j-m-d)';
			} else {
				$postedValue = isset($this->a_Post[$name]) ? $this->a_Post[$name] : false;
			}
			
			if($element['type'] != 'sep' && $element['type'] != 'comment' && $element['type'] != 'header'){
				$this->a_Post[$name] = $postedValue;
			}
			
			unset($this->a_Post['sep']);
			unset($this->a_Post['comment']);
			unset($this->a_Post['header']);			

			if($element['required'])
				if(!$postedValue or ($postedValue == '')){
					$this->MESSAGES->push("Het veld '$label' is nog leeg.", 'error');
					$a_ErrorFields[$name] = true;
				}
				
			require_once 'Zend/Validate.php';
			
			switch($element['type']){
				case 'emailadres':
					if($postedValue)									
						if(!Zend_Validate::is($postedValue, 'EmailAddress')) {
						$this->MESSAGES->push("Er is geen geldig emailadres ingevuld in veld '$label'.", 'error');
						$a_ErrorFields[$name] = true;
					}				
				
				break;
				case 'date':				
					$day = $this->a_Post[$name.'_day'];
					$month = $this->a_Post[$name.'_month'];
					$year = $this->a_Post[$name.'_year'];
					
					unset($this->a_Post[$name.'_day']); // Deze hebben we niet nodig, kolom bestaat niet in de DB..
					unset($this->a_Post[$name.'_month']); // Deze hebben we niet nodig, kolom bestaat niet in de DB..
					unset($this->a_Post[$name.'_year']); // Deze hebben we niet nodig, kolom bestaat niet in de DB..
						
					if(!checkdate($month, $day, $year)){
						$this->MESSAGES->push("Er is geen geldige datum ingevuld in het veld '$label'.", 'error');
						$a_ErrorFields[$name] = true;
					}
									
				break;			
			}
		}
		
		if(isset($this->a_Post['sh'])){
			if(trim($this->a_Post['sh']) != $_SESSION['sh'] or !$this->a_Post['sh']){
				$this->MESSAGES->push("Het sommetje is niet goed ingevuld.", 'error');
				$a_ErrorFields['sh'] = true;	
			}
		}		
		
 		if(count($a_ErrorFields)>=1){
			$this->TPL->assign($form['id'].'_ErrorFields', $a_ErrorFields);		
 			return false;
 		} else
 			return true; 		
	}

	protected function handleForm($element, $element_id){
		$obj_Forms = Loader::loadModule('Forms');
		$a_Form = $obj_Forms->get(array($element_id => $element['id'], 'status' => true));
		if($a_Form){
			$obj_Tools = Loader::load('Tools');
			$this->a_Post = $obj_Tools->ReadPost();
			if(isset($this->a_Post['submitted_'.$a_Form[0]['id']]) && $this->a_Post['submitted_'.$a_Form[0]['id']]){
 				unset($this->a_Post['submitted_'.$a_Form[0]['id']]);						
 						
 				if($this->validateForm($a_Form[0])){
 					try {
 					
	 					$a_Elements = $obj_Forms->get(array('form_id' => $a_Form[0]['id'], 'status' => true), array('order' => 'sequence_id ASC'), array(), 'elements');
 					
 						$scsmsg = $a_Form[0]['successmessage'] ? $a_Form[0]['successmessage'] : 'Het formulier is succesvol verstuurd!';
 						$obj_Email = loader::loadModule('Email');
						$obj_Email->sendForm($a_Form[0], $this->a_Post, $a_Elements);
						$this->MESSAGES->push($scsmsg, 'success');
						$this->TPL->assign('Form_Success', true);
					} catch(Exception $e){
						$this->MESSAGES->push('Het formulier kon niet verstuurd worden.', 'error');
						$this->MESSAGES->push('Er is een algemene fout opgetreden', 'general');
						dump("[CRITICAL ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());						
					}						
 				}	
 				
				$a_ElementsToAssign = array();		
				$a_Elements = $obj_Forms->get(array('form_id' => $a_Form[0]['id'], 'status' => true), array('order' => 'sequence_id ASC'), array(), 'elements');
				foreach($a_Elements as $element){			
					$a = array();
					$a = $element;
					$a['value'] = isset($this->a_Post[$element['fieldname']]) ? $this->a_Post[$element['fieldname']] : (($element['type'] == 'checkbox') ? false : $element['value']);
					$a_ElementsToAssign[] = $a;
				}
 											
			} else {
				$a_ElementsToAssign = $obj_Forms->get(array('form_id' => $a_Form[0]['id'], 'status' => true), array('order' => 'sequence_id ASC'), array(), 'elements');
			}		
			
			$this->TPL->assign('Content_Form', $a_Form[0]);
			$this->TPL->assign('Content_FormElements', $a_ElementsToAssign);
		}	
	}
	
}