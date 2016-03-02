<?php
namespace aisle\proj\doki8\log;
use aisle\core\Trace;
use aisle\log\FileLog;

class GrabLog extends FileLog{

	public function Write($statements){
		
		Trace::WriteLine($statements);
		
		parent::Write(array( 'desc'=>$statements ));
		
	}
	
} 