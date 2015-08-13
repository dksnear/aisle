<?php
namespace aisle\core;
require('ClassLoader.php');

abstract class NSClassLoader extends ClassLoader{
	
	protected function preLoad(){
	
		$this->appendLoaded($this->nsRoot.'\ClassLoader');		
	}
	
	protected function load($className){
	
		$this->appendRegist($className);
		parent::load($className);
	}
		
	protected function appendRegist($className,$path=null){
		
		if(!is_null($path))
			return $this->registClassMap[$className] = $path;
		
		preg_replace_callback('/^'.$this->nsRoot.'\\\\(.*)$/',function($m) use($className){
			
			$path = $this->scanRoot.'/'.preg_replace('/\\\\/','/',$m[1]).'.php';

			if(file_exists($path))	
				$this->registClassMap[$className] = $path;
							
		},$className);	
		
	}
	
	protected function appendLoaded($className,$path=null){
		
		if(!class_exists($className))
			return false;
		
		$this->appendRegist($className,$path);
		$this->loadedClassMap[$className] = $className;
		
		return true;
		
	}
	
}

