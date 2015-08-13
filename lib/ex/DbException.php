<?php
namespace aisle\ex;

class DbException extends Exception{
	
	protected $exSql;
	
	protected $exSqlParams;
	
	protected $exType = 'DbException';
	
	protected $exMsg = 'Project Database Exception Occurred!';
	
	public function __construct($msg,$code=1,$exSql='',$exSqlParams=''){
	
		parent::__construct($msg,$code);
		
		$this->exSql = $exSql;
		$this->exSqlParams = $exSqlParams;
		
	}
	
	protected function getStatements(){
		
		return array_merge(parent::getStatements() ,array(
		
			'sql_statement' =>  $this->exSql,
			'sql_params' =>  $this->exSqlParams		
		));
		
	}
}
