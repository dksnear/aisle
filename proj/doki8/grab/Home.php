<?php
namespace aisle\proj\doki8\grab;
use aisle\core\File;
use aisle\web\grab\Curl;

class Home{
			
	protected static $rootPath = './$source/grab/home';
	
	protected static $cookiesPath = './$source/grab/home-cookies.txt';

	protected $rawPath = '/%s.html';
		
	protected $url = 'http://www.doki8.com/';
	
	public function __construct(){
		
		$this->rawPath = sprintf(self::$rootPath.$this->rawPath,1);

	}
	
	public function FetchLeaderboard($clear=true){
		
		$source = $this->Fetch($clear);
		
		preg_match('/<ol class="myCRED-leaderboard">(.*?)<\/ol>/s',$source,$match);
		
		preg_match_all('/<li.*?>〖(\d+)〗～<a href="http:\/\/www.doki8.com\/members\/(.*?)\/">(.*?)<\/a>～(\d+)心动币<\/li>/s',$match[1],$matches);
		
		$result = array();
		
		for($i=0,$len=count($matches[0])-1;$i<$len;$i++){
			
			$result []= array(
				
				'rank'=>$matches[1][$i],
				'name'=>$matches[2][$i],
				'nick_name'=>$matches[3][$i],
				'point'=>$matches[4][$i]
			);
		}
		
		$clear && $this->Clear();
		
		return $result;
			
	}
	
	public function Fetch($clear=true){
		
		if(!$clear && file_exists($this->rawPath)){
			
			$source = File::Read($this->rawPath);
			
		} else{
			
			$source = Curl::Get($this->url,null,array( 
				CURLOPT_COOKIE => File::Read(self::cookiesPath)
			),false);
		
			File::Write($this->rawPath,$source);

		}
		
		return $source;
	}
	
	public function Clear(){
		
		return File::Delete($this->rawPath);
	}
		
}