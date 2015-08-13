<?php
namespace aisle\log;
use aisle\ex\ConfigException;

class LogManager{

	protected $confm;
	
	protected $logs;
	
	// @confm ConfigManager
	public function __construct($confm){
		
		$this->confm = $confm;
		$this->logs = $this->confm->Config()->GetLogs();
	
	}
		
	public function Client($name){
		
		return $this->confm->Config()->GetLogClient($name);
	}
	
	public function Write($statements){
		
		if(!$this->logs)
			throw new ConfigException('default logger can not find in config!');

		foreach($this->logs as $log)
			$log->Write($statements);		
	}
	
	
}