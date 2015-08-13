<?php
namespace aisle\conf;
use aisle\ex\ClassVerifyException;

class CacheConfig{
	
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
	
	public function Inst($name,$classmap,$db){
		
		$driver = $this->Get($name,'driver');

		$options = $driver ? $this->Get($name) : null;
		
		if(!$driver || !$options)
			return null;
		
		$class = $classmap->Get('cache-driver',$driver);
		
		$ICache = 'aisle\\cache\\ICache';
		
		if(!class_exists($class) || !is_subclass_of($class,$ICache))
			throw new ClassVerifyException($class,$ICache);
		
		$options['client'] = $db->Inst($this->Get($name,'db-client'),$classmap);
			
		$inst = new $class();
		$inst->Connect($options);
		
		return $inst;
		
	}
}

