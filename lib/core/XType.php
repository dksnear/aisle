<?php
namespace aisle\core;

use aisle\ex\CoreException;

class XType{
	
	private static $TYPEMAP = array(
		
		'boolean'=>'XBoolean',	
		'integer'=>'XNumeric',
		'double'=>'XNumeric',	
		'string'=>'XString',	
		'array'=>'XArray',	
		'object'=>'XType',	
		'resource'=>'XType',	
		'NULL'=>'XType',
		'unknown type'=>'XType'	
	);
	
	public static function Build($meta){
		
		$type = self::GetType($meta);
		return new $type($meta);
	}
	
	// 根据数据返回一个XType类
	public static function GetType($meta){
				
		return self::GetXType(gettype($meta));	
	}
	
	// 根据类型字符串返回一个XType类
	public static function GetXType($type){
		
		return  __NAMESPACE__.'\\'.(isset(self::$TYPEMAP[$type]) ? self::$TYPEMAP[$type] : 'XType');
	}
		
	public static function Empty_($v){
		
		return empty($v) && $v !== 0 && $v !=='0';
	}
	
	public static function And_(){
		
		$args = func_get_args();
		$count = count($args);
		
		for($i=0;$i<$count-1;$i++){
			
			if(self::Empty_($args[$i])) return $args[$i];
		}
		
		return $args[$i];
	}
	
	public static function Or_(){
		
		$args = func_get_args();
		$count = count($args);
			
		for($i=0;$i<$count-1;$i++){
			
			if(!self::Empty_($args[$i])) return $args[$i];
		}
		
		return $args[$i];
	}

	protected $meta;
	
	protected $previous;
	
	protected $next;
	
	protected $callableMethodMap = array();
	
	public function __construct($meta,$prev = null){
		
		$this->complie($meta);
		$this->setPrevious($prev);
	}
	
	public function __call($method,$args){
		
		if(!isset($this->callableMethodMap[$method]))
			throw new CoreException('method "'.$method.'" not exists!');
		
		array_unshift($args,$this->meta);
				
		return $this->resolveType(call_user_func_array($this->callableMethodMap[$method],$args));
		
	}
	
	public function Meta(){
		
		return $this->meta;
	}
	
	public function Write(){
		
		print_r($this->Meta());
		
		return $this;	
	}
	
	public function WriteLine(){
		
		print_r($this->Write());
		print_r("\n");
		return $this;
	}
	
	public function Serialize(){
		
		return $this->resolveType(serialize($this->meta));
	}
	
	// 强行转换或者重新构造一个XType类的实例
	public function XTypeTo($type){
		
		$type = self::GetXType($type);
		return $this->SetNext(new $type($this->meta,$this))->Next();
	}
	
	public function Call($callee){
		
		$args = func_get_args();
		array_shift($args);
		array_push($args,$this->Meta());
				
		return call_user_func_array($callee,$args);
	}
	
	public function Action($callee){
		
		return $this->resolveType($callee($this));
	}
	
	public function Previous(){
		
		return $this->previous;
	}
	
	public function Next(){
		
		return $this->next;
	}
	
	public function P(){
		
		return $this->previous();
	}
	
	public function N(){
		
		return $this->next();
	}
	
	protected function setPrevious($prev){
		
		$this->previous = $prev;
		
		return $this;
	}
	
	protected function setNext($next){
		
		$this->next = $next;
		
		return $this;
	}
	
	protected function resolveType($meta){
		
		if($meta instanceof self)
			return $meta;
				
		$type = self::GetType($meta);
	
		return $this->setNext(new $type($meta,$this))->Next();
		
	}
	
	protected function complie($meta){
		
		$this->meta = $meta;		
		return $this;		
	}
	
}

