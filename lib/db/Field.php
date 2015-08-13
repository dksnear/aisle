<?php
namespace aisle\db;

class Field{
	
	public static function Build($name){
		
		return new self($name);
	}
	
	public static function LastUpdateTime(){
		
		return new self('last_update_time',null,date('Y-m-d H:i:s',time()));
	}
	
	protected $statement;
	
	protected $name;
	
	protected $value;
	
	protected $alias;
	
	protected $tableName;
	
	protected $bind;
	
	protected $bindName;
	
	protected $expr;
	
	public function __construct($name,$alias=null,$value=null,$bind=false,$bindName=null,$aggregate=null,$tableName=null){
		
		$this->name = $name;
		$this->alias = $alias;
		$this->tableName = $tableName;
		$this->bind = $bind;
		$this->bindName = ($bind && !$bindName) ? $name : $bindName;
		$this->value = $value;
		$this->Aggregate($aggregate);
		
	}
	
	public function __call($name,$args){
	
		$prop = lcfirst($name);
	
		if(property_exists($this,$prop))
			$this->$prop = $args[0];
		
		return $this;
	}
	
	public function Bind($bind){
		
		if($bind){
			
			$this->bind = true;
			$this->bindName = is_bool($bind) ? $this->name : $bind;
			
			return $this;
		}
		
		$this->bind = false;
		
		return $this;
	}
	
	public function BindName($bindName){
		
		$this->bind = true;
		$this->bindName = $bindName;
		
		return $this;
	}
	
	public function Aggregate($aggregate){
		
		$this->statement = new Statement('field',$aggregate,null,1);
		
		return $this;
	}
	
	public function Export(){
		
		return array(
		
			'name'=>$this->name,
			'alias'=>$this->alias,
			'value'=>$this->value,
			'bind'=>$this->bind,
			'tableName'=>$this->tableName,
			'bindName'=>$this->bindName,
			'statement'=>$this->statement
		);
		
	}
	
}