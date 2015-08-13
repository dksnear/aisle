<?php
namespace aisle\core;

abstract class ClassLoader{

	// 加载路径映射表
	protected $registClassMap;
	
	// 已加载映射表
	protected $loadedClassMap;
	
	// 扫描器根目录
	protected $scanRoot = '.';
	
	// 根命名空间
	protected $nsRoot = 'aisle';
	
	public function __get($name){
		
		return isset($this->$name) ? $this->$name : null;
	}

	// 初始化类加载器
	protected function init(){
		
		$this->registClassMap = array();
		$this->loadedClassMap = array();
		$this->preLoad();
		$this->regist();
		
	}
	
	// 预加载初始化
	protected function preLoad(){
		
		
	}

	// 加载器
	protected function load($className){
				
		if(isset($this->loadedClassMap[$className]))
			return $this->loadedClassMap[$className];
		
		if(isset($this->registClassMap[$className])){
			
			//require_once($this->registClassMap[$className]);
			require($this->registClassMap[$className]);
			$this->loadedClassMap[$className] = $className;
			return $className;
		}
		
		$this->fileScan($this->scanRoot,$className);
	}
	
	// 加载器注册
	protected function regist(){
		
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
			while($hFile=readdir($hDir)){
				
				if($finded) break;
				
				if($hFile=='.'||$hFile=='..')
					continue;
				
				$this->fileScan($currentFileName.'/'.$hFile,$targetFileName,$level,$finded);
				
			}
			closedir($hDir);	
		}
	}
	
	// 文件扫描动作
	protected function fileScanAction($currentFileName,$targetFileName,$level,&$finded){
		
		$basename = preg_replace('/\.php$/','',basename($currentFileName));
		$this->registClassMap[$basename] = $currentFileName;				
		$finded = $basename == $targetFileName;
		$finded && $this->load($basename);

	}
	
}

