<?php
namespace aisle\conf;
use aisle\ex\ClassVerifyException;

class DbConfig{
	
	protected $clients;
	
	public function __construct($statements){
		
		$this->clients = $statements;
		
	}
	
	public function Get($name,$prop=null){
		
		$name = isset($this->clients[$name]) ? $this->clients[$name] : null;
		
		if(empty($name)) return null;
		
		if(empty($prop)) return $name;
		
		return isset($name[$prop]) ? $name[$prop] : null;
		
	}
	
	public function Inst($name,$classmap){
		
		$driver = $this->Get($name,'driver');
		$dsn = $driver ? $this->Get($name,'dsn') : null;
		
		if(!$driver)
			return null;
		
		$class = $classmap->Get('db-driver',$driver);
		
		$IDbClient = 'aisle\\db\\IDbClient';
		
		if(!class_exists($class) || !is_subclass_of($class,$IDbClient))
			throw new ClassVerifyException($class,$IDbClient);
			
		$inst = new $class();
		$inst->Connect($dsn);
		
		return $inst;
		
	}
}

