<?php
namespace aisle\view;

class Basic implements IView{
	
	protected $statements;
	
	public function Set($options,$direct=false){
		
		if($direct){
			
			$this->statements = $options;
			return $this;
		}
		
		foreach($options as $key=>$value)
			if(property_exists($this,$key))
				$this->$key = $value;
		
		return $this;
	}
	
	public function Render($statements=null,$ret=false){
		
		$statements = $statements ? $statements : $this->statements;
		
		if($ret) return $statements;
		
		print_r($statements);
	}
	
	public function Notify($viewm){
		
		$viewm->Complete();
	}
	
}