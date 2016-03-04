<?php
namespace aisle\ex;
use aisle\core\Trace;

class ExceptionManager{
	
	protected $logm;
	
	protected $viewm;
	
	protected $trace = false;
	
	public function __construct($logm,$viewm){
		
		$this->logm = $logm;
		$this->viewm = $viewm;
		$this->Regist();
	}
	
	public function SetTrace($trace){
		
		$this->trace = $trace;
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
		
		if($this->trace)
			Trace::Eject();
		
		die();
	}
	
	public function ErrResolver($level,$msg,$file,$line,$context){
		
		throw new ErrorException($msg,$level,$file,$line);
	}

}
