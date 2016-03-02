<?php
namespace aisle\proj\doki8\grab;

require_once(dirname(dirname(dirname(__DIR__))).'/core/File.php');

use aisle\core\File;
use aisle\web\grab\CurlThread;

class PointHistory extends CurlThread {
		
	public static function Write($dbClient,$userName,$pageCount,$lastPageRecordCount,$ignoreVerify=false){
		
		$args = $ignoreVerify ? false : self::GetGrabArgs($userName);
		$root = self::$rootPath.'/'.$userName;
		
		if($args && $args[1] != $lastPageRecordCount){
			
			throw new \Exception(sprintf('grab user "%s" point history failed!',$userName));
		}
		
		$files = array();
		
		File::Each($root,1,function($file) use($dbClient,&$files){
			
			if(!preg_match('/^(.*)\.sql$/',$file->name,$match))
				 return;
			
			foreach(array_slice(file($file->fullName),0) as $sql)
				$dbClient->DirectTrans($sql);
				
			$files []= $file->fullName;
			$files []= dirname($file->fullName).'/'.$match[1].'.html';
			
		});
		
		$dbClient
			->DirectTrans(sprintf(
				"update `profile` set `last_grab_page_count` = '%s', `last_update_time` = '%s', `status` = 1, ".
				"`points`= (select sum(`point`) from `point_history` where `user_name` = '%s'), ".
				"`regist_time` = (select `record_time` from `point_history` where user_name = '%s' order by `record_num` asc limit 0,1), ".
				"`last_visit_time` = (select `record_time` from `point_history` where user_name = '%s' order by `record_num` desc limit 0,1) ".
				"where `name` = '%s' ",
				$pageCount,date('Y-m-d H:i:s',time()),$userName,$userName,$userName,$userName))
			->Run();
				
		$files [] = $root;
		//$files [] = self::$rootPath;
		foreach($files as $file)
			File::Delete($file);
			
		return true;
		
	}
	
	public static function GetGrabArgs($userName){
		
		$grab = new self($userName);
		
		return array($grab->GetPageCount(),$grab->GetLastPageRecordCount());
		
	}
	
	protected static $rootPath = './$source/grab/pointhistory';

	protected $rawPath = '/%s/%s.html';
	
	protected $sqlPath = '/%s/%s.sql';
		
	protected $userName;
	
	protected $pageIndex;
	
	protected $pageCount;
	
	protected $lastPageRecordCount;
		
	public function __construct($userName,$pageIndex=1,$pageCount=0,$lastPageRecordCount=0){
		
		parent::__construct();
		
		$this->userName = $userName;
		$this->pageIndex = $pageIndex;
		$this->rawPath = sprintf(self::$rootPath.$this->rawPath,$userName,$pageIndex);
		$this->sqlPath = sprintf(self::$rootPath.$this->sqlPath,$userName,$pageIndex);
		$this->pageCount = $pageCount;
		$this->pageCount = $this->GetPageCount();
		$this->lastPageRecordCount = $this->GetLastPageRecordCount();

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
		
		preg_match_all('/<td class="column-entry"><div class="mycred-mobile-log" style="display:none;">(.*?)<div>(-?\d+).*?<\/div><\/div>(?:<a href="http:\/\/www\.doki8\.com\/(\d+)\.html">.*?<\/a>)?(?:<a href=".*?">.*?<\/a>)*(.*?)<\/td>/s',$source,$matches);
		
		$recordCount = count($matches[0]);
				
		for($i=0;$i<$recordCount;$i++){
		
			preg_match('/(\d+)年(\d+)月(\d+)日\s(\d+):(\d+)/',$matches[1][$i],$time);	
			$time = date('Y-m-d H:i:s',mktime($time[4],$time[5],0,$time[2],$time[3],$time[1]));
			$fullPages = $this->pageCount - $this->pageIndex - 1;
			$recordNum = ($fullPages < 0 ? 0 : $fullPages) * 50 + ($this->pageCount == $this->pageIndex ? 0 : $this->lastPageRecordCount) + $recordCount - $i;
					
			File::Write($this->sqlPath,sprintf("insert into `point_history` (`user_name`, `record_num`, `article_id`, `point`, `desc`, `record_time`, `last_update_time`) select '%s', '%s', '%s', '%s', '%s', '%s', '%s' from dual where not exists ( select 1 from `point_history` where `record_num` = '%s' and `user_name` = '%s' ) \n",
				$this->userName, $recordNum, $matches[3][$i] ? $matches[3][$i] : 0, $matches[2][$i], $matches[4][$i], $time, date('Y-m-d H:i:s',time()), $recordNum, $this->userName),'a');
			
		}
				
	}
	
	public function GetPageCount(){
		
		if($this->pageCount)
			return  $this->pageCount;
		
		$source = $this->curl->Get($this->getUrl($this->userName,$this->pageIndex));
		
		preg_match('/<span class=["\']total-pages["\']>(\d+)<\/span>/s',$source,$match);	
			$this->pageCount = $match[1];
		
		return $this->pageCount;
	}
	
	public function GetLastPageRecordCount(){
		
		if($this->lastPageRecordCount)
			return $this->lastPageRecordCount;
		
		$source = $this->curl->Get($this->getUrl($this->userName,$this->pageCount));
		
		preg_match('/<span class="displaying-num">.*?(\d+).*?<\/span>/s',$source,$match);	
			
		$this->lastPageRecordCount = $match[1];
		
		return $this->lastPageRecordCount;
		
	}
	
	protected function getUrl($userName,$pageIndex){
		
		return sprintf('http://www.doki8.com/members/%s/pointhistory/?paged=%s',$userName,$pageIndex);
	}
	
}