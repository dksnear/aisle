<?php
namespace aisle\_accurate\services\data{

	use \aisle\common as common;
	use \aisle\_accurate\sqlClient\MysqlClient as sqlClient;
	use \aisle\_accurate\Exception as Exception;
	use \aisle\_accurate\Exception\VerifyException as VerifyException;

	class Log extends \aisle\_accurate\Service{
			
		protected $db_require = true;
		
		
		public function test(){
			
			//trigger_error('xxx1');
			
			throw new \Exception('eee1');
			
			//throw new \aisle\Exception('xxx4');
			
			//return common\_config_get_log();
			
			//return common\_var_dump($GLOBALS);
			
			//return common\_client_ip();
			
			//$v= array();
			
			//return !$v;
			
			//return json_decode($this->db_client->first_field('select php_trace from aisle_sys_log where sys_id = 3'));
			
			//return \aisle\File::write('./logs/log',"dfdf \r\n",'a');
		
		}
		
		public function log(){
			
			return $this->db_client->query('select now()');
		}
		
	}
	
}