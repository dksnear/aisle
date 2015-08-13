<?php
namespace aisle\ex;
use aisle\view\Message;

class ClientException extends Exception{
	
	protected $exType = 'ClientException';
		
	// @logm LogManager
	public function Write($logm){
				
		// close log
		return false;
	}
	
	// @viewm ViewManager
	public function Render($viewm){
				
		$viewm->Render(new Message($this->getMessage(),false,$this->exType,$this->code));
	}
	
}
