<?php
namespace aisle\proj\doki8\grab;

require_once(dirname(dirname(dirname(__DIR__))).'/core/File.php');

use aisle\core\File;
use aisle\web\grab\CurlThread;

class AuthorList extends CurlThread {
		
	public static function Write($dbClient,$userName){
		
		$root = self::$rootPath.'/'.$userName;
		$files = array();
		
		File::Each($root,1,function($file) use($dbClient,&$files){
			
			if(!preg_match('/^(.*)\.sql$/',$file->name,$match))
				 return;
			
			foreach(array_slice(file($file->fullName),0) as $sql)
				$dbClient->DirectTrans($sql);
				
			$files []= $file->fullName;
			$files []= dirname($file->fullName).'/'.$match[1].'.html';
			
		});
		
		$dbClient->Run();
			
		$files []= $root;
		foreach($files as $file)
			File::Delete($file);
			
		return true;
		
	}
	
	public static function ClearCache($userName,$type='sql'){
		
		File::Each(self::$rootPath.'/'.$userName,1,function($file) use($type){
			
			if($type == 'all')
				return $file->DeleteFile();
			
			if($type == 'sql' && preg_match('/^(.*)\.sql$/',$file->name))
				return $file->DeleteFile();
			
			if($type == 'html' && preg_match('/^(.*)\.html$/',$file->name,$match))
				File::Delete(dirname($file->fullName).'/'.$match[1].'.html');
						
		});
	}
	
	public static function Get($dbClient,$userName){
		
		return $dbClient->DirectQuery(sprintf("select * from `article` where `author` = '%s'",$userName))->Run()->Meta();
	}
	
	protected static $rootPath = './$source/grab/authorlist';

	protected $rawPath = '/%s/%s.html';
	
	protected $sqlPath = '/%s/%s.sql';
	
	protected $userName;
	
	protected $pageIndex;
		
	public function __construct($userName,$pageIndex=1){
		
		parent::__construct();
		
		$this->userName = $userName;
		$this->pageIndex = $pageIndex;
		$this->rawPath = sprintf(self::$rootPath.$this->rawPath,$userName,$pageIndex);
		$this->sqlPath = sprintf(self::$rootPath.$this->sqlPath,$userName,$pageIndex);

	}
	
	public function Fetch(){
		
		if(file_exists($this->rawPath)){
			
			$source = File::Read($this->rawPath);
			
		} else{
			
			$source = $this->curl->Get($this->getUrl($this->userName,$this->pageIndex));
			File::Write($this->rawPath,$source);
		}
		
		if(file_exists($this->sqlPath))
			return;
		
		File::Write($this->sqlPath,'');
		
		preg_match_all('/<h2 class="post-box-title">.*?<a href="http:\/\/www.doki8.com\/(.*?).html">.*?<\/a>.*?<\/h2>/s',$source,$matches);
		
		foreach($matches[1] as $articleId){
			
			File::Write($this->sqlPath, sprintf("insert into `article` (`id`,`author`,`last_update_time`) select '%s','%s','%s' from dual where not exists ( select 1 from `article` where `id` = '%s') \n",
				$articleId,$this->userName,date('Y-m-d H:i:s',time()),$articleId),'a');		
		}
						
	}
	
	public function GetPageCount(){
		
		$source = $this->curl->Get($this->getUrl($this->userName,$this->pageIndex));
		
		preg_match('/<span class="pages">\d+\/(\d+)页<\/span>/s',$source,$match);	
		
		return isset($match[1]) ? $match[1] : 1;
	}
	
	protected function getUrl($userName,$pageIndex){
		
		if($pageIndex == 1)
			return sprintf('http://www.doki8.com/author/%s',$userName);
		
		return sprintf('http://www.doki8.com/author/%s/page/%s',$userName,$pageIndex);
	}
		
}