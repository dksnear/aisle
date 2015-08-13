<?php
namespace aisle\project\demo;
require('../../core/Program.php');

use aisle\core\Program;
use aisle\conf\ConfigManager;

final class WebProgram extends Program{
	
	protected $scanRoot = '../..';
	
	protected function createConfigManager(){
	
		return new ConfigManager(
			$this->scanRoot.'/$source/config.acj',
			$this->scanRoot.'/project/demo/$source/config.acj',
			array($this->scanRoot.'/project/demo/$source/config-cover.acj',true)
		);
	}
}
	
Program::Build('aisle\project\demo\WebProgram');


