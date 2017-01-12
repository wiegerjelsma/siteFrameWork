<?php
/**
 * @name	c_site_back_user_StatisticsException
 * @author 	wiegerjelsma
 * @version	1.0 2012-09-18 12:08:55
 */
class c_site_back_user_StatisticsException extends c_site_back_StatisticsException {}

/**
 * @name	c_site_back_user_Statistics
 * @author 	wiegerjelsma
 * @version	1.0 2012-09-18 12:08:55
 */
class c_site_back_user_Statistics extends c_site_back_Statistics {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-09-18 12:08:55
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
		$this->setUpModule();
	}
	
	
	public function country(){
		$country = $this->obj_Module->getCountry(ID_NAME);
		$this->view(ID_NAME, $country);
	}
	
	/**
	 * @name	statistics
	 * @desc	toont ons de statistieken voor deze gebruiker
	 */
	public function view($country_code = false, $country = false){
		$a_LoggedInUser = $this->SESSION->read('loggedin_user');
		
		$application_name = FWPREFIX.'.front';
		
		$a_Visits = $this->obj_Module->getVisits(array('country_code' => $country_code, 'application_name' => $application_name));
		$a_UniqueVisits = $this->obj_Module->getVisits(array('country_code' => $country_code, 'unique' => true, 'application_name' => $application_name));		
		$a_ReturningVisits = $this->obj_Module->getVisits(array('country_code' => $country_code, 'returning' => true, 'application_name' => $application_name));
		
		$a_TotalVisits = $this->obj_Module->getTotalVisits(array('country_code' => $country_code, 'application_name' => $application_name));
		$a_TotalVisitors = $this->obj_Module->getTotalVisits(array('country_code' => $country_code, 'visitors' => true, 'application_name' => $application_name));
		$a_TotalUniqueVisitors = $this->obj_Module->getTotalVisits(array('country_code' => $country_code, 'unique' => true, 'application_name' => $application_name));		
		$a_TotalReturningVisitors = $this->obj_Module->getTotalVisits(array('country_code' => $country_code, 'returning' => true, 'application_name' => $application_name));		

		$perc_Unique = $this->getPercentage('unique', $a_TotalUniqueVisitors, $a_TotalReturningVisitors);
		$this->TPL->assign('UniqueVisitsPercentage', str_replace(',','.',$perc_Unique));
		if($a_TotalReturningVisitors)
			$this->TPL->assign('ReturningVisitsPercentage', str_replace(',','.',100 - $perc_Unique));
		else
			$this->TPL->assign('ReturningVisitsPercentage', 0);

		$this->TPL->assign('VisitsHeader', 'Totaal dagelijkse bezoeken');
		$this->TPL->assign('VisitsSubheader', 'Laatste '.count($a_Visits).' dagen');				
		$this->TPL->assign('UniqueVersusReturningVisitsHeader', 'Nieuwe en terugkerende bezoeken');
		$this->TPL->assign('UniqueVersusReturningVisitsSubheader', 'Laatste '.count($a_Visits).' dagen');				
		
		$this->TPL->assign('VisitsHeaderXAxis', 'Bezoeken');

		$a_VisitsToAssign = array();
		foreach($a_Visits as $date => $visits)
			$a_VisitsToAssign[date('M dS', strtotime($date))] = $visits;		
		
		$this->TPL->assign('Visits', $a_VisitsToAssign);
		
		$this->TPL->assign('TotalVisits', $a_TotalVisits);
		$this->TPL->assign('TotalVisitors', $a_TotalVisitors);		
		$this->TPL->assign('UniqueVisitors', $a_TotalUniqueVisitors);
		$this->TPL->assign('ReturningVisitors', $a_TotalReturningVisitors);
				
		$this->TPL->assign('Titel', 'Bezoekersoverzicht');
		
		$subtitel = 'Laatste '.count($a_Visits).' dagen';
		if($country_code)
			$subtitel .= ' <span class=\'small\'>uit</span> '.$country;
		
		$this->TPL->assign('Subtitel', $subtitel);
		
		$gridtitel = $country_code ? 'Plaatsen' : 'Landen';
		$gridsublink = $country_code ? APPLICATION_URL.'/'.CONTROLLER_NAME : false;
		$gridsubtitel = !$country_code ? 'Klik op de landen om hierop te filteren' : false;
		
		$this->TPL->assign('GridTitel', $gridtitel);
		$this->TPL->assign('GridSubTitel', $gridsubtitel);
		$this->TPL->assign('GridSubLink', $gridsublink);
		
		$this->setGrid($country_code);
		
		$this->TPL->display('statistics.tpl');	
	}
	
	protected function getPercentage($type = 'unique', $unique, $returning){
		$perc_unique = ($returning or $unique) ? round((100 / ($unique + $returning)) * $unique, 2) : 0;
		return $type == 'unique' ? $perc_unique : 100 - $perc_unique;
	}
	
	protected function setGrid($country_code = false){
		$a_LoggedInUser = $this->SESSION->read('loggedin_user');
		$application_name = FWPREFIX.'.front';
			
		$this->readPost();
		
 	//	$a_Search = $this->setUpSearch();
 		$a_Search = isset($a_Search) && $a_Search ? $a_Search : array();
 		
 		$a_Order = $this->setUpOrder();
 		$a_Order = $a_Order ? array('order' => 'b.'.$a_Order['key'].' '.$a_Order['direction']) : array('order' => 'b.country ASC');
		 	
		if($country_code){ // Per city voor dit land
			$a_VisitsPer = $this->obj_Module->getVisits(array('per_city' => true, 'country_code' => $country_code, 'application_name' => $application_name, 'order' => $a_Order['order']));
			$a_VisitorsPer = $this->obj_Module->getVisits(array('per_city' => true, 'country_code' => $country_code, 'visitors' => true, 'application_name' => $application_name, 'order' => $a_Order['order']));			
			$a_VisitsUniquePer = $this->obj_Module->getVisits(array('per_city' => true, 'country_code' => $country_code, 'unique' => true, 'application_name' => $application_name, 'order' => $a_Order['order']));
			$a_VisitsReturningPer = $this->obj_Module->getVisits(array('per_city' => true, 'country_code' => $country_code, 'returning' => true, 'application_name' => $application_name, 'order' => $a_Order['order']));
						
			$a_VisitsPerRef = array();
			foreach($a_VisitsPer as $a_Res)
				$a_VisitsPerRef[md5($a_Res['city'])] = $a_Res;
			
			$a_VisitorsPerRef = array();
			foreach($a_VisitorsPer as $a_Res)
				$a_VisitorsPerRef[md5($a_Res['city'])] = $a_Res;
						
			$a_VisitsUniquePerRef = array();
			foreach($a_VisitsUniquePer as $a_Res)
				$a_VisitsUniquePerRef[md5($a_Res['city'])] = $a_Res;
			
			$a_VisitsReturningPerRef = array();
			foreach($a_VisitsReturningPer as $a_Res)
				$a_VisitsReturningPerRef[md5($a_Res['city'])] = $a_Res;				
				
			$a_Result = array();	
			foreach($a_VisitsPerRef as $hash => $a_City){
				$record = array();
				
				$unique = isset($a_VisitsUniquePerRef[$hash]) ? $a_VisitsUniquePerRef[$hash]['visits'] : 0;
				$returning = isset($a_VisitsReturningPerRef[$hash]) ? $a_VisitsReturningPerRef[$hash]['visits'] : 0;
				$visitors = isset($a_VisitorsPerRef[$hash]) ? $a_VisitorsPerRef[$hash]['visits'] : 0;
				
				$record['id'] = $hash;
				$record['city'] = $a_City['city'];
				$record['visits_total'] = $a_City['visits'];				
				$record['visitors_total'] = $visitors;
				$record['visitors_unique'] = $unique;
				$record['visitors_returning'] = $returning;
				$record['visitors_perc_unique'] = $this->getPercentage('unique', $unique, $returning).'%';
				$record['visitors_perc_returning'] = $this->getPercentage('returning', $unique, $returning).'%';
				$a_Result[] = $record;
			}
			$this->cfg['view'] = $this->cfg['city']['view'];			
							
		} else {
			$a_VisitsPer = $this->obj_Module->getVisits(array('per_country' => true, 'application_name' => $application_name, 'order' => $a_Order['order']));
			$a_VisitorsPer = $this->obj_Module->getVisits(array('visitors' => true, 'per_country' => true, 'application_name' => $application_name, 'order' => $a_Order['order']));			
			$a_VisitorsUniquePer = $this->obj_Module->getVisits(array('per_country' => true, 'unique' => true, 'application_name' => $application_name, 'order' => $a_Order['order']));
			$a_VisitorsReturningPer = $this->obj_Module->getVisits(array('per_country' => true, 'returning' => true, 'application_name' => $application_name, 'order' => $a_Order['order']));
			
			$a_VisitsPerRef = array();
			foreach($a_VisitsPer as $a_Res)
				$a_VisitsPerRef[$a_Res['country_code']] = $a_Res;

			$a_VisitorsPerRef = array();
			foreach($a_VisitorsPer as $a_Res)
				$a_VisitorsPerRef[$a_Res['country_code']] = $a_Res;
			
			$a_VisitorsUniquePerRef = array();
			foreach($a_VisitorsUniquePer as $a_Res)
				$a_VisitorsUniquePerRef[$a_Res['country_code']] = $a_Res;
			
			$a_VisitorsReturningPerRef = array();
			foreach($a_VisitorsReturningPer as $a_Res)
				$a_VisitorsReturningPerRef[$a_Res['country_code']] = $a_Res;
				
			$a_Result = array();	
			foreach($a_VisitsPerRef as $country_code => $a_Country){
				$record = array();
				
				$unique = isset($a_VisitorsUniquePerRef[$country_code]) ? $a_VisitorsUniquePerRef[$country_code]['visits'] : 0;
				$returning = isset($a_VisitorsReturningPerRef[$country_code]) ? $a_VisitorsReturningPerRef[$country_code]['visits'] : 0;
				$visitors = isset($a_VisitorsPerRef[$country_code]) ? $a_VisitorsPerRef[$country_code]['visits'] : 0;
				
				$record['id'] = $country_code;
				$record['country_code'] = $country_code;
				$record['country'] = $a_Country['country'];
				$record['visits_total'] = $a_Country['visits'];
				$record['visitors_total'] = $visitors;				
				$record['visitors_unique'] = $unique;
				$record['visitors_returning'] = $returning;
				$record['visitors_perc_unique'] = $this->getPercentage('unique', $unique, $returning).'%';
				$record['visitors_perc_returning'] = $this->getPercentage('returning', $unique, $returning).'%';
				$a_Result[] = $record;
			}	
			
			$this->cfg['view'] = $this->cfg['country']['view'];
			
			// the grid config
			$this->cfg['view']['country']['url'] = APPLICATION_URL.'/'.CONTROLLER_NAME.'/country/{id}';			
		}		
		
		$a_Header = $this->getDataHeader();
		$a_HeaderKeys = array_keys($a_Header);
				
		if($a_Result)
			$a_Dataset = $this->formatResult($a_Result, $a_HeaderKeys);
		else
			$a_Dataset = false;		
		
 		$a_Buttons = false;
 		if(isset($this->cfg['access']['actions']['add']) && $this->cfg['access']['actions']['add'])
	 		$a_Buttons[] = array('url' => APPLICATION_URL.'/'.CONTROLLER_NAME.'/add', 'value' => 'toevoegen');
 		
		$this->TPL->assign('Buttons', $a_Buttons); 			 		
		$this->TPL->assign('DataHeader', $a_Header);
		$this->TPL->assign('DataSet', $a_Dataset);				
	}
	
	public function test(){
		$this->setUpModule();
		print "<pre>";
		// Haal de data voor de grafiek voor NL.
//		print_r($this->obj_Module->getVisits(array('country_code' => 'NL', 'subdomain' => 'medotdesign', 'application_name' => 'cs.front')));
		
		// Haal per land de visits
		print_r($this->obj_Module->getVisits(array('per_country' => true, 'application_name' => FWPREFIX.'.front')));
		
		// Haal per land de unieke visits
		//print_r($this->obj_Module->getVisits(array('per_country' => true, 'unique' => true, 'subdomain' => 'medotdesign', 'application_name' => 'cs.front')));

		// Haal per land de returning visits
		//print_r($this->obj_Module->getVisits(array('per_country' => true, 'returning' => true, 'subdomain' => 'medotdesign', 'application_name' => 'cs.front')));
				
		// Haal per stad de visits
		//print_r($this->obj_Module->getVisits(array('per_city' => true, 'country_code' => 'NL', 'subdomain' => 'medotdesign', 'application_name' => 'cs.front')));

		// Haal per stad de unieke visits
		//print_r($this->obj_Module->getVisits(array('per_city' => true, 'country_code' => 'NL', 'unique' => true, 'subdomain' => 'medotdesign', 'application_name' => 'cs.front')));

		// Haal per stad de returning visits
		//print_r($this->obj_Module->getVisits(array('per_city' => true, 'country_code' => 'NL', 'returning' => true, 'subdomain' => 'medotdesign', 'application_name' => 'cs.front')));		
	}
}
