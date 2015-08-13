<?php
namespace aisle\view;
use aisle\ex\ConfigException;

class ViewManager{
	
	protected static $RENDERED = false;
	
	protected $confm;
	
	protected $default;
	
	// @confm ConfigManager
	public function __construct($confm){
		
		$this->confm = $confm;
		$this->default = $this->confm->Config()->GetView();
		
	}
	
	public function Client($name){
		
		return $this->confm->Config()->GetViewClient($name);
	}
	
	public function Complete(){
		
		self::$RENDERED = true;
	}
	
	public function Render($statements){
	
		if(self::$RENDERED)
			return;
		
		$this->Complete();
						
		if($statements instanceof IView)
			return $statements->Render();
		
		if(!$this->default)
			throw new ConfigException('default view can not find in config!');
		
		return $this->default->Render($statements);
			
	}
}