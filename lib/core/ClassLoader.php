<?php
namespace aisle\core;

abstract class ClassLoader{

	// 加载路径映射表
	protected $registClassMap;
	
	// 已加载映射表
	protected $loadedClassMap;
	
	// 根目录配置
	//root: array[scanroot1,scanroot2,..]
	protected $root = array('.');
	
	protected function __construct(){

		$this->registClassMap = array();
		$this->loadedClassMap = array();
		$this->splRegist();
	}
	
	public function __get($name){
		
		return property_exists($this->$name) ? $this->$name : null;
	}
	
	public function SetRoot($root){
		
		$this->root = empty($this->root) ? array() : $this->root;
		
		foreach($root as $item)
			$this->root []= $item;
			
		return this;
	}
	
	public function AddRoot($item){
		
		$this->root = empty($this->root) ? array() : $this->root;
		$this->root []= $item;
		return this;
	}

	// 加载器
	protected function load($className){
	
		if(isset($this->loadedClassMap[$className]))
			return $this->loadedClassMap[$className];
		
		if(isset($this->registClassMap[$className])){
			
			require($this->registClassMap[$className]);
			return $this->loadedClassMap[$className] = $className;
		}
					
		$this->eachRoot(function($scanRoot,$item){
			
			$finded = false;
			
			$this->fileScan($this->scanRoot,$className,1,$finded);
			
			if($finded)
				return false;
		});
	}
	
	protected function eachRoot($fn){
		
		if(empty($this->root) || !is_array($this->root) || !is_callable($fn)) 
			return $this;
		
		foreach($this->root as $scan){
			
			if($fn($this->trimSlash($scan)) === false)
				break;
			
		}
		
		return $this;
	}
	
	// 加载器注册
	protected function splRegist(){
		
		spl_autoload_register(array($this,'load'));
				
	}
		
	// 文件扫描器
	protected function fileScan($currentFileName,$targetFileName,$level=1,&$finded=false){
				
		if($finded) return;

		if(is_file($currentFileName)){
			
			if(!preg_match('/\.php$/',$currentFileName)) return;
			
			return $this->fileScanAction($currentFileName,$targetFileName,$level,$finded);
		}
		
		if(is_dir($currentFileName)){
			
			$level+=1;
			$hDir = opendir($currentFileName);
			while($hFile = readdir($hDir)){
				
				if($finded) break;
				
				if($hFile=='.'||$hFile=='..')
					continue;
				
				$this->fileScan($currentFileName.DIRECTORY_SEPARATOR.$hFile,$targetFileName,$level,$finded);
				
			}
			closedir($hDir);	
		}
	}
	
	// 文件扫描动作
	protected function fileScanAction($currentFileName,$targetFileName,$level,&$finded){
		
		$basename = preg_replace('/\.php$/','',basename($currentFileName));
		$this->registClassMap[$basename] = $currentFileName;				
		$finded = preg_replace('/\//','\\\\',$basename) == $targetFileName;
		$finded && $this->load($basename);

	}
	
	protected function trimSlash($dir){
		
		return preg_replace('/^\\\\|^\/|\\\\$|\/$/','',$dir);
	}
	
}

