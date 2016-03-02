<?php
namespace aisle\web;
use aisle\ex\RoutingFailedException;
use aisle\attr\AttributeResolver;

class Router{

	public static function Resolve($routers,$managers,$request=null){
			
		(count($routers) > 1) && usort($routers,function($left,$right){
				
			if ($left->order == $right->order) return 0;
			return ($left->order < $right->order) ? -1 : 1;
			
		});
		
		$res = null;
				
		foreach($routers as $router){
			
			if(!empty($request))
				$router->SetRequest($request);
					
			$res = $router->Match();
			
			if(!$res)
				continue;
			
			break;
			
		}
				
		if(!$res) 
			throw new RoutingFailedException();
		
		return (new AttributeResolver($managers['confm']))->Invoke(
			$res['class'],
			$res['method'],
			array(
				'managers'=>$managers,
				'request'=>new Request($res['params'])
			),
			$res['params']);
		
	}
	
	protected $order;
	
	protected $controller;
	
	protected $action;
	
	protected $params;
		
	// @aisle\web\Request;
	protected $request;
	
	public function __construct($controller,$action,$order=1,$request=null){
		
		$this->request = $request ? $request : new Request();
		$this->params = $this->request->Param();
		$this->controller = $controller;
		$this->action = $action;
		$this->order = $order;
	}
	
	public function __get($name){
			
		return property_exists($this,$name) ?  $this->$name : null;
	}
	
	public function SetRequest($request){
		
		$this->request = $request;
		$this->params = $request->Param();
	}
	
	public function Match(){
		
		$class = $this->parseController();
		
		if(!$class) return false;
		
		$method = $this->parseAction($class);
		
		if(!$method) return false;
		
		return array(
		
			'class' => $class,
			'method' => $method,
			'params' => $this->params
		);
	}

	protected function parseController(){
		
		if(empty($this->controller))
			return false;
		
		$class = $this->controller;
		$count = count($class);
		
		for($i = 1; $i < $count; $i++){
			
			$key = $class[$i];
			
			$class[$i] = $this->request->Param($key);
			
			if(empty($class[$i])) return false;
			
			unset($this->params[$key]);
					
		}
		
		$class = call_user_func_array('sprintf',$class).'Controller';
	
		if(!class_exists($class))
			throw new RoutingFailedException();
		
		return $class;
	}
	
	protected function parseAction($class){
		
		$method = $this->action;
		$count = count($method);
		
		for($i = 1; $i < $count; $i++){
			
			$key = $method[$i];
			
			$method[$i] = $this->request->Param($key);
			
			if(is_null($method[$i])) return false;
			
			unset($this->params[$key]);
					
		}
		
		$method = call_user_func_array('sprintf',$method);
		
		return $method ? $method : false;	
	}
}
