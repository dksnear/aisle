<?php
namespace aisle\cache;

interface ICache {
	
	public function Connect($options);
	
	public function Get($key);

	public function Set($key, $value);
	
	public function Remove($key);
		
	public function Clear();
}

