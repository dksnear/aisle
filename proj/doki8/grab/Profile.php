<?php
namespace aisle\proj\doki8\grab;

require_once(dirname(dirname(dirname(__DIR__))).'/core/File.php');

use aisle\core\File;
use aisle\web\grab\CurlThread;

class Profile extends CurlThread {
		
	public static function Write($dbClient){
		
		$root = self::$rootPath;
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
			
		// $files []= $root;
		foreach($files as $file)
			File::Delete($file);
			
		return true;
		
	}
	
	public static function ClearCache($type='sql'){
		
		File::Each(self::$rootPath,1,function($file) use($type){
			
			if($type == 'all')
				return $file->DeleteFile();
			
			if($type == 'sql' && preg_match('/^(.*)\.sql$/',$file->name))
				return $file->DeleteFile();
			
			if($type == 'html' && preg_match('/^(.*)\.html$/',$file->name,$match))
				File::Delete(dirname($file->fullName).'/'.$match[1].'.html');
						
		});
	}
	
	public static function Get($dbClient,$userName){
		
		return $dbClient->DirectQuery(sprintf("select * from `profile` where `name` = '%s'",$userName))->Run()->Meta();
	}
	
	protected static $rootPath = './$source/grab/profile';

	protected $rawPath = '/%s.html';
	
	protected $sqlPath = '/%s.sql';
	
	protected $url = 'http://www.doki8.com/members/%s/';
	
	protected $userName;
		
	public function __construct($userName){
		
		parent::__construct();
		
		$this->userName = $userName;
		$this->rawPath = sprintf(self::$rootPath.$this->rawPath,$userName);
		$this->sqlPath = sprintf(self::$rootPath.$this->sqlPath,$userName);
		$this->url = sprintf($this->url,$userName);

	}
	
	public function Fetch(){
		
		if(file_exists($this->rawPath)){
			
			$source = File::Read($this->rawPath);
			
		} else{
			
			$source = $this->curl->Get($this->url);
			File::Write($this->rawPath,$source);
		}
		
		if(file_exists($this->sqlPath))
			return;
		
		File::Write($this->sqlPath,'');
		
		preg_match_all('/http:\/\/www.doki8.com\/wp-content\/uploads\/2015\/06\/(.*?)\.gif/s',$source,$badges);	
		preg_match('/http:\/\/www.doki8.com\/wp-content\/uploads\/2015\/01\/level(\d+)\.gif/s',$source,$rank);
		preg_match('/<title>(.*?)\s\|.*?<\/title>/s',$source,$nickName);
		$badges = isset($badges[1]) ? implode(',',$badges[1]) : '';
		$rank = isset($rank[1]) ? $rank[1] : 0;
		$nickName = $nickName[1];
		
		File::Write($this->sqlPath,sprintf("update `profile` set `nick_name`='%s',`badges`='%s',`rank`='%s',`last_update_time`='%s' where `name`='%s' \n",
			$nickName,$badges,$rank,date('Y-m-d H:i:s',time()),$this->userName),'a');
		
		File::Write($this->sqlPath,sprintf("insert into `profile` (`name`,`nick_name`,`badges`,`rank`,`last_update_time`) select '%s','%s','%s','%s','%s' from dual where not exists ( select 1 from `profile` where `name` = '%s') \n",
			$this->userName,$nickName,$badges,$rank,date('Y-m-d H:i:s',time()),$this->userName),'a');
	}
		
}