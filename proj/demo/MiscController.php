<?php
namespace aisle\proj\demo;

use aisle\core\Trace;
use aisle\core\XType;
use aisle\core\XString;
use aisle\core\XArray;
use aisle\cache\FileCache;
use aisle\web\grab\Curl;
use aisle\web\Controller;

class MiscController extends Controller{
	
	public function MHR_Search(){
		
		return Curl::Post(
			'http://api.manhuaren.com/searchapi.ashx',
			'gpakt=com.ilike.cartoon&gl=&gut=1455623937881&gav=1.0.8.8&gfcl=&gr=480x800&go=1&ga=keywordList&gcl=other&gtk=ef5a0be7a1cc4564b860d4baa27a2b05&gd=359090043979267&k=%E6%B5%B7%E8%B4%BC%E7%8E%8B&gavr=16_4.1.2&p=1&gc=CN&gui=2697601&glg=zh&gct='		
		);
		
	}
	
}