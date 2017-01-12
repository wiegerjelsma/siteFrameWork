<?php
class Client extends Library {
	
	protected static $instance;
	
	private static $a_Client = array();
	
	
	var $agent		= NULL;

	var $is_browser	= FALSE;
	var $is_robot	= FALSE;
	var $is_mobile	= FALSE;

	var $languages	= array();
	var $charsets	= array();

	var $platforms	= array();
	var $browsers	= array();
	var $mobiles	= array();
	var $robots		= array();

	var $platform	= '';
	var $browser	= '';
	var $version	= '';
	var $mobile		= '';
	var $robot		= '';

	/**
	 * Constructor
	 *
	 * Sets the User Agent and runs the compilation routine
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			$this->agent = trim($_SERVER['HTTP_USER_AGENT']);
		}

		if ( ! is_null($this->agent))
		{
			if ($this->_load_agent_file())
			{
				$this->_compile_data();
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Compile the User Agent Data
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _load_agent_file()
	{
		$return = FALSE;

		if (isset($this->cfg['platforms']))
		{
			$this->platforms = $platforms;
			unset($platforms);
			$return = TRUE;
		}

		if (isset($this->cfg['browsers']))
		{
			$this->browsers = $browsers;
			unset($browsers);
			$return = TRUE;
		}

		if (isset($this->cfg['mobiles']))
		{
			$this->mobiles = $mobiles;
			unset($mobiles);
			$return = TRUE;
		}

		if (isset($this->cfg['robots']))
		{
			$this->robots = $robots;
			unset($robots);
			$return = TRUE;
		}

		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * Compile the User Agent Data
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _compile_data()
	{
		$this->_set_platform();

		foreach (array('_set_robot', '_set_browser', '_set_mobile') as $function)
		{
			if ($this->$function() === TRUE)
			{
				break;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Platform
	 *
	 * @access	private
	 * @return	mixed
	 */
	private function _set_platform()
	{
		if (is_array($this->platforms) AND count($this->platforms) > 0)
		{
			foreach ($this->platforms as $key => $val)
			{
				if (preg_match("|".preg_quote($key)."|i", $this->agent))
				{
					$this->platform = $val;
					return TRUE;
				}
			}
		}
		$this->platform = 'Unknown Platform';
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Browser
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _set_browser()
	{
		if (is_array($this->browsers) AND count($this->browsers) > 0)
		{
			foreach ($this->browsers as $key => $val)
			{
				if (preg_match("|".preg_quote($key).".*?([0-9\.]+)|i", $this->agent, $match))
				{
					$this->is_browser = TRUE;
					$this->version = $match[1];
					$this->browser = $val;
					$this->_set_mobile();
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Robot
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _set_robot()
	{
		if (is_array($this->robots) AND count($this->robots) > 0)
		{
			foreach ($this->robots as $key => $val)
			{
				if (preg_match("|".preg_quote($key)."|i", $this->agent))
				{
					$this->is_robot = TRUE;
					$this->robot = $val;
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Mobile Device
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _set_mobile()
	{
		if (is_array($this->mobiles) AND count($this->mobiles) > 0)
		{
			foreach ($this->mobiles as $key => $val)
			{
				if (FALSE !== (strpos(strtolower($this->agent), $key)))
				{
					$this->is_mobile = TRUE;
					$this->mobile = $val;
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the accepted languages
	 *
	 * @access	private
	 * @return	void
	 */
	private function _set_languages()
	{
		if ((count($this->languages) == 0) AND isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) AND $_SERVER['HTTP_ACCEPT_LANGUAGE'] != '')
		{
			$languages = preg_replace('/(;q=[0-9\.]+)/i', '', strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE'])));

			$this->languages = explode(',', $languages);
		}

		if (count($this->languages) == 0)
		{
			$this->languages = array('Undefined');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set the accepted character sets
	 *
	 * @access	private
	 * @return	void
	 */
	private function _set_charsets()
	{
		if ((count($this->charsets) == 0) AND isset($_SERVER['HTTP_ACCEPT_CHARSET']) AND $_SERVER['HTTP_ACCEPT_CHARSET'] != '')
		{
			$charsets = preg_replace('/(;q=.+)/i', '', strtolower(trim($_SERVER['HTTP_ACCEPT_CHARSET'])));

			$this->charsets = explode(',', $charsets);
		}

		if (count($this->charsets) == 0)
		{
			$this->charsets = array('Undefined');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Is Browser
	 *
	 * @access	public
	 * @return	bool
	 */
	public function is_browser($key = NULL)
	{
		if ( ! $this->is_browser)
		{
			return FALSE;
		}

		// No need to be specific, it's a browser
		if ($key === NULL)
		{
			return TRUE;
		}

		// Check for a specific browser
		return array_key_exists($key, $this->browsers) AND $this->browser === $this->browsers[$key];
	}

	// --------------------------------------------------------------------

	/**
	 * Is Robot
	 *
	 * @access	public
	 * @return	bool
	 */
	public function is_robot($key = NULL)
	{
	
	//	
	
		if ( ! $this->is_robot)
		{
			return $this->is_bot();
		}

		// No need to be specific, it's a robot
		if ($key === NULL)
		{
			return TRUE;
		}

		// Check for a specific robot
		$isBot = array_key_exists($key, $this->robots) AND $this->robot === $this->robots[$key];
		
		return $isBot ? $isBot : $this->is_bot();
		
	}
	
	
public function is_bot(){
 
	$bot_list= array("Ask Jeeves","Baiduspider","Butterfly","FAST","Feedfetcher-Google","Firefly","Gigabot","Googlebot","InfoSeek","Me.dium","Mediapartners-Google","NationalDirectory","Rankivabot","Scooter","Slurp","Sogou web spider","Spade","TECNOSEEK","TechnoratiSnoop","Teoma","TweetmemeBot","Twiceler","Twitturls","URL_Spider_SQL","WebAlta Crawler","WebBug","WebFindBot","ZyBorg","alexa","appie","crawler","froogle","girafabot","inktomi","looksmart","msnbot","rabaz","www.galaxy.com");
	$user_agent= $_SERVER["HTTP_USER_AGENT"];
 
 
	foreach($bot_list as $bot){
 
		if(strpos($user_agent,$bot)!== false){
 
			return true;
 
		}
 
 
		return false;
 
	}
}

	// --------------------------------------------------------------------

	/**
	 * Is Mobile
	 *
	 * @access	public
	 * @return	bool
	 */
	public function is_mobile($key = NULL)
	{
		if ( ! $this->is_mobile)
		{
			return FALSE;
		}

		// No need to be specific, it's a mobile
		if ($key === NULL)
		{
			return TRUE;
		}

		// Check for a specific robot
		return array_key_exists($key, $this->mobiles) AND $this->mobile === $this->mobiles[$key];
	}

	// --------------------------------------------------------------------

	/**
	 * Is this a referral from another site?
	 *
	 * @access	public
	 * @return	bool
	 */
	public function is_referral()
	{
		if ( ! isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '')
		{
			return FALSE;
		}
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Agent String
	 *
	 * @access	public
	 * @return	string
	 */
	public function agent_string()
	{
		return $this->agent;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Platform
	 *
	 * @access	public
	 * @return	string
	 */
	public function platform()
	{
		return $this->platform;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Browser Name
	 *
	 * @access	public
	 * @return	string
	 */
	public function browser()
	{
		return $this->browser;
	}

	// --------------------------------------------------------------------

	/**
	 * Get the Browser Version
	 *
	 * @access	public
	 * @return	string
	 */
	public function version()
	{
		return $this->version;
	}

	// --------------------------------------------------------------------

	/**
	 * Get The Robot Name
	 *
	 * @access	public
	 * @return	string
	 */
	public function robot()
	{
		return $this->robot;
	}
	// --------------------------------------------------------------------

	/**
	 * Get the Mobile Device
	 *
	 * @access	public
	 * @return	string
	 */
	public function mobile()
	{
		return $this->mobile;
	}

	// --------------------------------------------------------------------

	/**
	 * Get the referrer
	 *
	 * @access	public
	 * @return	bool
	 */
	public function referrer()
	{
		return ( ! isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '') ? '' : trim($_SERVER['HTTP_REFERER']);
	}

	// --------------------------------------------------------------------

	/**
	 * Get the accepted languages
	 *
	 * @access	public
	 * @return	array
	 */
	public function languages()
	{
		if (count($this->languages) == 0)
		{
			$this->_set_languages();
		}

		return $this->languages;
	}

	// --------------------------------------------------------------------

	/**
	 * Get the accepted Character Sets
	 *
	 * @access	public
	 * @return	array
	 */
	public function charsets()
	{
		if (count($this->charsets) == 0)
		{
			$this->_set_charsets();
		}

		return $this->charsets;
	}

	// --------------------------------------------------------------------

	/**
	 * Test for a particular language
	 *
	 * @access	public
	 * @return	bool
	 */
	public function accept_lang($lang = 'en')
	{
		return (in_array(strtolower($lang), $this->languages(), TRUE));
	}

	// --------------------------------------------------------------------

	/**
	 * Test for a particular character set
	 *
	 * @access	public
	 * @return	bool
	 */
	public function accept_charset($charset = 'utf-8')
	{
		return (in_array(strtolower($charset), $this->charsets(), TRUE));
	}
	
	
	
	
	
	##########	
	
	
	public static function singleton(){
    	if (!isset(self::$instance)) {
     		$c = __CLASS__;
       		self::$instance = new $c;
    	}
       	return self::$instance;
   	}
   	
	public function getIp(){
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
//		return '178.84.139.101';
//		return '77.250.12.69';
//return '71.37.103.240';
	}
	
	public function getUserAgent(){
		return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : false;	
	}
	
	public function getIpNumber(){
		$ip = $this->getIp();
		$ips = explode(".", $ip); 
        return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256); 
	}
	 
	
	public function getLocation(){	
		if(isset(self::$a_Client['country_code']) && isset(self::$a_Client['city']))
			return array('country_code' => self::$a_Client['country_code'], 'city' => self::$a_Client['city'], 'country' => self::$a_Client['country']);
	
		try {
			$ipnr = $this->getIp();
			$obj_DB = Loader::load('Database');
$cfg['db']['geoip']['dsn']['type'] = 'PDO_MYSQL';
$cfg['db']['geoip']['dsn']['params']['VPSMEDOT'] =
		array(
 	   		'username' => 'geoip',
 	   		'password' => 'grasduin',
 	   		'host' => 'localhost',
		);
$cfg['db']['geoip']['dsn']['type'] = 'PDO_MYSQL';
$cfg['db']['geoip']['dsn']['params']['MACBOOK'] =
		array(
 	   		'username' => 'root',
 	   		'password' => 'Kasj28()',
 	   		'host' => 'localhost',
		);

			
 			$DB = $obj_DB->connect($cfg['db']['geoip'],'geoip_lite'); 		
								
   			$select = $DB->select();						
			$select->from(array('gbl' => $this->cfg['db']['tables']['blocks']), array('country_code' => 'glc.glc_country_code','city' => 'glc.glc_city', 'country' => 'glc.glc_country'));
			$select->join(array('glc' => $this->cfg['db']['tables']['locations']), 'glc.glc_id = gbl.gbl_glc_id');
			$select->where("gbl.gbl_block_start <= INET_ATON(?)", $ipnr);
			$select->order('gbl.gbl_block_start DESC');
			$select->limit(1);
			
			$a_Res = $DB->query($select)->fetchAll();
			self::$a_Client['country_code'] = isset($a_Res[0]['country_code']) ? $a_Res[0]['country_code'] : false;
			self::$a_Client['country'] = isset($a_Res[0]['country']) ? $a_Res[0]['country'] : false;
			self::$a_Client['city'] = isset($a_Res[0]['city']) ? $a_Res[0]['city'] : false;
			return array('country_code' => self::$a_Client['country_code'], 'city' => self::$a_Client['city'], 'country' => self::$a_Client['country']);
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}       
	} 	
}