<?php
namespace aisle\view;

class Json extends Basic{
	
	public function Render($statements=null,$ret=false){
		
		$statements = parent::Render($statements,true);
		
		$message = $statements instanceof Message ? $statements : new Message($statements);
				
		$out = json_encode($message->jsonSerialize());
		
		if($ret) return $out;
		
		echo $out;
	}
	
}