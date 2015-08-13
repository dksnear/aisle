<?php
namespace aisle\db;
use aisle\ex\ConfigException;

class DbClientManager{
	
	protected $confm;
	
	protected $default;

	// @confm ConfigManager
	public function __construct($confm){
		
		$this->confm = $confm;
		$this->default = $this->confm->Config()->GetDb();
	
	}
	
	public function Client($name){
		
		return $this->confm->Config()->GetDbClient($name);
	}
	
	public function Connect($dsn,$reset=false){
		
		return $this->getDef()->Connect($dsn,$reset);
	}
	
	public function Close(){
		
		return $this->getDef()->Close();
	}
	
	public function Run($mode=null){
		
		return $this->getDef()->Run($mode);
	}
	
	public function Query($tableName='',$params=null,$allows=null){
		
		return $this->getDef()->Query($tableName,$params,$allows);
	}
		
	public function Trans($tableName='',$params=null,$allows=null){
		
		return $this->getDef()->Trans($tableName,$params,$allows);
	}
	
	public function Compile($trace = false){
		
		return $this->getDef()->Compile($trace);
	}
	
	public function DirectQuery($sqlStatement,$params=null,$allows=null){
		
		return $this->getDef()->DirectQuery($sqlStatement,$params,$allows);
	}
	
	public function DirectTrans($sqlStatement,$params=null,$allows=null){
		
		return $this->getDef()->DirectTrans($sqlStatement,$params,$allows);
	}
	
	public function Clean(){
		
		return $this->getDef()->Clean();
	}
	
	protected function getDef(){
		
		if(!$this->default)
			throw new ConfigException('default database can not find in config!');
		
		return $this->default;
	}
}