<?php
namespace aisle\ex;

interface IException{
	
	// @logm LogManager
	public function Write($logm);
	
	// @exportm ViewManager
	public function Render($viewm);
	
}
