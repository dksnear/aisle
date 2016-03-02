<?php
namespace aisle\cache;
use \aisle\ex\CacheException;

class MemCache implements ICache{
	
	protected $client;
	
	protected $host = '127.0.0.1';
	
	protected $port = 11211;
	
	protected $expire = 0;
		
	public function __destruct(){
		
		if(!class_exists('\MemCache',false))
			return;
		
		$this->client->close();		
	}
	
	public function Connect($options = array()){
		
		foreach($options as $key=>$value){
			
			if(property_exists($this,$key))
				$this->$key = $value;
		}
		
		if(!class_exists('\MemCache',false))
			throw new CacheException('MemCache class can not used, please check out php config!');
	
		$this->client = new \MemCache();
		
		if(!$this->client->connect($this->host,$this->port))
			throw new CacheException(sprintf('memcache host "%s:%s" can not connected!',$this->host,$this->port));
		
		return true;
		
	}
	
	public function Get($key){
	
		return $this->client->get($key);		
	
	}
	
	public function Set($key,$value){
	
		return $this->client->set($key,$value,false,$this->expire);		
	}
	
	public function Remove($key){
	
		return $this->client->delete($key);
	
	}
			
	public function Clear(){
	
		return $this->client->flush();
	
	}
	
}

