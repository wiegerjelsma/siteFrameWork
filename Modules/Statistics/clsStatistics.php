<?php
/**
 * @name	m_StatisticsException
 * @author 	wiegerjelsma
 */
class m_StatisticsException extends m_ModuleException {}

/**
 * @name	m_Statistics
 * @author 	wiegerjelsma
 */
class m_Statistics extends m_Module {			
	
	private $_session_id;
	private $_is_robot;			
	private $_visitor_id;
	private $_referer;
		
	/**
	 * @name	init 
	 */
	public function init(){
		parent::init();
		$this->setUpSession();
		
		$this->_is_robot = $this->SESSION->read('_is_robot') ? $this->SESSION->read('_is_robot') : 'NOT SET';
		$this->_visitor_id = $this->SESSION->read('_visitor_id') ? $this->SESSION->read('_visitor_id') : false;
		$this->_referer = $this->SESSION->read('_referer') ? $this->SESSION->read('_referer') : false;		
		
		if($this->_is_robot == 'NOT SET'){
			$obj_Client = Loader::LoadLibrary('Client');
			$this->_is_robot = $obj_Client->is_robot();
			$this->SESSION->write('_is_robot', $this->_is_robot);
		}
					
		if(!$this->_visitor_id){			
			if(!$this->_is_robot){
				$this->_visitor_id = $this->saveVisitor();
				$this->SESSION->write('_visitor_id', $this->_visitor_id);			
			}
		} 
		
		$this->_session_id = $this->SESSION->getID();		
	}
	
	/**
	 * @name	getVisitor
	 */
	public function getVisitor($a_Params = array()){
		return;
		try {
			$select = $this->DB->select();						
			$select->from(array('b' => $this->cfg['db']['tables']['M_statistics_visitors']));						
			foreach($a_Params as $key => $value)
				$select->where('b.'.$key.' = ?', $value);								
			$a_Res = $this->DB->query($select)->fetchAll();			
			return (count($a_Res)>0) ? $a_Res[0] : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}	
	}
	
	/**
	 * @name	getCountry
	 */
	public function getCountry($country_code){
		return;		try {
			$select = $this->DB->select();						
			$select->from(array('b' => $this->cfg['db']['tables']['M_statistics_visitors']),'country');						
			$select->where('b.country_code = ?', $country_code);								
			return $this->DB->fetchOne($select);			
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}	
	}
	
	
	
	/**
	 * @name	getTotalVisits
	 * @returns	number
	 */
	public function getTotalVisits($a_Params = false, $from_type = 'days', $from_period = false, $til_period = false){
			return;
				return $this->getVisits($a_Params, $from_type, $from_period, $til_period, true);	
	}
	
	
	/**
	 * @name	getVisits
	 * @returns	array(date => visits) per day|month|year
	 */
	public function getVisits($a_Params = false, $from_type = 'days', $from_period = false, $til_period = false, $total = false){
		return;		try {
			$unique = isset($a_Params['unique']) ? $a_Params['unique'] : false;
			$returning = isset($a_Params['returning']) ? $a_Params['returning'] : false;			
			$visitors = isset($a_Params['visitors']) ? $a_Params['visitors'] : false;			
			
			$print = isset($a_Params['print']) ? $a_Params['print'] : false;			
			
			$subdomain = isset($a_Params['subdomain']) ? $a_Params['subdomain'] : SUBDOMAIN;
			$application_name = isset($a_Params['application_name']) ? $a_Params['application_name'] : APPLICATION_NAME;
			$country_code = isset($a_Params['country_code']) ? $a_Params['country_code'] : false;
			$order = isset($a_Params['order']) ? $a_Params['order'] : false;
			
			$til_period = $til_period ? $til_period: false;

			$per_country = isset($a_Params['per_country']) ? $a_Params['per_country'] : false;
			$per_city = isset($a_Params['per_city']) ? ($a_Params['per_city'] ? $a_Params['per_city'] : $country_code) : false;
			
			$select = $this->DB->select();
			
			// Set the table
			$table = $unique ? 'V_statistics_visitors-unique-' : ($returning ? 'V_statistics_visitors-returning-': ($visitors ? 'V_statistics_visitors-' : 'V_statistics_visits-'));
			if($per_country)
				$table .= 'country-';
			elseif($per_city)
				$table .= 'city-';				
			elseif($country_code)
				$table .= 'country-';
				
			if($from_type == 'year')
				$table .= 'year';	
			elseif($from_type == 'month')
				$table .= 'month';	
			elseif($from_type == 'days')
				$table .= 'day';				
											
			// Set the select FROM	
			if($per_country)
				$select->from(array('b' => $this->cfg['db']['views'][$table]), array('visits' => 'SUM(b.visits)', 'b.country', 'b.country_code'));
			elseif($per_city)
				$select->from(array('b' => $this->cfg['db']['views'][$table]), array('visits' => 'SUM(b.visits)', 'b.city'));
			elseif($total)
				$select->from(array('b' => $this->cfg['db']['views'][$table]), array('visits' => 'SUM(b.visits)'));						
			else
				$select->from(array('b' => $this->cfg['db']['views'][$table]));
								
			// Set the WHERE
			switch($from_type){
				default:
				case 'days':
					$from_period = $from_period ? $from_period : '29';				
					$from = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-$from_period, date("Y")));
					$til = $til_period ? date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-($from_period+$til_period), date("Y"))) : date('Y-m-d');
										
					$select->where('b.day >= ?', $from);
					$select->where('b.day <= ?', $til);
				break;
				case 'month':
					$from_period = $from_period ? $from_period : '1';
					$from_month = date('m', mktime(0, 0, 0, date("m")-$from_period, date("d"), date("Y")));
					$from_year = date('Y', mktime(0, 0, 0, date("m")-$from_period, date("d"), date("Y")));
					$til_month = $til_period ? date('m', mktime(0, 0, 0, date("m")-($from_period+$til_period), date("d"), date("Y"))) : date('m');
					$til_year = $til_period ? date('Y', mktime(0, 0, 0, date("m")-($from_period+$til_period), date("d"), date("Y"))) : date('Y');
					$select->where(
    					$this->DB->quoteInto('((b.month >= ?', $from_month).
    					$this->DB->quoteInfo(' AND b.year = ?)', $from_year).
   	 					$this->DB->quoteInfo(' OR (b.month <= ?', $til_month).
   	 					$this->DB->quoteInfo(' AND b.year = ? ))', $til_year)
					);		
				break;
				case 'year':
					$from_period = $from_period ? $from_period : '1';
					$from = date('Y', mktime(0, 0, 0, date("m"), date("d"), date("Y")-$from_period));
					$til = $til_period ? date('Y', mktime(0, 0, 0, date("m"), date("d"), date("Y")-($from_period+$til_period))) : date('Y');			
					$select->where('b.year >= ?', $from);
					$select->where('b.year <= ?', $til);					
				break;			
			}		
			
			$select->where('b.subdomain = ?', $subdomain);								
			$select->where('b.application_name = ?', $application_name);
			
			if($country_code)
				$select->where('b.country_code = ?', $country_code);
			elseif($per_city)
				$select->where('b.country_code = ?', $per_city);
						
			
			// Set the GROUP BY
			if($per_country)
				$select->group('b.country');
			if($per_city)
				$select->group('b.city');
													
									
			// Set the Order By
			if($order){
				list($key, $direction) = explode(' ', $order);
				switch($key){
					default:
					break;					
					case 'b.visits_total':
						$select->order('b.visits '.$direction);
					break;
					case 'b.country':
						$select->order($order);
					break;				
				}
			}
			
			if($print)
				dump($select->__toString());
				
			if($total)
				return $this->DB->fetchOne($select);
				
			if($per_country or $per_city)
				return $this->DB->query($select)->fetchAll();	
			
			$a_Return = array();
			$a_Visits = $this->DB->query($select)->fetchAll();
			$a_VisitDate = array();
			foreach($a_Visits as $visit)
				$a_VisitDate[$visit['day']] = $visit['visits'];
				
			$a_DateRange = $this->createDateRangeArray($from, $til);
			foreach($a_DateRange as $date)
				$a_Return[$date] = isset($a_VisitDate[$date]) ? $a_VisitDate[$date] : 0;
			
			return $a_Return;
   		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}
	}

	
	
	/**
	 * @name	createDateRangeArray
	 */
	private function createDateRangeArray($strDateFrom,$strDateTo) {
		return; 		
 		// takes two dates formatted as YYYY-MM-DD and creates an
  		// inclusive array of the dates between the from and to dates.
  		// could test validity of dates here but I'm already doing
  		// that in the main script
  		$aryRange=array();
  		$iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
  		$iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));
  		if($iDateTo>=$iDateFrom){
    		array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
   	 		while ($iDateFrom<$iDateTo) {
      			$iDateFrom+=86400; // add 24 hours
      			array_push($aryRange,date('Y-m-d',$iDateFrom));
    		}
  		}
  		return $aryRange;
	}	
		
	/**
	 * @name	saveVisitor
	 */
	public function saveVisitor(){
		return;		
		if($this->_is_robot)
			return;

		try {
			$obj_Client = Loader::Load('Client');
			$client_ip = $obj_Client->getIp();		
			$a_Location = $obj_Client->getLocation();
			
			$a_Visitor = array();
			$a_Visitor['ipaddress'] = $client_ip;
			$a_Visitor['country_code'] = $a_Location['country_code'];
			$a_Visitor['country'] = $a_Location['country'];			
			$a_Visitor['city'] = $a_Location['city'];
			
			$visitor = $this->getVisitor(array('ipaddress' => $client_ip));
			if($visitor)
				return $visitor['id'];
			
			$this->DB->insert($this->cfg['db']['tables']['M_statistics_visitors'], $a_Visitor);
			return $this->DB->lastInsertId();
   		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}			
	}
	
	/**
	 * @name	savePageView
	 */
	public function savePageView(){
		return;		
		if($this->_is_robot)
			return;

		try {
			$a_PageView = array();
			$a_PageView['application_name'] = APPLICATION_NAME;
			$a_PageView['session_id'] = $this->_session_id;
			$a_PageView['visitor_id'] = $this->_visitor_id;			
			$a_PageView['protocol'] = PROTOCOL;
			$a_PageView['subdomain'] = SUBDOMAIN;
			$a_PageView['domain'] = DOMAIN;
			$a_PageView['tld'] = TLD;
			$a_PageView['controller_name'] = CONTROLLER_NAME;
			$a_PageView['function_name'] = FUNCTION_NAME;
			$a_PageView['id_name'] = ID_NAME;
			$a_PageView['controller_param'] = CONTROLLER_PARAM;
			$a_PageView['function_param'] = FUNCTION_PARAM;
			$a_PageView['id_param'] = ID_PARAM;						
			$a_PageView['querystring'] = QUERYSTRING;		
			if(!$this->_referer){
				$obj_Client = Loader::Load('Client');
				$referer = $obj_Client->referrer();				 
				if($referer && !preg_match('/'.DOMAIN.'.'.TLD.'/', $referer)){
					$this->_referer = true;
					$this->SESSION->write('_referer', $this->_referer);	
					$a_PageView['referer'] = $referer;				
				}				
			}
			
			$this->DB->insert($this->cfg['db']['tables']['M_statistics_pageviews'], $a_PageView);
			return $this->DB->lastInsertId();
			
   		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}		
	}
	
	/**
	 * @name	saveEvent
	 */
	public function saveEvent($category, $action, $label = false, $value = false){
		return;
				if($this->_is_robot)
			return;

		try {			
			$a_Event = array();
			$a_Event['application_name'] = APPLICATION_NAME;
			$a_Event['session_id'] = $this->_session_id;
			$a_Event['visitor_id'] = $this->_visitor_id;
			$a_Event['category'] = $category;
			$a_Event['action'] = $action;
			$a_Event['label'] = $label;
			$a_Event['value'] = $value;
			
			$this->DB->insert($this->cfg['db']['tables']['M_statistics_events'], $a_Event);
			return $this->DB->lastInsertId();
			
   		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}		
	}	
}