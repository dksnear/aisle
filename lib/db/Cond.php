<?php
namespace aisle\db;

class Cond{
	
	protected $statements;
	
	protected $table;
	
	protected $previous;
	
	public function __construct($previous,$table=null){
		
		$this->previous = $previous;
		$this->table = $table ? $table : $previous;
		$this->statements = array();
	}
	
	// ignoreLo: ignore logic operator
	public function And_($ignoreLo=false){
		
		$cond = new self($this,$this->table);
		$this->addStatement('and',array('and',$cond,$ignoreLo));
		return $cond;
	}
	
	public function Or_($ignoreLo=false){
		
		$cond = new self($this,$this->table);
		$this->addStatement('or',array('or',$cond,$ignoreLo));
		return $cond;	
	}
	
	// return @Table
	public function Exists($tableName,$lo=null){
		
		return $this->buildTable('exists',$tableName,$lo);
	}
	
	// return @Table
	public function NotExists($tableName,$lo=null){
		
		return $this->buildTable('notexists',$tableName,$lo);
	}
	
	// return @Table
	public function In($tableName,$lo=null){
		
		return $this->buildTable('in',$tableName,$lo);
	}
		
	public function NotIn($tableName,$lo=null){
		
		return $this->buildTable('notin',$tableName,$lo);
	}
	
	public function Ins($field,$values,$lo=null){
		
		return $this->addStatement('ins',array($lo,$this->buildField($field),$values));
	}
	
	public function NotIns($field,$values,$lo=null){
		
		return $this->addStatement('notins',array($lo,$this->buildField($field),$values));
	}
	
	public function Gt($field,$lo=null){
		
		return $this->addStatement('gt',array($lo,$this->buildField($field)));
	}
	
	public function Gte($field,$lo=null){
		
		return $this->addStatement('gte',array($lo,$this->buildField($field)));
	}
	
	public function Lt($field,$lo=null){
		
		return $this->addStatement('lt',array($lo,$this->buildField($field)));
	}
	
	public function Lte($field,$lo=null){
		
		return $this->addStatement('lte',array($lo,$this->buildField($field)));
	}
	
	public function Eq($field,$lo=null){
		
		return $this->addStatement('eq',array($lo,$this->buildField($field)));
	}
	
	public function Ne($field,$lo=null){
		
		return $this->addStatement('ne',array($lo,$this->buildField($field)));
	}
	
	public function Like($field,$lo=null){
		
		return $this->addStatement('like',array($lo,$this->buildField($field)));
	}
	
	public function Null_($field,$lo=null){
		
		return $this->addStatement('null',array($lo,$this->buildField($field)));
	}
	
	public function NotNull($field,$lo=null){
		
		return $this->addStatement('notnull',array($lo,$this->buildField($field)));	
	}
	
	public function Between($field,$rs,$rn,$lo=null){
		
		return $this->addStatement('between',array($lo,$this->buildField($field),$rs,$rn));
	}
	
	public function NotBetween($field,$rs,$rn,$lo=null){
		
		return $this->addStatement('notbetween',array($lo,$this->buildField($field),$rs,$rn));
	}
	
	public function Compile($trace=false){
		
		return $this->table->Compile($trace);
	}
	
	public function Previous(){
		
		return $this->previous;
	}
	
	public function P(){
		
		return $this->Previous();
	}
	
	public function Table(){
		
		return $this->table;
	}
	
	public function T(){
		
		return $this->Table();
	}
	
	public function Export(){
		
		return array(
		
			'statements'=>$this->statements
		);
	}
		
	protected function buildTable($op,$tableName,$lo){
		
		$table = new Table($tableName,$this,$this->table->Client());
		$this->addStatement($op,array($lo,$table));
		return $table;
	}
	
	protected function buildField($field){
		
		return !is_array($field) ? $field : call_user_func_array(function($name,$alias=null,$value=null,$bind=false,$bindName=null,$aggregate=null,$tableName=null){
				
			return new Field($name,$alias,$value,$bind=false,$bindName,$aggregate,$tableName ? $tableName : $this->table->GetName());
			
		},$field);
		
	}
	
	protected function addStatement($op,$ods){
		
		$this->statements[]=new Statement('cond',$op,$ods);
		
		return $this;
	}
	
}