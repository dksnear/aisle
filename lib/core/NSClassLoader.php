<?php
namespace aisle\core;
require('ClassLoader.php');

abstract class NSClassLoader extends ClassLoader{
	
	protected function preLoad(){
	
		$this->appendLoaded($this->nsRoot.'\\ClassLoader');		
	}
	
	protected function load($className){
	
		$this->appendRegist($className);
		parent::load($className);
	}
		
	protected function appendRegist($className,$path=null){
		
		if(!is_null($path))
			return $this->registClassMap[$className] = $path;
			
		$this->scanRoot = is_array($this->scanRoot) ? $this->scanRoot : array($this->scanRoot);

		foreach($this->scanRoot as $scanRoot){
			
			if(!is_dir($scanRoot))
				continue;
			
			$path = preg_replace('/^'.$this->nsRoot.'\\\\/','',$className);
			$path = $scanRoot.DIRECTORY_SEPARATOR.preg_replace('/\\\\/',DIRECTORY_SEPARATOR,$path).'.php';
			if(file_exists($path)){	
				$this->registClassMap[$className] = $path;
				break;
			}
		}

	}
	
	protected function appendLoaded($className,$path=null){
		
		if(!class_exists($className))
			return false;
		
		$this->appendRegist($className,$path);
		$this->loadedClassMap[$className] = $className;
		
		return true;
		
	}
	
}

