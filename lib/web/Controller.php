<?php
namespace aisle\web;

use aisle\ex\WebException;

class Controller{

	// aisle\web\Request
	protected $request;
	
	// aisle\web\Response
	protected $response;
	
	// @array
	// 'cachem'=>@aisle\cache\CacheManager
	// 'dbm'=>@aisle\db\DbClientManager
	// 'viewm'=>@aisle\view\ViewManager
	// 'logm'=>@aisle\log\LogManager
	// 'confm'=>@aisle\conf\ConfigManager
	protected $managers;
	
	public function __construct($managers,$request=null){
		
		$this->managers = $managers;
		$this->request = $request ? $request : new Request();
		$this->response = new Response();

	}
	
	public function __get($name){
		
		if(isset($this->$name))
			return $this->$name;
		
		if(isset($this->managers[$name]))
			return $this->managers[$name];
		
		throw new WebException(sprintf('property "%s" can not init!',$name));
		
	}
	
	protected function view($name,$statements,$direct=false){
		
		$View = '\\aisle\\view\\'.ucfirst($name);
				
		if(class_exists($View) && is_subclass_of($View,'\\aisle\\view\\IView'))
			return (new $View())->Set($statements,$direct);
		
		return $this->viewm->Client($name)->Set($statements,$direct);
	}
	
	// @abs 绝对地址
	protected function redirect($url,$abs=false){
				
		return $this->view('Redirect',array(
	
			'statements'=>$url,
			'abs'=>$abs
		));
	}
	
	protected function page($path,$ext='html'){
	
		return $this->view('HtmlPage',array(
		
			'statements'=>$path,
			'pageExt'=>$ext
		));
	}

}
