<?php
namespace aisle\conf;

class ClassMapConfig{
	
	protected $groups;
	
	public function __construct($statements){
		
		$this->groups = $statements;
	}
	
	public function Get($group,$alias){
		
		$group = isset($this->groups[$group]) ? $this->groups[$group] : null;
		
		if(empty($group)) return null;
		
		return isset($group[$alias]) ? $group[$alias] : null;
	}
}

