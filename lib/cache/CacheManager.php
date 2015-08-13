<?php
namespace aisle\cache;
use aisle\ex\ConfigException;

class CacheManager{
	
	protected $confm;
	
	protected $default;

	// @confm ConfigManager
	public function __construct($confm){
		
		$this->confm = $confm;
		$this->default = $this->confm->Config()->GetCache();
	
	}
	
	public function Client($name){
		
		return $this->confm->Config()->GetCacheClient($name);
	}
	
	public function Get($key){
		
		return $this->getDef()->Get($key);	
	}

	public function Set($key, $value){
		
		return $this->getDef()->Set($key,$value);
	}
	
	public function Remove($key){
		
		return $this->getDef()->Remove($key);
	}
		
	public function Clear(){
		
		return $this->getDef()->Clear();
	}
	
	protected function getDef(){
		
		if(!$this->default)
			throw new ConfigException('default cache client can not find in config!');
		
		return $this->default;
	}
}