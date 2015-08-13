<?php
namespace aisle\ex;
use aisle\view\Message;

class RoutingFailedException extends ClientException{
	
	protected $exType = 'RoutingFailedException';
			
	public function __construct(){
		
		parent::__construct('Request URL Was Not Found On This Server!',1);
	}
	
}
