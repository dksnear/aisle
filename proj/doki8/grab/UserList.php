<?php
namespace aisle\proj\doki8\grab;

require_once(dirname(dirname(dirname(__DIR__))).'/core/File.php');

use aisle\core\File;
use aisle\web\grab\CurlThread;

class UserList extends CurlThread {
		
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
		
	}
	
	protected static $rootPath = './$source/grab/userlist';

	protected $rawPath = '/%s.html';
	
	protected $sqlPath = '/%s.sql';
	
	protected $url = 'http://www.doki8.com/wp-admin/admin-ajax.php';
	
	protected $pageIndex;
		
	public function __construct($pageIndex=1){
		
		parent::__construct();
		
		$this->pageIndex = $pageIndex;
		$this->rawPath = sprintf(self::$rootPath.$this->rawPath,$pageIndex);
		$this->sqlPath = sprintf(self::$rootPath.$this->sqlPath,$pageIndex);

	}
	
	public function Fetch(){
		
		if(file_exists($this->rawPath)){
			
			$source = File::Read($this->rawPath);
			
		} else{
			
			$source = $this->curl->Post($this->url,array(
	
				'action'=>'members_filter',
				'cookie'=>'bp-activity-oldestpage%3D1%26bp-members-scope%3Dall%26bp-members-filter%3Dalphabetical',
				'object'=>'members',
				'filter'=>'alphabetical',
				'search_terms'=>'',
				'scope'=>'all',
				'page'=>$this->pageIndex,
				'template'=>''
			));
			

			File::Write($this->rawPath,$source);

		}
		
		if(file_exists($this->sqlPath))
			return;
		
		File::Write($this->sqlPath,'');
		
		preg_match_all('/<div class="item-title">.*?<a href="http:\/\/www.doki8.com\/members\/(?:([^"\/]*))\/?">(.*?)<\/a>.*?<\/div>/s',$source,$matches);
		
		for($i=0;$i<count($matches[0]);$i++){
			
			if(!$matches[1][$i])
				continue;
				
			File::Write($this->sqlPath,sprintf("insert into `profile` (`name`, `nick_name`, `last_update_time`) select '%s', '%s', '%s' from dual where not exists ( select 1 from `profile` where `name` = '%s') \n",
				$matches[1][$i], $matches[2][$i] ,date('Y-m-d H:i:s',time()), $matches[1][$i]),'a');
			// File::Write($this->sqlPath,sprintf("insert into `profile` (`name`, `nick_name`, `last_update_time`) values('%s', '%s', '%s') \n",
				// $matches[1][$i], $matches[2][$i] ,date('Y-m-d H:i:s',time())),'a');
			
		}
		
	}
		
}