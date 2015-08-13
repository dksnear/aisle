<?php
namespace aisle\web\grab;

abstract class CurlThread extends \Thread {
	
	protected $curl;
	
	public function __construct(){
		
		$this->curl = new Curl();
	}
	
	public function run(){
		
		$this->Fetch();
	}
	
	public function Fetch(){}
	
}