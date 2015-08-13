<?php
namespace aisle\db;

class Table{
	
	protected $name;
	
	protected $alias;
	
	protected $conflict=true;
			
	protected $statements;
	
	protected $client;
	
	protected $previous;
	
	public function __construct($name,$previous=null,$client=null){
		
		$this->client = $client;
		$this->name = $name;
		$this->statements = array();
		$this->previous = $previous;
	}
	
	public function Select($fields){
		
		return $this->addStatement('select',array($this->buildFields(func_get_args())));
	}
	
	public function Selects($fields){
		
		return $this->addStatement('select',array($this->buildFields($fields)));
	}
	
	public function Insert($fields){
	
		return $this->addStatement('insert',array($this->buildFields(func_get_args()),null));
	}
	
	public function Inserts($fields){
	
		return $this->addStatement('insert',array($this->buildFields($fields),null));
	}
	
	public function InsertFrom($tableName,$fields){
		
		$args = func_get_args();
		
		$tableName = array_shift($args);
		$table = new self($tableName,$this,$this->client);
		$this->addStatement('insert',array($this->buildFields($args),$table));
		
		return $table;
	}
	
	public function InsertsFrom($tableName,$fields){
		
		$table = new self($tableName,$this,$this->client);
		$this->addStatement('insert',array($this->buildFields($fields),$table));
		
		return $table;
	}
	
	public function Update($fields){
		
		return $this->addStatement('update',array($this->buildFields(func_get_args())));
	}
	
	public function Updates($fields){
		
		return $this->addStatement('update',array($this->buildFields($fields)));
	}
	
	public function Delete(){
		
		return $this->addStatement('delete');
	}
	
	public function Where(){
				
		return $this->buildCond('where');
	}
		
	public function Limit($start,$range){
		
		return $this->addStatement('limit',array($start,$range));
	}
	
	public function Desc($fields){
		
		return $this->addStatement('desc',array($this->buildFields(func_get_args())));
	}
	
	public function Asc($fields){
		
		return $this->addStatement('asc',array($this->buildFields(func_get_args())));		
	}
	
	public function GroupBy($fields){
		
		return $this->addStatement('groupby',array($this->buildFields(func_get_args())));
	}
	
	public function Having(){
		
		return $this->buildCond('having');
	}
		
	public function Join($tableName,$alias=null,$ignoreOp=false){
		
		return $this->buildTable('join',$tableName,$alias,$ignoreOp);
	}
	
	public function LeftJoin($tableName,$alias=null,$ignoreOp=false){
		
		return $this->buildTable('leftjoin',$tableName,$alias,$ignoreOp);
	}
	
	public function RightJoin($tableName,$alias=null,$ignoreOp=false){
		
		return $this->buildTable('rightjoin',$tableName,$alias,$ignoreOp);
	}
	
	public function On(){
		
		return $this->buildCond('on');
	}
	
	public function Union($tableName,$ignoreOp=false){
		
		return $this->buildTable('leftjoin',$tableName,null,$ignoreOp);
	}
	
	public function UnionAll($tableName,$ignoreOp=false){
		
		return $this->buildTable('leftjoin',$tableName,null,$ignoreOp);
	}
	
	public function Client(){
		
		return $this->client;
	}
	
	public function Compile($trace=false){
		
		return $this->client->Compile($trace);
	}
	
	public function GetName(){
		
		return $this->alias ? $this->alias : $this->name;
	}
	
	public function Alias($alias){
		
		$this->alias = $alias;
		
		return $this;
	}
	
	public function NoConflict(){
		
		$this->conflict = false;
		
		return $this;
	}
	
	public function Previous(){
		
		return $this->previous;	
	}
	
	public function P(){
		
		return $this->Previous();
	}
	
	public function Export(){
		
		return array(
		
			'name'=>$this->name,
			'alias'=>$this->alias,
			'statements'=>$this->statements,
			'conflict'=>$this->conflict
		);
	}
	
	protected function buildFields($fields){
	
		if(!is_array($fields))
			return $fields;
	
		$insts = array();
			
		foreach($fields as $field)
			$insts []= !is_array($field) ? $field : call_user_func_array(array($this,'buildField'),$field);
		
		return $insts;
		
	}
	
	protected function buildField($name,$alias=null,$value=null,$bind=false,$bindName=null,$aggregate=null,$tableName=null){
		
		return new Field($name,$alias,$value,$bind,$bindName,$aggregate,$tableName ? $tableName : $this->GetName());
	}
	
	protected function buildCond($op){
		
		$cond = new Cond($this);
		$this->addStatement($op,array($cond));	
		return $cond;
	}
	
	protected function buildTable($type,$tableName,$alias=null,$ignoreOp=false){
		
		$table = new self($tableName,$this,$this->client);
		$this->addStatement($type,array($table,$alias,$ignoreOp));
		return $table;	
	}
	
	protected function addStatement($op,$ods=null){
		
		$this->statements[]=new Statement('table',$op,$ods);
		
		return $this;
	}
	
}