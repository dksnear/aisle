<?php
namespace aisle\conf;

interface IExtensionConfig{
		
	public function Load($statements,$appConfig);
	
	public function Get($key);
	
}

