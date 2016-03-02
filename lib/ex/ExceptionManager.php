<?php
namespace aisle\ex;
use aisle\core\Trace;

class ExceptionManager{
	
	protected $logm;
	
	protected $viewm;
	
	protected $program;
	
	public function __construct($logm,$viewm,$program){
		
		$this->logm = $logm;
		$this->viewm = $viewm;
		$this->program = $program;
		$this->Regist();
	}
	
	public function Regist(){
		
		set_error_handler(array($this,'ErrResolver'));
		
		set_exception_handler(array($this,'ExResolver'));
			
	}
	
	public function ExResolver($ex){
		
		if(!$ex instanceof IException)
			$ex = new Exception($ex,0);
			
		$ex->Write($this->logm);
		$ex->Render($this->viewm);
		
		if($this->program->trace)
			Trace::Eject();
		
		die();
	}
	
	public function ErrResolver($level,$msg,$file,$line,$context){
		
		throw new ErrorException($msg,$level,$file,$line);
	}

}
