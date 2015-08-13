<?php
namespace aisle\ex;

use aisle\view\Message;
use aisle\web\Request;

class Exception extends \Exception implements IException{
	
	protected $exType = 'Exception';
	
	protected $exMsg = 'Project Inner Exception Occurred!';
	
	public function __construct($msg,$code=1){
			
		$msg instanceof \Exception ? 
			parent::__construct($msg->getMessage(),$code,$msg) :
			parent::__construct($msg,$code);
		
	}
	
	public function __get($name){
		
		return property_exists($this,$name) ? $this->$name : null;
	}
	
	// @logm LogManager
	public function Write($logm){
				
		$logm->Write($this->getStatements());
	}
	
	// @viewm ViewManager
	public function Render($viewm){
		
		$viewm->Render(new Message($this->exMsg,false,$this->exType,$this->code));
	}
	
	protected function getStatements(){
		
		$request = new Request();
		
		return array(
		
			'type' => $this->exType,
			'msg' => $this->getMessage(),
			'code' => $this->getCode(),
			'file' => $this->getPrev()->getFile(),
			'line' => $this->getPrev()->getLine(),
			'client_ip' => $request->ClientIp(),
			'request' =>  json_encode($request->Form()),
			'trace' => $this->getPrev()->getTraceAsString()		
		);
	}
	
	protected function getPrev(){
		
		$prev = $this->getPrevious();
		
		return $prev ? $prev : $this;
	}
	
	
}
