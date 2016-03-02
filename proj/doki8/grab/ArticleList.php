<?php
namespace aisle\proj\doki8\grab;

require_once(dirname(dirname(dirname(__DIR__))).'/core/File.php');

use aisle\core\File;
use aisle\web\grab\CurlThread;

class ArticleList extends CurlThread {
		
	public static function Write($dbClient,$category){
		
		$root = self::$rootPath.'/'.$category;
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
	
	public static function ClearCache($category,$type='sql'){
		
		File::Each(self::$rootPath.'/'.$category,1,function($file) use($type){
			
			if($type == 'all')
				return $file->DeleteFile();
			
			if($type == 'sql' && preg_match('/^(.*)\.sql$/',$file->name))
				return $file->DeleteFile();
			
			if($type == 'html' && preg_match('/^(.*)\.html$/',$file->name,$match))
				File::Delete(dirname($file->fullName).'/'.$match[1].'.html');
						
		});
	}
	
	protected static $rootPath = './$source/grab/articlelist';

	protected $rawPath = '/%s/%s.html';
	
	protected $sqlPath = '/%s/%s.sql';
	
	protected $url = '';
	
	protected $category;
	
	protected $pageIndex;
		
	public function __construct($category,$pageIndex=1){
		
		parent::__construct();
		
		$this->rawPath = sprintf(self::$rootPath.$this->rawPath,$category,$pageIndex);
		$this->sqlPath = sprintf(self::$rootPath.$this->sqlPath,$category,$pageIndex);
		$this->url = $this->getUrl($category,$pageIndex);
		$this->category = $category;
		$this->pageIndex = $pageIndex;
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
		
		$reg = 
			'<article.*?>.*?<h2 class="post-box-title">.*?<a href="http:\/\/www.doki8.com\/(\d+)\.html">(.*?)<\/a>.*?<\/h2>.*?<p class="post-meta">.*?'.
			'<span class="post-meta-author"><i class="fa fa-user"><\/i><a href="http:\/\/www.doki8.com\/author\/(.*?)" title="">.*?<\/a><\/span>.*?'.
			'<span class="tie-date"><i class="fa fa-clock-o"><\/i>(.*?)<\/span>.*?'.
			'<span class="post-cats"><i class="fa fa-folder"><\/i>(.*?)<\/span>.*?'.
			'<span class="post-comments"><i class="fa fa-comments"><\/i><a href=".*?">\s*(.+?)\s*<\/a><\/span>.*?'.
			'<span class="post-views"><i class="fa fa-eye"><\/i>\s*(.+?)\s*<\/span>.*?'.
			'<\/p>.*?<\/article>';
		
		preg_match_all('/'.$reg.'/s',$source,$matches);
		
		for($i=0;$i<count($matches[0]);$i++){
			
			preg_match('/(\d+)年(\d+)月(\d+)日/',$matches[4][$i],$date);	
			preg_match_all('/<a href="http:\/\/www.doki8.com\/(.*?)" rel="category tag">(.*?)<\/a>/',$matches[5][$i],$categorys);
					
			$category_tags = array_reduce($categorys[1],function($cur,$name){
					
				$map = array(
	
					'drama' => 1, // 日剧
					'online' => 2, // 在线日剧
					'taiga' => 4, // 大河剧
					'jidaigeki' => 8, // 时代剧
					'movie' => 16, // 电影
					'music' => 32, // 音乐
					'shicho' => 64, // 收视率
					'master' => 128, // 站长
					'contributor' => 256, // 投稿
					'story' => 512, // 剧情
					'love' => 1024, // 爱情
					'medical' => 2048, // 医疗
					'suspense' => 4096, // 悬疑
					'criminal' => 8192, // 罪案
					'school' => 16384, // 校园
					'horror' => 32768, // 恐怖
					'documentary' => 65536, // 记录			
					'variety' => 131072, // 综艺
					'now'=> 262144,
					'sp'=> 524288,
					'p'=> 1048576,
					'rare' => 2097152
				);
				
				return $cur | (isset($map[$name]) ? $map[$name] : 0);
				
			},0);	
			
			$category_desc = implode(',',$categorys[2]);
		
			$date = date('Y-m-d H:i:s',mktime(0,0,0,$date[2],$date[3],$date[1]));
			$comments = preg_replace('/,/','',$matches[6][$i]);
			$views = preg_replace('/,/','',$matches[7][$i]);
			
			$comments = $comments ? $comments : 0;
			$views = $views ? $views : 0;
			
			File::Write($this->sqlPath, sprintf("update `article` set `title`='%s',`category_tags`='%s',`category_desc`='%s',`comment_count`='%s',`view_count`='%s',`publish_time`='%s',`last_update_time`='%s' where `id`='%s' \n",
				$matches[2][$i],$category_tags,$category_desc,$comments,$views,$date,date('Y-m-d H:i:s',time()),$matches[1][$i]),'a');
			
			File::Write($this->sqlPath, sprintf("insert into `article` (`id`,`author`,`title`,`category_tags`,`category_desc`,`comment_count`,`view_count`,`publish_time`,`last_update_time`) select '%s','%s','%s','%s','%s','%s','%s','%s','%s' from dual where not exists ( select 1 from `article` where `id` = '%s') \n",
				$matches[1][$i],$matches[3][$i],$matches[2][$i],$category_tags,$category_desc,$comments,$views,$date,date('Y-m-d H:i:s',time()),$matches[1][$i]),'a');
				
		}
				
	}
	
	public function GetPageCount(){
		
		$source = $this->curl->Get($this->url);
		
		preg_match('/<span class="pages">\d+\/(\d+)页<\/span>/s',$source,$match);	
		
		return isset($match[1]) ? $match[1] : 1;
	}
	
	protected function getUrl($category,$pageIndex){
		
		if($pageIndex == 1)
			return sprintf('http://www.doki8.com/%s',$category);
		
		return sprintf('http://www.doki8.com/%s/page/%s',$category,$pageIndex);
	}

}