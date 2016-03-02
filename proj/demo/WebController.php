<?php
namespace aisle\proj\demo;

use aisle\core\File;
use aisle\web\Controller;
use aisle\web\grab\Curl;

class WebController extends Controller{
	
	public function Test($name,$___attributes=array(array('test'))){
		
		return $name.' test!';
	}
	
	public function RedirectTo($url=null){
				
		return $this->redirect('test.html?t=1');	
	}
	
	public function TestPage(){
		
		return $this->page('test');
	}
	
	public function Grab1(){
	
		File::Write('grab.html',Curl::Get('http://www.baidu.com'));
	}
	
	public function Grab2(){
		
		$curl = new Curl();
		$source = $curl
			->Opts(array(
				CURLOPT_URL => 'http://www.doki8.com/wp-admin/admin-ajax.php',
				CURLOPT_POST => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_POSTFIELDS => array(
	
					'action'=>'members_filter',
					'cookie'=>'bp-activity-oldestpage%3D1%26bp-members-scope%3Dall%26bp-members-filter%3Dalphabetical',
					'object'=>'members',
					'filter'=>'alphabetical',
					'search_terms'=>'',
					'scope'=>'all',
					'page'=>'3',
					'template'=>''
				),
				CURLOPT_COOKIEJAR => './proj/doki8/$source/data/userlist/cookies.txt',
				CURLOPT_HEADER => 0
				
			))->Exec()->Close()->Result();		
			
		File::Write('./proj/doki8/$source/grab/userlist/2.html',$source);
		
	}
	
	public function Grab3(){
		
		$source = Curl::Post('http://www.doki8.com/wp-admin/admin-ajax.php',array(
	
			'action'=>'members_filter',
			'cookie'=>'bp-activity-oldestpage%3D1%26bp-members-scope%3Dall%26bp-members-filter%3Dalphabetical',
			'object'=>'members',
			'filter'=>'alphabetical',
			'search_terms'=>'',
			'scope'=>'all',
			'page'=>'1',
			'template'=>''
			
		),array( CURLOPT_COOKIEJAR => './proj/doki8/$source/data/userlist/cookies.txt' ));
		
		File::Write('./proj/doki8/$source/grab/userlist/2.html',$source);
	}
	
}