<?php
namespace aisle\project\detector;
use aisle\core\File;
use aisle\web\Controller;
use aisle\ex\RoutingFailedException;

class DetectController extends Controller{
		
	public function __call($name,$args){
	
		$path = sprintf('%s/$doc/sql/%s.sql', __DIR__,lcfirst($name));
	
		if(!file_exists($path))
			throw new RoutingFailedException();
	
		$sqls = File::Read($path);
		$isQuery = preg_match('/\#query\#/is',$sqls);
		$sqls = preg_replace(array('/--.+|USE.+/','/(\\r\\n)*|(\\n)*/'),'',$sqls);
		
		$sqls = explode(';',$sqls);	
		$result = array();
		
		foreach($sqls as $sql){
			
			if(empty($sql))
				continue;
			
			$result [] = $isQuery? $this->dbm->DirectQuery($sql)->Run()->Meta() :
				$this->dbm->DirectTrans($sql)->Run()->Get(0)->Meta();
			
		}
		
		return $result;
	}
	
	public function Welcome(){
		
		return 'Welcome to Aisle Detector !';
	}
	
	public function Db(){
		
		return array($this->create_table(),$this->insert(),$this->select());
	}
	
}