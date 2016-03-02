<?php
namespace aisle\_accurate\services\data{

	use \aisle\common as common;
	use \aisle\_accurate\sqlClient\MysqlClient as SqlClient;
	use \aisle\_accurate\Exception as Exception;
	use \aisle\_accurate\Exception\VerifyException as VerifyException;

	class Link extends \aisle\_accurate\Service{
			
		protected $db_require = true;
		
		public function add(){
		
		
		}
		
		public function get($category,$order,$start,$limit){
		
			return $this->db_client->query(SqlClient::generate_query('TB_LINK_POOL',array(
			
				'id'=>'link_id',
				'name'=>'link_name',
				'url'=>'link_url'
			
			),'`category`=:category',$order,$start,$limit),array(
			
				'category'=>$category
			
			));
			
		}
		
		// 读取链接池各分类连接数量
		public function get_category_list(){
						
			return $this->db_client->query('select `dic`.`key` as `category`,`dic`.`value` as `name`, count(`link`.`category`) as `count` from 
				( select `key`,`value`,`order` from `TB_DICTIONARY` where `name`=\'link_category\') as `dic` left join 
				( select `category` from `TB_LINK_POOL` ) as `link` on `dic`.`key`=`link`.`category`  
				group by `dic`.`key` order by `dic`.`order` asc;');
			
		}
		
		// 读取连接池所有分类
		public function category(){
		
			return $this->db_client->get_dictionary('link_category',array(
			
				'key'=>'key',
				'value'=>'value'
			
			));
		
		}
		
	}
	
}