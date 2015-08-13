<?php
namespace aisle\core;

class Cmd{
	
	public static function Run($name){
		
		exec(dirname(__DIR__).'/$source/cmd/'.$name.'.bat');	
	}
	
	public static function Alert(){
		
		self::Run('alert');
	}
	
}
