<?php
namespace aisle\ex;

class ClassVerifyException extends ConfigException{
	
	protected $exType = 'ClassVerifyException';
	
	public function __construct($class,$super=null){

		$msg = !$super ? 
			sprintf('class "%s" is not defined!',$class) : 
			sprintf('class "%s" is not defined or class "%s" is not implements from "%s"! ',$class,$class,$super);
	
		parent::__construct($msg,1);

	}
	
	
}