<?php
namespace aisle\conf;

class SimpleExtensionConfig implements IExtensionConfig{
		
	protected $options;

	public function Load($statements,$appConfig){
		
		$this->options = $statements;
	}
	
	public function Get($key){
				
		return isset($this->options[$key]) ? $this->options[$key] : null;
	}
	
}

