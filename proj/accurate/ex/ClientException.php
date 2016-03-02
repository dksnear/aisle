<?php
namespace aisle\proj\accurate\ex;
use aisle\proj\accurate\view\Message;

class ClientException extends ClientException{
	
	protected $exType = 'AccurateClientException';
		
	// @viewm ViewManager
	public function Render($viewm){
				
		$viewm->Render(new Message($this->getMessage(),false,$this->exType,$this->code));
	}
	
}
