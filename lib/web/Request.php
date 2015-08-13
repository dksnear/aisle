<?php
namespace aisle\web;

use aisle\core\XType;

class Request{
	
	protected static $IS_SESSION_START = false;
	
	public static function SessionStart($limit=null,$expire=null){
		
		if(!self::$IS_SESSION_START) { 
		
			!empty($limit) && session_cache_limiter($limit);
			!empty($expire) && session_cache_expire($expire);
			session_start();
		}
		
		self::$IS_SESSION_START = true;
	}
	
	protected $data;
	
	public function __construct($data=null){
		
		$this->data = $data ? $data : $_REQUEST;
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
}
