<?php
namespace aisle\core;

use aisle\ex\CoreException;

class Shell{
	
	// public static $PHP = 'g:/win8/Documents/php/php5.4.13/php';
	public static $PHP = 'G:/win8/Documents/php/php5.6.18/php';
	// public static $PHP = 'G:/win8/Documents/php/php7.0.3/php';
		
	public static function Run($name,$args='',$reveal=false){
		
		$ext = preg_match('/win/i',PHP_OS) ? '.cmd' : '';
		
		$path = dirname(__DIR__).'/$source/shell/'.$name.$ext;
		
		if(!file_exists($path))
			throw new CoreException(sprintf('shell path "%s" is not exists!',$path));
		
		$ret = array();
		
		if(!$reveal)
			return exec($path.' '.$args);
		
		return array(exec($path.' '.$args,$ret),$ret,implode("\n",$ret));
			
	}
	
	public static function AisleRun($url,$reveal=false){
		
		$url = preg_replace('/(^http:\/\/|^https:\/\/|^ftp:\/\/).*?\/|^\//i','',$url);
		$url = explode('?',$url);	
		$path = empty($url[0]) ? array() : explode('/',$url[0]);
		$args = isset($url[1]) ? $url[1] : '';
		$count = empty($path) ? 0 : count($path);
		
		if(!empty($args))
			$args = preg_replace('/&|%/','^$0','&'.$args);		
		
		if($count <= 0 || $count > 4)
		    $format = '"demo" "" "Core" "Welcome" "%s" "%s"';
		
		if($count == 1)
			$format ='"demo" "" "Core" "%s" "%s" "%s"';
		
		if($count == 2)
			$format ='"demo" "" "%s" "%s" "%s" "%s"';
		
		if($count == 3)
			$format ='"%s" "" "%s" "%s" "%s" "%s"';
		
		if($count == 4)
			$format ='"%s" "%s" "%s" "%s" "%s" "%s"';
		
		array_unshift($path,$format);
		array_push($path,$args,self::$PHP);
		
		$path = call_user_func_array('sprintf',$path);
				
		if(!$reveal)
			return self::Run('aisle_run',$path,$reveal);
	
		$r = self::Run('aisle_run',$path,$reveal);
		
		return $r[2];
		
	}
	
}
