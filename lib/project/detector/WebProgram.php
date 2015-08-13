<?php
namespace aisle\project\detector;
require('../../core/Program.php');

use aisle\core\Program;
use aisle\conf\ConfigManager;

final class WebProgram extends Program{
	
	// 扫描器根目录
	protected $scanRoot = '../..';
	
	protected function createConfigManager(){
		
		return new ConfigManager(
			$this->scanRoot.'/$source/config.acj',
			array('./$source/config-cover.acj',true)
		);		
	}
}

Program::Build('aisle\project\detector\WebProgram');

