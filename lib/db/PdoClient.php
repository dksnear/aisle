<?php
namespace aisle\db;
use aisle\core\XType;
use aisle\ex\Exception;
use aisle\ex\DbException;

abstract class PdoClient implements IDbClient{
	
	protected $queryTables;
	
	protected $queryStatements;
	
	protected $queryParams;
	
	protected $queryAllows;
	
	// query|trans
	protected $queryMode;
		
	protected $dsn = array();
	
	protected $pdo;
	
	protected $connected = false;
	
	public function __get($name){
		
		return property_exists($this,$name) ? $this->$name : null;
	}
		
	//# impletment from IClient;
	
	public function Connect($dsn,$reset=false){
	
		$this->connected = true;
		return $this;
			
	}
	
	public function Close(){
		
		$this->connected = false;
		$this->pdo = null;
		
		return $this;
	}
	
	//@mode raw|rowcount|assoc|num|both
	public function Run($mode=null){
		
		$result = null;
		
		if($this->queryMode == 'query'){
			
			$result = $this->execute($this->queryStatements[0],$this->queryParams[0],$this->queryAllows[0],$mode ? $mode : 'assoc');
		}
		
		if($this->queryMode == 'trans'){
			
			$result = $this->transaction($this->queryStatements,$this->queryParams,$this->queryAllows,$mode ? $mode : 'rowcount');
		}
		
		$this->Clean();
		
		return XType::Build($result);
	}
	
	public function Query($tableName='',$params=null,$allows=null){
		
		$table = new Table($tableName,null,$this);
		
		$this->queryTables = array($table);
		$this->queryParams = array($params);
		$this->queryAllows = array($allows);
		
		$this->queryMode = 'query';
		
		return $table;
	}
		
	public function Trans($tableName='',$params=null,$allows=null){
							
		if($this->queryMode != 'trans'){
			
			$this->queryTables = array();
			$this->queryParams = array();
			$this->queryAllows = array();
		}
		
		$table = new Table($tableName,null,$this);
		$this->queryTables []= $table;
		$this->queryParams []= $params;
		$this->queryAllows []= $allows;
		
		$this->queryMode = 'trans';
				
		return $table;
	}
	
	public function Compile($trace=false){
				
		return $this;
	}
	
	public function DirectQuery($sqlStatement,$params=null,$allows=null){
		
		$this->queryStatements = array($sqlStatement);
		$this->queryParams = array($params);
		$this->queryAllows = array($allows);
		
		$this->queryMode = 'query';
		
		return $this;
	}
	
	public function DirectTrans($sqlStatement,$params=null,$allows=null){
		
		if($this->queryMode != 'trans'){
			
			$this->queryStatements = array();
			$this->queryParams = array();
			$this->queryAllows = array();
		}
		
		$this->queryStatements[] = $sqlStatement;
		$this->queryParams []= $params;
		$this->queryAllows []= $allows;
		
		$this->queryMode = 'trans';
		
		return $this;
	}
	
	public function Clean(){
		
		$this->queryTables = null;
		$this->queryStatements = null;
		$this->queryParams = null;
		$this->queryAllows = null;
		$this->queryMode = null;
		
		return $this;
	}
	
	//@mode raw|rowcount|assoc|num|both
	protected function result($query,$mode){
		
		$mode = strtolower($mode);
		
		if($mode == 'rowcount')
			return $query->rowCount();
		if($mode == 'assoc')
			return $query->fetchAll(\PDO::FETCH_ASSOC);
		if($mode == 'num')
			return $query->fetchAll(\PDO::FETCH_NUM);
		if($mode == 'both')
			return $query->fetchAll(\PDO::FETCH_BOTH);
		
		return $query;
		
	}
		
	protected function execute($sql,$params=null,$allows=null,$mode='assoc'){
		
		try{
			
			if(!$this->connected)
				throw new Exception('sql client can not connected!');
		
			$query = $this->pdo->prepare($sql,array(\PDO::ATTR_CURSOR=>\PDO::CURSOR_FWDONLY));
			
			if(!empty($params)){
			
				$params = !empty($allows) ? array_intersect_key($params,$allows) : $params;
			
				foreach($params as $key=>$value){
				
					$type = false;
					
					if(is_int($value))
						$type = \PDO::PARAM_INT;					
					if(is_bool($value))
						$type = \PDO::PARAM_BOOL;
					if(is_string($value))
						$type = \PDO::PARAM_STR;
					if(is_null($value))
						$type = \PDO::PARAM_NULL;
					
					$query->bindValue(':'.$key,$value,$type);
						
				}
			
			}
			
			$query->execute();
			
			return $this->result($query,$mode);
		
		}catch(\PDOException $ex){
			
			throw new DbException($ex,1,$sql,json_encode($params));
		}
		
	}
	
	protected function transaction($sqlSet,$paramsSet,$allowsSet,$mode='rowcount'){
		
		try{
		
			$affects= array();
			
			$this->pdo->beginTransaction();
			
			foreach($sqlSet as $idx => $sql){
			
				$affects []= $this->execute($sql,$paramsSet[$idx],$allowsSet[$idx],$mode);
			}
							
			$this->pdo->commit();
			
			return $affects;
			
		
		}catch(\PDOException $ex){
		
			$this->pdo->rollBack();	
			
			throw new DbException($ex,1,json_encode($sqlSet),json_encode($paramsSet));
		}
	}

}