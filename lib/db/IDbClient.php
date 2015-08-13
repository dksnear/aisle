<?php
namespace aisle\db;

interface IDbClient{
	
	public function Connect($dsn,$reset=false);
	
	public function Close();
	
	public function Run($mode=null);
	
	public function Query($tableName='',$params=null,$allows=null);
		
	public function Trans($tableName='',$params=null,$allows=null);
	
	public function Compile($trace=false);
	
	public function DirectQuery($sqlStatement,$params=null,$allows=null);
	
	public function DirectTrans($sqlStatement,$params=null,$allows=null);
	
	public function Clean();
	
}