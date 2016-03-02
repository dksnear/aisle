<?php
namespace aisle\proj\accurate\view;

use  aisle\view\Message;

class Message extends Message{
	
	protected token;
	
	public function __construct($data,$success=true,$type='DATA',$code=1){
		
		parent::__construct($data,$success,$type,$code);
		
		$this->token = '';
	}
		
    public function jsonSerialize(){
		
		return array_merge(parent::jsonSerialize(),array('token'=>$this->token));
	}
	
}