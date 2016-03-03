<?php
namespace aisle\core;
require('ClassLoader.php');

abstract class NSClassLoader extends ClassLoader{
	
	// 根命名空间
	protected $nsRoot = 'aisle';
	
	protected function __construct($pclsMap=null){
		
		parent::__construct();
		$this->preRegist($pclsMap);
	}
	
	protected function preRegist($pclsMap){
		
		if(empty($pclsMap)) 
			return $this;
		
		foreach($pclsMap as $clsn=>$path)
			$this->appendRegist($clsn,$path);
		
		return $this;
	}
	
	protected function load($className){
			
		$this->appendRegist($className);
		parent::load($className);
	}
		
	protected function appendRegist($className,$path=null){
				
		if(isset($this->registClassMap[$className]))
			return $this->registClassMap[$className] = file_exists($path) ? $path : $this->registClassMap[$className];	
				
		if(file_exists($path))
			return $this->registClassMap[$className] = $path;
		
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
		
		if(!class_exists($className,false))
			return false;
		
		$this->appendRegist($className,$path);
		$this->loadedClassMap[$className] = $className;
		
		return true;
		
	}
	
}

