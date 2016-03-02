<?php
namespace aisle\proj\shell;

use aisle\core\Trace;
use aisle\web\Controller;

class ArrangeController extends Controller{
	
	public function Wel(){
		
		return PHP_VERSION;
	}
	
	public function Php(){
		
		phpinfo();
		return 1;
	}
	
}