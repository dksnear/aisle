<?php
namespace aisle\conf;

use aisle\core\XArray;
use aisle\core\File;
use aisle\ex\ConfigException;

class ConfigManager{
	
	protected $config;
	
	protected $statements = array();
	
	public function __construct($paths){
				
		foreach($paths as $path){
			
			$path = is_array($path)? $path : array($path);
			$cover = isset($path[1]) ? $path[1] : false;
			
			$this->load($path[0],$cover);
		};
			
		$this->config = new AppConfig($this->statements);
	}
	
	public function __call($name,$args){
		
		$name = lcfirst($name);
		
		return property_exists($this,$name) ? $this->$name : null; 
				
	}
		
	protected function load($path,$cover=false){
		
		$statements = File::Read($path);
		
		$statements = json_decode(strip_tags($statements),true);
		
		if(empty($statements) || !is_array($statements))
			throw new ConfigException(sprintf('config file "%s" can not read or have some errors in it!',$path));
		
		if(empty($this->statements))
			return $this->statements = $statements;
		
		if($cover)
			return $this->statements = array_merge($this->statements,$statements);
		
		return $this->statements = XArray::Build($this->statements)->DeepMerge($statements)->Meta();
			
	}
}

