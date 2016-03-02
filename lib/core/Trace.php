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
	
	public static function Begin($name=null,$reveal=true){
		
		return new self($name,$reveal);
	}
	
	public static function Eject(){
		
		while(count(self::$popStack)){
			
			$trace = array_pop(self::$popStack);
			$trace->End();
		}
	}
	
	private $isRuning = false;
	
	protected $name = 'aisle trace ';
	
	protected $startTime = 0;
	
	protected $endTime = 0;
	
	protected $startMem = 0;
	
	protected $endMem = 0;
	
	protected $reveal = true;
		
	public function __construct($name=null,$reveal=true){
		
		array_push(self::$popStack,$this);
		
		$this->name = $name ? $name : $this->name.count(self::$popStack);
		$this->Start($reveal);
		$this->reveal = $reveal;
	}
	
	public function __get($prop){
		
		if(isset($this->$prop))
			return $prop;
		
		return null;
	}
	
	public function Start(){
		
		if($this->isRuning)
			return;
	
		$this->startTime = microtime(true);
		$this->startMem = memory_get_usage();
		$this->endTime = 0;
		$this->endMem = 0;		
		$this->isRuning = true;
	
		if(!$this->reveal) return;
		
		self::WriteLine(sprintf(
			"\n%s has begun!\ncurrent time: %s Seconds\ncurrent memory: %s MBytes",
			$this->name,
			$this->startTime,
			($this->startMem)/1024/1024
		));
	
	}
	
	public function End(){
		
		if(!$this->isRuning)
			return;
		
		$this->endTime = microtime(true);
		$this->endMem = memory_get_usage();		
		$this->isRuning = false;
			
		if(!$this->reveal) return;
		
		self::WriteLine(sprintf(
			"\n%s has ended!\nrun time: %s Seconds\nmemory usage: %s MBytes",
			$this->name,
			$this->endTime-$this->startTime,
			($this->endMem-$this->startMem)/1024/1024
		));
		
	}
}

