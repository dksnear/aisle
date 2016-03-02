<?php
namespace aisle\web;
use aisle\core\XType;

class Request{
	
	protected static $IS_SESSION_START = false;
	
	protected static $CONSOLE_PARAMS = null;
	
	public static function SessionStart($limit=null,$expire=null){
		
		if(!self::$IS_SESSION_START) { 
		
			!empty($limit) && session_cache_limiter($limit);
			!empty($expire) && session_cache_expire($expire);
			session_start();
		}
		
		self::$IS_SESSION_START = true;
	}
	
	public static function ParseParam($pstr,$decoder = null){
		
		$pstr = preg_replace('/^\?/','',$pstr);
		$params = array();
		
		preg_replace_callback('/(?:^|&)(.*?)=(.*?)(?=&|$)/',function($match) use(& $params,$decoder) {
			
			$k = $match[1];
			$v = $match[2];
			
			if(is_callable($decoder)){
				
				$k = $decoder($k);
				$v = $decoder($v);
			}
				
			$params[$k] = $v;  
			
		},$pstr);
		
		return $params;
		
	}
	
	public static function IsConsole(){
		
		return isset($_SERVER['SESSIONNAME']) && strtolower($_SERVER['SESSIONNAME']) == 'console';
	}
	
	protected $data;
	
	public function __construct($data=null){
			
		$this->data = $data ? $data : $this->getDefaultData();
	}
	
	public function Param($name = null){
		
		return $this->getValue($this->data,$name);
	}
	
	public function Form($name = null){
		
		return $this->getValue($_REQUEST,$name);
	}

	public function Get($name = null){
		
		return $this->getValue($_GET,$name);
		
	}
	
	public function Post($name = null){
		
		return $this->getValue($_POST,$name);

	}
	
	public function File($name = null){
		
		return $this->getValue($_FILES,$name);
		
	}
	
	public function Server($name = null){
		
		return $this->getValue($_SERVER,$name);
		
	}
	
	public function Cookie($name = null){
		
		return $this->getValue($_COOKIE,$name);
		
	}
	
	public function Session($name = null){
		
		self::SessionStart();

		return $this->getValue($_SESSION,$name);
	}
	
	public function ClientIp(){
		
		$ip =  XType::Or_(
		
			$this->Server('HTTP_X_FORWARDED_FOR'),
			$this->Server('HTTP_CLIENT_IP'),
			$this->Server('REMOTE_ADDR'),
			getenv('HTTP_X_FORWARDED_FOR'),
			getenv('HTTP_CLIENT_IP'),
			getenv('REMOTE_ADDR'),
			'unknown'
		);
	
		if(is_array($ip))
			return implode(',',$ip);
		
		return $ip;
	}
	
	protected function getValue($arr,$name=null){
		
		if(!$name) return $arr;
		
		return isset($arr[$name]) ? $arr[$name] : null;
	}
	
	protected function getDefaultData(){
		
		if(!self::IsConsole())
			return $_REQUEST;
		
		if(!empty(self::$CONSOLE_PARAMS))
			return self::$CONSOLE_PARAMS;
		
		if($GLOBALS['argc'] < 2)
			return array();
		
		self::$CONSOLE_PARAMS = self::ParseParam($GLOBALS['argv'][1],function($str){
			
			return XType::Build($str)->Unescape()->Meta();
		});
						
		return self::$CONSOLE_PARAMS;
		
	}
}
