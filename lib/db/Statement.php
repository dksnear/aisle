<?php
namespace aisle\db;

class Statement{
	
	public static function GetOp($opName,$typeName){
		
		return $opName ? self::$OP_MAP[$typeName][$opName] : null;	
	}
	
	protected static $OP_MAP = array(
	
		'table'=>array('select'=>1,'insert'=>2,'update'=>4,'delete'=>8,'where'=>16,'limit'=>32,'desc'=>64,'asc'=>128,'groupby'=>256,'having'=>512,'join'=>1024,'leftjoin'=>2048,'rightjoin'=>4096,'on'=>8192,'union'=>16384,'unionall'=>32768),
		
		'cond'=>array('and'=>1,'or'=>2,'exists'=>4,'notexists'=>8,'in'=>16,'ins'=>32,'notin'=>64,'notins'=>128,'gt'=>256,'gte'=>512,'lt'=>1024,'lte'=>2048,'eq'=>4096,'ne'=>8192,'like'=>16384,'null'=>32768,'notnull'=>65536,'between'=>131072,'notbetween'=>262144),
		
		'field'=>array('avg'=>1,'count'=>2,'min'=>4,'max'=>8,'sum'=>16)
		
	);
	
	// table | cond | field
	protected $type;
	
	protected $operator;
	
	protected $operands;
	
	protected $order;
	
	public function __construct($type,$operator,$operands,$order=1){
		
		$this->type = $type;
		$this->operator = self::GetOp($operator,$type);
		$this->operands = $operands;
		$this->order = $order;
		
	}
		
	public function __get($propName){
		
		if(isset($this->$propName))
			return $this->$propName;
		
		return null;
	}
		
	public function OpIn($ranges){
		
		return $this->operator && ($ranges & $this->operator) == $this->operator;
	}
	
	public function OpRanges(){
				
		return array_reduce(func_get_args(),function($current,$item){
			
			return $current | self::GetOp($item,$this->type);
			
		},0);
		
	}
}