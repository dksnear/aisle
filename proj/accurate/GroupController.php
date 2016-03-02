<?php
namespace aisle\_accurate\services\data{

	use \aisle\common as common;
	use \aisle\_accurate\sqlClient\MysqlClient as SqlClient;
	use \aisle\_accurate\Exception as Exception;
	use \aisle\_accurate\Exception\VerifyException as VerifyException;

	class Group extends \aisle\_accurate\Service{
			
		protected $db_require = true;
		
		public function get($group_id=null,$user_id=null){
			
			$user_id = common\_or($user_id,common\_session_get('user_info','id'));
			
			if(empty($group_id))
				return array(		
					array(
					
						'id'=>'group-root',
						'pId'=>'0',
						'text'=>'分组列表',
						'hasChild'=>true,
						'status'=>1
					)
				);
			
			 return $group_id === 'group-root'
				? $this->db_client->query('select `id`,\'group-root\' as `pId`,`name` as `text`,`has_link` as `hasChild`,1 as `branch`,`status` as `status` from `TB_LINK_GROUP` where `user_id`=:user_id order by `sys_create_time`',array('user_id'=>$user_id))
				: $this->db_client->query('select uuid() as `id`,`link`.`id` as `linkId`,`group`.`group_id` as `pId`,`link`.`name` as `text` ,1 as `leaf`,0 as `status` from 
					(select `group_id`,`link_id` from `TB_LINK_REL_LINK_GROUP` where `group_id`=:group_id) as `group` left join
					(select `id`,`name` from `TB_LINK_POOL` ) as `link` on `group`.`link_id`=`link`.`id`',array('group_id'=>$group_id));
		
		}
		
		public function update($data,$user_id=null,$___attributes=array(
				'promptRA'=>array('msg'=>'修改成功!')
			)
		){
		
			$user_id = common\_or($user_id,common\_session_get('user_info','id'));
		
			$data = json_decode($data,true);
			
			$trans = array();
				
			foreach($data as $row){
			
				$params = array('sys_last_update_time'=>date('Y-m-d H:i:s'));
			
				if((int)$row['$$depth'] == 3){
				
					if(isset($row['$add'])){
					
						$params['id'] = $row['id'];
						$params['user_id'] = $user_id;
						$params['name'] = $row['text'];
					
						$trans []= array(
						
							'sql'=>SqlClient::generate_insert('TB_LINK_GROUP',$params),
							'params'=>$params
						
						);
					}
					
					if(isset($row['$update'])){
					
						$params['id'] = $row['id'];
						$params['name'] = $row['text'];
					
						$trans []= array(
						
							'sql'=>SqlClient::generate_update('TB_LINK_GROUP',array('name','sys_last_update_time'),'`id`=:id'),
							'params'=>$params
						
						);
					}
					
					if(isset($row['$delete'])){
					
						$params['group_id'] = $row['id'];
						unset($params['sys_last_update_time']);
					
						$trans []= array(
						
							'sql'=>SqlClient::generate_delete('TB_LINK_GROUP','`id`=:group_id and `status`&1 != 1'),
							'params'=>$params
						
						);
						
						$trans [] = array(
						
							'sql'=> SqlClient::generate_delete('TB_LINK_REL_LINK_GROUP','`group_id`=:group_id'),
							'params'=>$params
						
						);
					
					}
						
				
				}
			
				if((int)$row['$$depth'] == 4){
				
					if(isset($row['$add'])){
										
						$params['group_id'] = $row['pId'];
						$params['has_link'] = 1;
						$trans []= array(
							
							'sql'=>SqlClient::generate_update('TB_LINK_GROUP',array('has_link','sys_last_update_time'),'`id`=:group_id'),
							'params'=>$params
						
						);
						
						unset($params['has_link']);
						$params['link_id'] = $row['linkId'];
					
						$trans []= array(
						
							'sql'=>SqlClient::generate_insert('TB_LINK_REL_LINK_GROUP',$params),
							'params'=>$params
						
						);
						
					}
					
					if(isset($row['$delete'])){
					
						$params['link_id'] = $row['linkId'];
						$params['group_id'] = $row['pId'];
						unset($params['sys_last_update_time']);
						
						$trans [] = array(
						
							'sql'=> SqlClient::generate_delete('TB_LINK_REL_LINK_GROUP','`link_id`=:link_id and `group_id`=:group_id'),
							'params'=>$params
						
						);
						

						$params['sys_last_update_time'] = date('Y-m-d H:i:s');
						$params['g_group_id'] = $row['pId'];
						$params['l_group_id'] = $row['pId'];
						$params['has_link'] = 0;		
						unset($params['group_id']);
						unset($params['link_id']);
						
						$trans [] = array(
						
							'sql'=>SqlClient::generate_update('TB_LINK_GROUP',array('has_link','sys_last_update_time'),'`id` = :g_group_id and not exists (select 1 from `TB_LINK_REL_LINK_GROUP` where `group_id`=:l_group_id)'),
							'params'=>$params
						
						);
					}
				
				
				}
			
			}
			
			$this->db_client->trans($trans);
			
			return true;
				
		}
		
	}
	
}