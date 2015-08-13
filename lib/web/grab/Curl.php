<?php
namespace aisle\web\grab;
use aisle\ex\WebException;

class Curl{
	
	public static function Get($url,$args=null,$opts=null,$trace_agent=false){
		
		$args = empty($args) ? '' : '?'.implode('&',array_map(function($k) use ($args){ return $k.'='.$args[$k]; },array_keys($args)));
		
		$url = $args ? $url.$args : $url;
		
		$opts = $opts ? $opts : array();
		
		$curl = new self();
		
		$trace_agent &&	$curl->Opts(array(
		
			CURLOPT_PROXY => '127.0.0.1:8888', //设置代理服务器 
			CURLOPT_SSL_VERIFYPEER =>0 //若PHP编译时不带openssl则需要此行
		));
	
		return $curl->Opts(array(
		
			CURLOPT_URL => $url,
			CURLOPT_HEADER => 0,
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1
		))
		->Opts($opts)
		->Exec()
		->Close()
		->Result();
	}
	
	public static function Post($url,$args=null,$opts=null,$trace_agent=false){
		
		$args = $args ? $args : array();
		$opts = $opts ? $opts : array();
		
		$curl = new self();
		
		$trace_agent &&	$curl->Opts(array(
		
			CURLOPT_PROXY => '127.0.0.1:8888', //设置代理服务器 
			CURLOPT_SSL_VERIFYPEER =>0 //若PHP编译时不带openssl则需要此行
		));
		
		return $curl->Opts(array(
			
			CURLOPT_URL => $url,
			CURLOPT_HEADER => 0,
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $args
		))
		->Opts($opts)
		->Exec()
		->Close()
		->Result();
	}
	
	protected $ch;
	
	protected $results = array();
	
	protected $callableMethodMap = array(
		
		'Close'=>'curl_close',
		'Exec'=>'curl_exec',
		'Opt'=>'curl_setopt',
		'Opts'=>'curl_setopt_array',
		'Reset'=>'curl_reset'
		
	);
	
	public function __construct(){
		
		$this->ch = curl_init();
	}
	
	public function __call($method,$args){
		
		if(!isset($this->callableMethodMap[$method])){
			throw new WebException('method "'.$method.'" not exists!');
		}
		
		array_unshift($args,$this->ch);
		
		$result = call_user_func_array($this->callableMethodMap[$method],$args);
		
		if($method == 'Exec')
			$this->results []= $result;
		
		return $this;
	}
		
	public function Results($idx=null){
		
		if(empty($this->results))
			return null;
		
		return is_null($idx) ? $this->results :$this->results[$idx];
	}
	
	public function Result(){
		
		return $this->Results(0);
	}

	
}