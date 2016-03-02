<?php
namespace aisle\proj\accurate\web;
use aisle\web\Controller;
use aisle\proj\accurate\view\Message;

class Controller extends Controller{

	protected function Msg(){
		
		return new Message();
	}

}
