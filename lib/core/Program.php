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
		
	}
	
	public static function Build($class=null){
		
		if(!empty(self::$inst))
			return self::$inst;
	
		self::$inst = !$class ? new self() : new $class();
		
		self::$inst->BuildManagers();
		self::$inst->Run();
		
		return self::$inst;
	}
	
	protected static $inst;
	
	protected $managers;
	
	protected function __construct(){
		
		parent::init();
		
	}
	
	public function __get($name){
		
		if(property_exists($this,$name))
			return $this->$name;
		if(isset($this->managers[$name]))
			return $this->managers[$name];
		
		return null;
	}
	
	public function Trace(){
		
		var_dump($_REQUEST);
		$this->confm && var_dump($this->confm->Statements());
		
		return $this;
		
	}
	
	public function BuildManagers(){
		
		$this->managers['confm'] = $this->createConfigManager();
		$this->managers['cachem'] = $this->createCacheManager();
		$this->managers['logm'] = $this->createLogManager();
		$this->managers['dbm'] = $this->createDbClientManager();
		$this->managers['viewm'] = $this->createViewManager();
		$this->managers['exm'] = $this->createExceptionManager();
	}
	
	public function Run(){
		
		$this->viewm->Render(Router::Resolve($this->confm->Config()->GetRouters(),$this->managers));
	}
		
	// close fileScan
	protected function fileScan($currentFileName,$targetFileName,$level=1,&$finded=false){
			
		return false;
	}
	
	protected function createConfigManager(){
		
		return new ConfigManager('./$source/config.acj');
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
		
		return new ExceptionManager($this->logm,$this->viewm);
	}
		
}
