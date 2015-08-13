<?php
namespace aisle\cache;

use \aisle\core\XType;
use \aisle\core\File;
use \aisle\ex\CacheException;

class FileCache implements ICache{
		
	protected $path = '';
			
	protected $content;
	
	// expire=0 永远不过期
	protected $expire = 0;
	
	protected $dir = './$source/cache';
	
	protected $name = 'AISLE_SYS_FILE_CACHE';
	
	public function Connect($options = array()){
		
		foreach($options as $key=>$value){
			
			if(property_exists($this,$key))
				$this->$key = $value;
		}
		
		//clearstatcache();
					
		$this->path = implode('/',array($this->dir,$this->name));
		
		if(!file_exists($this->path) && File::Write($this->path,'') === false)
			throw new CacheException(sprintf('cache storage file "%s" can not write!',$this->path));
		
		$this->content = File::Read($this->path);
		
		if($this->content === false)
			throw new CacheException(sprintf('cache storage file "%s" can not read!',$this->path));
		
		if(!$this->content){
			
			$this->content = array();
			return true;
		}
					
		// 缓存文件过期
		if($this->expire !== 0 && time() > (filemtime($this->path) + $this->expire))
			$this->Clear();
		else $this->content = XType::Build($this->content)->Unescape()->Unserialize()->Meta();
			
		return true;
	}
	
	public function Get($key){
								
		return isset($this->content[$key])? $this->content[$key] : null;
	
	}
	
	public function Set($key,$value){
		
		$this->content[$key] = $value;
	
		return $this->write();		
	
	}
	
	public function Remove($key){
	
		unset($this->content[$key]);

		return $this->write();
				
	}
			
	public function Clear(){
	
		$this->content = array();
		
		return $this->write();

	}
	
	protected function write(){
		
		return File::Write($this->path,XType::Build($this->content)->Serialize()->Escape()->Meta());
	}
	
}
