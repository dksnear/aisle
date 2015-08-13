<?php
namespace aisle\view;

// php5.3 不支持JsonSerializable
class Message implements \JsonSerializable{
	
	protected $success;
	
	protected $type;
	
	protected $code;
	
	protected $data;
	
	public function __construct($data,$success=true,$type='DATA',$code=1){
		
		$this->success = $success;
		$this->type = $type;
		$this->code = $code;
		$this->data = $data;
	}
		
	// implements JsonSerializable
    public function jsonSerialize(){
		
		return array(
	
			'success' => $this->success,
			'type' => $this->type,
			'code' => $this->code,
			'data' => $this->data	
		);	
	}
	
}