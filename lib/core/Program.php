<?php
namespace aisle\core;
require('NSClassLoader.php');
use aisle\conf\ConfigManager;
use aisle\cache\CacheManager;
use aisle\log\LogManager;
use aisle\db\DbClientManager;
use aisle\view\ViewManager;
use aisle\ex\ExceptionManager;
use aisle\web\Router;
use aisle\ex\CoreException;

class Program extends NSClassLoader{
	
	public static function Get($prop=null){
		
		if(empty(self::$inst))
			throw new CoreException('program can not use!');
		
		if($prop)
			return self::$inst->$prop;
		
		return self::$inst;
	}
	
	// 注册一个全局实例引用
	public static function GlobalRegist($name=null){
			
		$name = $name ? $name : 'AISLE_PROGRAM';
		
		$GLOBALS[$name] = self::Get();
		
		return $GLOBALS[$name];
		
	}
	
	public static function Build($class=null,$regName=null,$pclsMap=null,$trace=false){
		
		if(!empty(self::$inst))
			return self::$inst;
	
		self::$inst = !$class ? new self($pclsMap,$trace) : new $class($pclsMap,$trace);		
		return self::GlobalRegist($regName);
	}
	
	public static function BuildWithTrace($class=null,$regName=null,$pclsMap=null){
		
		return self::Build($class,$regName,$pclsMap,true);
	}
	
	public static function BuildWithPreLoad($pclsMap=null,$class=null,$regName=null,$trace=false){
		
		return self::Build($class,$regName,$pclsMap,$trace);
	}
	
	protected static $inst;
	
	protected $managers;
	
	protected $scanRoot = array('../../lib','../..');
	
	protected $trace = false;
	
	protected function __construct($pclsMap=null,$trace=false){
		
		parent::__construct($pclsMap);
		$this->trace = $trace;
		if($this->trace)
			Trace::Begin('aisle program');
		$this->buildManagers();
		
	}
	
	public function __get($name){
		
		if(property_exists($this,$name))
			return $this->$name;
		if(isset($this->managers[$name]))
			return $this->managers[$name];
		
		return null;
	}
			
	public function Run(){
		
		// var_dump($_REQUEST);
		// $this->confm && var_dump($this->confm->Statements());
		
		$this->viewm->Render(Router::Resolve($this->confm->Config()->GetRouters(),$this->managers));
		
		if($this->trace)
			Trace::Eject();
		
		
	}
	
	protected function buildManagers(){
		
		$this->managers['confm'] = $this->createConfigManager();
		$this->managers['cachem'] = $this->createCacheManager();
		$this->managers['logm'] = $this->createLogManager();
		$this->managers['dbm'] = $this->createDbClientManager();
		$this->managers['viewm'] = $this->createViewManager();
		$this->managers['exm'] = $this->createExceptionManager();
	}
		
	// close fileScan
	protected function fileScan($currentFileName,$targetFileName,$level=1,&$finded=false){
			
		return false;
	}
	
	protected function createConfigManager(){
		
		$paths = array();
		
		foreach($this->scanRoot as $scanRoot){
			
			if(!file_exists($scanRoot.'/$source/config.acj'))
				continue;
			$paths []= $scanRoot.'/$source/config.acj';
		}
		
		if(file_exists('./$source/config.acj'))
			$paths []= './$source/config.acj';
		if(file_exists('./$source/config-cover.acj'))
			$paths []= array('./$source/config-cover.acj',true);
		
		return new ConfigManager($paths);
	}
	
	protected function createCacheManager(){
		
		return new CacheManager($this->confm);
	}
	
	protected function createLogManager(){
		
		return new LogManager($this->confm);
	}
	
	protected function createDbClientManager(){
		
		return new DbClientManager($this->confm);
	}
	
	protected function createViewManager(){
		
		return new ViewManager($this->confm);
	}
	
	protected function createExceptionManager(){
		
		return new ExceptionManager($this->logm,$this->viewm,$this);
	}
		
}
