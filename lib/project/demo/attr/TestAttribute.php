<?php
namespace aisle\project\demo\attr;

use aisle\core\Trace;
use aisle\attr\Attribute;

class TestAttribute extends Attribute{
	
	protected $methodAffect = true;
	
	public function BeforeInvoke(){
		
		Trace::WriteLine('before_invoke');
		
	}
	
	public function AfterInvoke(){
		
		Trace::WriteLine('after_invoke');
	}
}