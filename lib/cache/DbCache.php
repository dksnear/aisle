<?php
namespace aisle\cache;

use aisle\core\XType;
use aisle\db\Field;
	
class DbCache implements ICache{
					
	protected $client;
	
	protected $table = 'aisle_sys_cache';
	
	protected $name = 'AISLE_SYS_DB_CACHE';
	
	// expire=0 永远不过期
	protected $expire = 0;
	
	public function Connect($options = array()){
		
		foreach($options as $key=>$value){
			
			if(property_exists($this,$key))
				$this->$key = $value;
		}
		
	}	

	public function Get($key){
		
		if(XType::Empty_($key)) return null;
		
		$query = $this->client
			->Query($this->table,array('name'=>$this->name,'key'=>$key))
			->Select(Field::Build('value'),Field::Build('expire'),Field::Build('last_update_time'))
			->Where()
			->Eq(Field::Build('name')->Bind(true))
			->Eq(Field::Build('key')->Bind(true),'and')
			->Compile()->Run()->Get(0)->Meta();
		
		if(empty($query)) return null;
				
		// expired
		if($query['expire']!=0 && $query['last_update_time']+$query['expire'] < time()){
			
			$this->Remove($key);
			
			return null;
		}
			
		return XType::Build($query['value'])->Unescape()->Unserialize()->Meta();
	
	}		
	
	public function Set($key,$value){
		
		if(XType::Empty_($key)) return false;
		
		$params = array(
		
			'name' => $this->name,
			'key' => $key,
			'value' => XType::Build($value)->Serialize()->Escape()->Meta(),
			'last_update_time' => time(),
			'expire' => $this->expire
		);
		
		$this->Remove($key);
			
		return !!$this->client
			->Trans($this->table,$params)
			->Inserts(array_map(function($key){ return Field::Build($key)->Bind(true); },array_keys($params)))
			->Compile()->Run()->Get(0)->Meta();
					
	}
	
	public function Remove($key){
		
		if(XType::Empty_($key)) return false;
		
		return !!$this->client
			->Trans($this->table,array('name' => $this->name,'key' => $key))
			->Delete()->Where()
			->Eq(Field::Build('name')->Bind(true))
			->Eq(Field::Build('key')->Bind(true),'and')
			->T()->Compile()->Run()->Get(0)->Meta();
	}
			
	public function Clear(){
			
		return !!$this->client
			->Trans($this->table,array('name' => $this->name))
			->Delete()->Compile()->Run()->Get(0)->Meta();

	}	
}
	
