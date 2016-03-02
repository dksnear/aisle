<?php
namespace aisle\_accurate\services\data{

	use \aisle\common as common;
	use \aisle\_accurate\sqlClient\MysqlClient as sqlClient;
	use \aisle\_accurate\Exception as Exception;
	use \aisle\_accurate\Exception\VerifyException as VerifyException;

	class Forum extends \aisle\_accurate\Service{
			
		protected $db_require = true;
	}
	
}