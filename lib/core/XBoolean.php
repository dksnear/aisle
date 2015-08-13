<?php
namespace aisle\core;

class XBoolean extends XType{
	
	public static function Build($meta){
		
		return new self($meta);
	}
	
	public function Write(){
		
		print_r($this->Meta() ? 'true' : 'false');
		
		return $this;
	}
		
	protected function compile($meta){
		
		$this->meta = (boolean)$meta;
		
		return $this;
	}
}

