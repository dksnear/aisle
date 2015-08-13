<?php
namespace aisle\ex;

class WebException extends Exception{
	
	protected $exType = 'WebException';
	
	protected $exMsg = 'Project Inner Web Exception Occurred!';
	
}
