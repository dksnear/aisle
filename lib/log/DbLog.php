<?php
namespace aisle\log;
use aisle\db\Field;

class DbLog implements ILog{
	
	protected $dbClient;
	
	protected $table = 'aisle_sys_ex_log';
	
	public function Connect($options = array()){
		
		foreach($options as $key=>$value){
			
			if(property_exists($this,$key))
				$this->$key = $value;
		}
		
		return $this;
	}

	public function Write($statements){
		
		return $this->dbClient->Trans($this->table,$statements)->Inserts(array_map(function($key){
			
			return Field::Build($key)->Bind(true);
			
		},array_keys($statements)))->Compile()->Run();
	}
	
} 