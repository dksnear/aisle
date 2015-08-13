<?php
namespace aisle\web;

class Response{
	
	protected static $IS_CUSTOMER_HEADER = false;
	
	public function Header(){
		
		if(self::$IS_CUSTOMER_HEADER)
			return false;
		
		$args = func_get_args();
		
		if(!empty($args))
			foreach($args as $statement)
				header($statement);
	
		self::$IS_CUSTOMER_HEADER = true;
		
		return true;
		
	}
	
	public function Cookie($key,$value,$expire = 86400,$path = '/' ,$domain = null,$secure = 0,$http = true){
		
		return setcookie($key,$value,time() + $expire,$path,$domain,$secure,$http);
		
	}
	
	public function Session($key,$value){
		
		Request::SessionStart();

		$_SESSION[$key] = $value;
		
	}

}
