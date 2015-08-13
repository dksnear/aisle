<?php
namespace aisle\core;

class Reflection extends \ReflectionClass{
	
	public function NewInstanceWithAssocArgs($args){
		
		if(empty($args))
			return $this->newInstance();
		
		$constructor = $this->getConstructor();
		
		if(empty($constructor)) return $this->newInstance();
		
		$params = array();
		
		foreach($constructor->getParameters() as $param){
			
			$params []= isset($args[$param->name]) ? 
				$args[$param->name] : 
				($param->isDefaultValueAvailable() ? $param->getDefaultValue() : null);
			
		}
		
		if(empty($params)) return $this->newInstance();
		
		return $this->newInstanceArgs($params);
		
	}
}

