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
						
		if($statements instanceof IView){
			
			$statements->Render();
			$statements->Notify($this);
			return;
		}
		
		if(!$this->default)
			throw new ConfigException('default view can not find in config!');
		
		$this->default->Render($statements);
		$this->default->Notify($this);
			
	}
}