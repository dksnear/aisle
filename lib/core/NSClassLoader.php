<?php
namespace aisle\core;
require('ClassLoader.php');

abstract class NSClassLoader extends ClassLoader{
	
	//根目录配置
	//root:array(items) items:array('ns'=>nsRoot,'scans'=>scanRoots) nsRoot:string nsroot, scanRoots:array[scanroot1,scanroot2,..]
	protected $root = array(array('ns'=>'aisle','scans'=>array('.')));
		
	public function PreRegist($pclsMap){
		
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
	
	protected function eachRoot($fn){
		
		if(empty($this->root) || !is_array($this->root) || !is_callable($fn)) 
			return $this;
		
		foreach($this->root as $item){
			
			if(!is_array($item))
				continue;
			
			$ns = !isset($item['ns']) ? null : (empty($item['ns']) ? '' : $item['ns']);
			$scans = !isset($item['scans']) ? null : (!is_array($item['scans']) ? null : $item['scans']);
			
			if(empty($scans))
				continue;
			
			foreach($scans as $scan){
				
				if($fn($this->trimSlash($scan),$this->trimSlash($ns),$item) === false)
					break;
			}
		}
		
		return $this;
	}
		
	protected function appendRegist($className,$path=null){
				
		if(isset($this->registClassMap[$className]))
			return $this->registClassMap[$className] = file_exists($path) ? $path : $this->registClassMap[$className];	
				
		if(file_exists($path))
			return $this->registClassMap[$className] = $path;
		
		$this->eachRoot(function($scanRoot,$ns) use($className){
			
			if(!is_dir($scanRoot))
				continue;

			$path = preg_replace('/^'.$ns.'\\\\/','',$className);
			$path = $scanRoot.DIRECTORY_SEPARATOR.preg_replace('/\\\\/',DIRECTORY_SEPARATOR,$path).'.php';
			if(file_exists($path)){	
				$this->registClassMap[$className] = $path;
				return false;
			}
			
		});
	}
	
	protected function appendLoaded($className,$path=null){
		
		if(!class_exists($className,false))
			return false;
		
		$this->appendRegist($className,$path);
		$this->loadedClassMap[$className] = $className;
		
		return true;
		
	}
		
}

