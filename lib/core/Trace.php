<?php
namespace aisle\core;

class Trace{
	
	protected static $popStack = array();
	
	public static function WriteLine($data,$halt=false){
		
		print_r($data);
		print_r("\n");
		$halt && exit(1024);
	}
	
	public static function Write($data){
		
		print_r($data);
	}
	
	public static function Eject(){
		
		while(count(self::$popStack)){
			
			$trace = array_pop(self::$popStack);
			$trace->End();
		}
	}
	
	private $isRuning = false;
	
	protected $name = 'trace';
	
	protected $startTime = 0;
	
	protected $endTime = 0;
	
	protected $startMem = 0;
	
	protected $endMem = 0;
		
	public function __construct($name=null,$disp=false){
		
		array_push(self::$popStack,$this);
		
		$this->name = $name ? $name :$this->name;
		$this->Start($disp);
	}
	
	public function __get($prop){
		
		if(isset($this->$prop))
			return $prop;
		
		return null;
	}
	
	public function Start($disp=true){
		
		if(!$this->isRuning){
		
			$this->startTime = microtime(true);
			$this->startMem = memory_get_usage();
			$this->endTime = 0;
			$this->endMem = 0;
			
			$this->isRuning = true;
		}
		
		$disp && $this->Disp();
	}
	
	public function End($disp=true){
		
		if($this->isRuning){
			
			$this->endTime = microtime(true);
			$this->endMem = memory_get_usage();
			
			$this->isRuning = false;
		}
		
		$disp && $this->Disp();
		
	}
	
	public function Disp(){
		
		self::WriteLine(sprintf(
			"Aisle %s:\nrun time: %s Seconds\nmemory usage: %s MBytes",
			$this->name,
			$this->endTime-$this->startTime,
			($this->endMem-$this->startMem)/1024/1024
		));
	}
}

