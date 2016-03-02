<?php
namespace aisle\_accurate\services\common{

	// \aisle\common\_config_get_class('accurate-sql-client','sql-client');

	use \aisle\common as common;
	use \aisle\File as File;
	// use \aisle\_accurate\sqlClient\MysqlClient as SqlClient;

	class Extra extends \aisle\_accurate\Service{
		
		protected $db_require = true;
				
		public function opcacheinfo(){
		
			function_exists('opcache_get_status') && common\_var_dump(opcache_get_status(true));
		
		}
		
		public function remoteimage(){
		
	
			$pic_url_set = array();
				
			for($i=0;$i<20;$i++){
			
				$num = $i + 1;
				
				$num = $num > 9 ? $num : '0'.$num;
			
				$pic_url_set []= 'http://pic.920mm.com/Upload2010/Z29/zsmqygqy/'.$num.'.jpg';
			
			}
			
			
			foreach($pic_url_set as $index=>$pic_url){
			
				$pic = file_get_contents($pic_url);
				$dir = './accurate/files/download/images/';
				if(!is_dir($dir))
					mkdir($dir,0777,true);
				
				//preg_match('/\.+(.jpg)$/',$pic_url,$matches);
				
				//$name = $matches[1];
				
				$name = ($index+1).'.jpg';
				
				$file = fopen($dir.$name,'w');
				fwrite($file,$pic);
				fclose($file);
				
			}
			
			return true;
		
		}
		
		public function get_links(){
		
			$data = json_decode(File::read('./accurate/files/Bookmarks'),true);
			$data = common\_array_get($data,'roots');
			
			$this->generate_link_sql($data,$sqls);
			
			foreach($sqls as $sql)
				common\_echo($sql.'<br/>');
		
		}
		
		protected function generate_link_sql($data,& $sqls=null){
		
			if(empty($data)) return;
		
			$sqls = empty($sqls) ? array() : $sqls;
			
			foreach($data as $row){
			
				if(common\_array_get($row,'type')=='folder')
					$this->generate_link_sql(common\_array_get($row,'children'),$sqls);
				else
					$sqls []= sprintf('insert into `TB_LINK_POOL` (`id`,`name`,`category`,`url`) values(\'%s\',\'%s\',\'%s\',\'%s\');',
						common\_guid(),str_replace('\'','\\\'',common\_or(common\_array_get($row,'name'),'')),mt_rand(1,5),common\_array_get($row,'url'));
			
			}
		
		}
		
		public function candidate_captcha(){
					
			$cache_tag_name = 'candidate-captcha-cache-tag';
			$cache_tag = common\_array_get($_GET,$cache_tag_name);
	
			if(!empty($cache_tag) && common\_array_get($_SERVER,'HTTP_IF_NONE_MATCH') == $cache_tag){
			
				common\_set_header('HTTP/1.1 304 Not Modified');
				exit();
			
			}
			
			common\_session_start('public');
			
			!empty($cache_tag) 
				? common\_set_header('Content-type: image/png','Etag:'.$cache_tag)
				: common\_set_header('Content-type: image/png');
						
			$captcha = common\_config_get_class('captcha');
			$captcha = new $captcha(array(
			
				'is_candidate'=>true,
				'is_zh'=>true
			
			));
			
			
			common\_session_set('candidate_captcha',$captcha->get_code());
				
			
			$captcha->create_image();	
			
		}
			
		public function validate_candidate_captcha(){
		
			$code = common\_array_get($_GET,'candidate_captcha');
			
			return !empty($code) && ($code == common\_session_get('candidate_captcha','code')) && time() < common\_session_get('candidate_captcha','expire');
		
		}
		
		public function test(){
			
			$vac = common\_config_get_class('userVA','attribute');
			$hac = common\_config_get_class('cacheHA','attribute');
			
			$a = array(new $vac(),new $hac(),new $hac(),new $vac());
			
			usort($a,function($left,$right){
				
				if($left->after_method_attrs_affect == $right->after_method_attrs_affect) return 0;
				
				if(!$right->after_method_attrs_affect) return 1;
				
				return -1;
				
			});
			
			return array_map(function($item){ return get_class($item); },$a);
			
		}
		
	}

}