<?php
namespace aisle\cache;
use \aisle\core\XType;
use \aisle\ex\CacheException;

class RedisCache implements ICache{
		
	protected $client = null;
	
	protected $host = '127.0.0.1';
	
	protected $port = 6379;
	
	protected $expire = 0;
	
	public function Connect($options = array()){
		
		foreach($options as $key=>$value){
			
			if(property_exists($this,$key))
				$this->$key = $value;
		}
		
		if(!class_exists('\Redis',false))
			throw new CacheException('Redis class can not used, please check out php config!');
	
		$this->client = new \Redis();
		
		if(!$this->client->connect($this->host,$this->port))
			throw new CacheException(sprintf('redis host "%s:%s" can not connected!',$this->host,$this->port));
		
		return true;
		
	}
	
	public function Get($key){
		
		$value = $this->client->get($key);	

		if($value === false)
			return $value;
		
		return XType::Build($value)->Unescape()->Unserialize()->Meta();
			
	}
	
	public function Set($key,$value){
		
		$value = XType::Build($value)->Serialize()->Escape()->Meta();
		
		if($this->expire > 0)
			return $this->client->setex($key,$this->expire,$value);
		
		return $this->client->set($key,$value);		
	}
	
	public function Remove($key){
	
		return $this->client->delete($key);
	
	}
			
	public function Clear(){
	
		//return $this->client->flushAll();
		
		return $this->client->flushDB();
	
	}	
}
