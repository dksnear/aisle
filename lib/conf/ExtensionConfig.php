<?php
namespace aisle\conf;
use aisle\ex\ClassVerifyException;

class ExtensionConfig{
	
	protected $clients;
	
	protected $appConfig;

	public function __construct($statements,$appConfig){
		
		$this->clients = $statements;
		$this->appConfig = $appConfig;
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
		
		if(!$driver)
			return null;
		
		$class = $classmap->Get('extension-config-driver',$driver);
		
		$IExtensionConfig = 'aisle\\conf\\IExtensionConfig';
		
		if(!class_exists($class) || !is_subclass_of($class,$IExtensionConfig))
			throw new ClassVerifyException($class,$IExtensionConfig);
				
		$inst = new $class();
		$inst->Load($options,$this->appConfig);
		
		return $inst;
		
	}
}

