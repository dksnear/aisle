<?php
namespace aisle\view;

use aisle\web\Request;
use aisle\web\Response;

class Json extends Basic{
		
	protected $callback;
		
	public function Render($statements=null,$ret=false){
		
		$statements = parent::Render($statements,true);
		
		(new Response())->Header('Content-Type:text/javascript');
		
		$callback = $this->callback;
		$callback = (new Request())->Form($callback);
		$callback = $callback ? $callback : $this->callback;	
		$callback = explode('*',$callback);
		
		$method_name = array_shift($callback);
		$params = '';
		
		if(!empty($callback))
			$params = ','.implode(',',$callback);

		$out = sprintf('%s(%s%s);',$method_name,(new Json())->Render($statements,true),$params);
		
		if($ret) return $out;
		
		echo $out;
		
	}
	
}