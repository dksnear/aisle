<?php
namespace aisle\log;

interface ILog{
	
	public function Connect($options);

	public function Write($statements);
	
} 