<?php
namespace aisle\log;

use aisle\core\File;

class FileLog implements ILog{

	protected $path = './$source/log/AISLE_SYS_FILE_LOG';
	
	public function Connect($options = array()){
		
		foreach($options as $key=>$value){
			
			if(property_exists($this,$key))
				$this->$key = $value;
		}
		
		return $this;
	}

	public function Write($statements){
		
		// xml
		// return File::Write($this->path,"<aisle-sys-log>\n    <time>".date('Y-m-d H:i:s')."</time>\n".implode('',array_map(function($key) use($statements) { 
			// return sprintf("    <%s>%s</%s>\n",$key,$statements[$key],$key);
		// },array_keys($statements)))."</aisle-sys-log>\n\n",'a');

		// json
		// return File::Write($this->path,"\"aisle-sys-log\":{\n    \"time\":".date('Y-m-d H:i:s').",\n".implode(",\n",array_map(function($key) use($statements) { 
			// return sprintf("    \"%s\":\"%s\"",$key,str_replace("\"","\\\"",$statements[$key]),$key);
		// },array_keys($statements)))."\n},\n\n",'a');
				
		return File::Write($this->path,"[time]:".date('Y-m-d H:i:s')."\n".implode("\n",array_map(function($key) use($statements) { 
			return sprintf("[%s]:%s",$key,$statements[$key]);
		},array_keys($statements)))."\n\n",'a');
		
	}
	
} 