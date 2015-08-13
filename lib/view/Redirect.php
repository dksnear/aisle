<?php
namespace aisle\view;
use aisle\web\Response;
use aisle\web\Request;

class Redirect extends Basic{
	
	//@localRoot 本地路径
	protected $localRoot;
	
	//@abs(bool) 绝对地址
	protected $abs = false;
		
	public function Render($statements=null,$ret = false){
		
		$statements = parent::Render($statements,true);
		
		if(!$this->abs){
			
			$this->localRoot = $this->localRoot ? $this->localRoot : dirname((new Request())->Server('SCRIPT_NAME')).'/$static_page';
			$statements = $this->localRoot.'/'.$statements;
		}
		
		(new Response())->Header('location: '.$statements);
		
	}
	
}