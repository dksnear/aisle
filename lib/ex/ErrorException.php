<?php
namespace aisle\ex;

use aisle\view\Message;
use aisle\web\Request;

class ErrorException extends \ErrorException implements IException{
	
	protected static $ERR_MAP = array(
		
		E_WARNING=>'E_WARNING',
		E_NOTICE=>'E_NOTICE',
		E_USER_ERROR=>'E_USER_ERROR',
		E_USER_WARNING=>'E_USER_WARNING',
		E_USER_NOTICE=>'E_USER_NOTICE',
		E_RECOVERABLE_ERROR=>'E_RECOVERABLE_ERROR',
		E_ALL=>'E_ALL',
		E_STRICT=>'E_STRICT'
	);
	
	protected $exType = 'E_UNKNOW';
	
	protected $exMsg = 'Project Inner Error Occurred!';
	
	// @code errLevel
	public function __construct($msg,$code,$errFile,$errLine){
		
		parent::__construct($msg,0,$code,$errFile,$errLine);
				
		$this->exType = isset(self::$ERR_MAP[$code]) ? self::$ERR_MAP[$code] : $this->exType;
	
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
			
		$viewm->Render(new Message($this->exMsg,false,$this->exType,$this->getSeverity()));
	}
	
	protected function getStatements(){
		
		$request = new Request();
		
		return array(
		
			'type' => $this->exType,
			'msg' => $this->getMessage(),
			'code' => $this->getSeverity(),
			'file' => $this->getFile(),
			'line' => $this->getLine(),
			'client_ip' => $request->ClientIp(),
			'request' =>  json_encode($request->Form()),
			'trace' => $this->getTraceAsString()		
		);
	}
	
}
