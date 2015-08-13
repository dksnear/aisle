<?php
namespace aisle\conf;

use aisle\web\Router;
use aisle\ex\ConfigException;

class AppConfig {
	
	protected $classMap;
	
	protected $dbClient;
	
	protected $cacheClient;
	
	protected $logClient;
	
	protected $viewClient;
	
	// 扩展配置
	protected $extClient;
	
	protected $logs = array();
	
	protected $cache;
	
	protected $view;
	
	protected $db;
	
	protected $routers = array();
	
	protected $timezone;
	
	protected $statements;
	
	public function __construct($statements){
		
		$this->statements = $statements;
		$this->resolve();
	}
	
	public function __call($name,$args){
		
		if(!preg_match('/^Get(\w+)$/',$name,$match))
			return null;

		$name = lcfirst($match[1]);
		
		if(!property_exists($this,$name))
			return null;
		
		$prop = $this->$name;
		
		// prop not in config
		if(is_null($prop))
			return null;
		
		$arg0 = isset($args[0]) ? $args[0] : null;
		$arg1 = isset($args[1]) ? $args[1] : null;
		
		if($name == 'classMap')
			$prop = $prop->Get($arg0,$arg1);
		if($name == 'dbClient' || $name == 'viewClient')
			$prop = $prop->Inst($arg0,$this->classMap);
		if($name == 'cacheClient' || $name == 'logClient')
			$prop = $prop->Inst($arg0,$this->classMap,$this->dbClient);
		if($name == 'extClient')
			$prop = $prop->Inst($arg0,$this->classMap)->Get($arg1);
		
		if(is_null($prop))
			throw new ConfigException(sprintf('some errors occured in config segment "%s"! ',$name));
		
		return $prop;
		
	}
	
	protected function resolve(){
		
		$statements = $this->statements;
		
		if(isset($statements['timezone']) && $statements['timezone']){
			
			$this->timezone = $statements['timezone'];
			date_default_timezone_set($this->timezone);
		}
		
		if(isset($statements['class-map']) && $statements['class-map']){
			
			$this->classMap = new ClassMapConfig($statements['class-map']);
		}
		
		if(isset($statements['db-client']) && $statements['db-client']){
			
			$this->dbClient = new DbConfig($statements['db-client']);
		}
		
		if(isset($statements['cache-client']) && $statements['cache-client']){
			
			$this->cacheClient = new CacheConfig($statements['cache-client']);
		}
		
		if(isset($statements['log-client']) && $statements['log-client']){
			
			$this->logClient = new LogConfig($statements['log-client']);
		}
		
		if(isset($statements['view-client']) && $statements['view-client']){
			
			$this->viewClient = new ViewConfig($statements['view-client']);	
		}
		
		if($this->cacheClient && isset($statements['cache']) && $statements['cache']){
			
			$this->cache = $this->cacheClient->Inst($statements['cache'],$this->classMap,$this->dbClient);				
		}
		
		if($this->logClient && isset($statements['logs']) && $statements['logs']){

			foreach($statements['logs'] as $log)
				$this->logs []= $this->logClient->Inst($log,$this->classMap,$this->dbClient);
			
		}
		
		if($this->viewClient && isset($statements['view']) && $statements['view']){
			
			$this->view = $this->viewClient->Inst($statements['view'],$this->classMap);
		}
		
		if($this->dbClient && isset($statements['db']) && $statements['db']){
			
			$this->db = $this->dbClient->Inst($statements['db'],$this->classMap);
		}

		if(isset($statements['routers']) && $statements['routers']){
						
			foreach($statements['routers'] as $router){
				
				$router = array_merge(array(
				
					'controller' =>  array(),
					'action'  =>  array(),
					'order'  =>  1
					
				),$router);
				
				$this->routers []= new Router($router['controller'],$router['action'],$router['order']);
				
			}
		
		}
		
		if(isset($statements['extension']) && $statements['extension']){
			
			$this->extClient = new ExtensionConfig($statements['extension'],$this);	
		}
		
	}
	
	
}

