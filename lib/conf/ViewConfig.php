<?php
namespace aisle\conf;
use aisle\ex\ClassVerifyException;

class ViewConfig{
	
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
		$options = $driver ? $this->Get($name) : null;
	
		if(!$driver || !$options) return null;
		
		$class = $classmap->Get('view-driver',$driver);
		
		$IView = 'aisle\\view\\IView';
				
		if(!class_exists($class) || !is_subclass_of($class,$IView))
			throw new ClassVerifyException($class,$IView);
		
		$inst = new $class();
		$inst->Set($options);
		
		return $inst;
		
	}
}

