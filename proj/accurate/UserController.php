<?php
namespace aisle\_accurate\services\data{

	use \aisle\common as common;
	use \aisle\_accurate\sqlClient\MysqlClient as SqlClient;
	use \aisle\_accurate\Exception as Exception;
	use \aisle\_accurate\Exception\VerifyException as VerifyException;

	class User extends \aisle\_accurate\Service{
				
		protected $db_require = true;
		
		// for test
		public function auto_add(){
		
			$gid = common\_or(common\_session_get('gid'),1);
		
			$this->db_client->insert('TB_USER_PROFILES',array(
			
				'id'=> common\_guid(),
				'name'=> 'atest'.$gid,
				'login_name'=> 'atest'.$gid,
				'sex'=> '1',
				'birth'=> '1988-01-01',
				'pwd'=>md5(123456),
				'is_login'=>null
						
			));
			
			$gid++;
			
			common\_session_set('gid',$gid);
		
		}
		
		public function add($profiles,$___attributes=array(
				
				'captchaVA'=>array('is_form_param'=>true),
				'paramsVA'=>array('is_form_param'=>true,'replies'=>array('login_name'=>'登录名不能为空!','mail'=>'邮箱不能为空!','pwd'=>'密码不能为空!')),
				'promptRA'=>array('msg'=>'注册成功!')
			)
		){	
		
			$profiles = is_array($profiles) ? $profiles : json_decode($profiles,true);

			unset($profiles['candidate_captcha']);
			
			if(!$this->login_name_not_exists($profiles['login_name']))
				throw new VerifyException(sprintf('登录名"%s"已存在!',$profiles['login_name']));
				
			if(!$this->mail_not_exits($profiles['mail']))
				throw new VerifyException(sprintf('邮箱"%s"已注册!',$profiles['mail']));
			
			$profiles['pwd'] = md5($profiles['pwd']);
			$profiles['id'] = common\_guid();
			$profiles['sys_last_update_time'] = date('Y-m-d H:i:s');
			
			$link_group = array(
				
				'id'=>common\_guid(),
				'user_id'=>$profiles['id'],
				'name'=>'默认组',
				'status'=>'1',
				'sys_last_update_time'=>date('Y-m-d H:i:s')
			
			);
			
			$this->db_client->trans(array(
			
				array(
					
					'sql'=>SqlClient::generate_insert('TB_USER_PROFILES',$profiles),
					'params'=>$profiles
				
				),
				array(
				
					'sql'=>SqlClient::generate_insert('TB_LINK_GROUP',$link_group),
					'params'=>$link_group
				)
				
			
			));
			
			return true;
		
		}
			
		public function get($login_name){
		
			if(empty($login_name))
				return $this->db_client->query('select * from TB_USER_PROFILES order by sys_create_time desc');
			
			return $this->db_client->query('select * from TB_USER_PROFILES where `name`=:name or `login_name`=:name',array(
			
				'login_name'=>$login_name
			
			));
		}
				
		public function update($login_name,$profiles){
		
			$profiles = is_array($profiles) ? $profiles : json_decode($profiles,true);

			if(count(array_intersect_key($profiles,array('id'=>1,'login_name'=>1,'pwd'=>1,'is_delete'=>1)))>0)
				throw new Exception('存在不能修改的字段!');
			
			return $this->db_client->update('TB_USER_PROFILES',$profiles,array(
			
				'scond' => array(
					
					array(
					
						'key'=>'login_name',
						'op'=>'eq',
						'od'=>$login_name
					),
					'`is_delete` = 0'
				)
			));
		
		}
		
		public function remove($login_name){
		
			return $this->db_client->update('TB_USER_PROFILES',$params,array(
			
				'scond' => array(
					
					array(
					
						'key'=>'login_name',
						'op'=>'eq',
						'od'=>$login_name
					),
					'`is_delete` = 1'
				)
			));
		
		}
		
		public function delete($login_name){
			
			return $this->db_client->delete('TB_USER_PROFILES',array(
			
				'key'=>'login_name',
				'op'=>'eq',
				'od'=>$login_name
			));
		
		}
				
		public function change_pwd($login_name,$new_pwd,$old_pwd){
					
			return $this->db_client->update('TB_USER_PROFILES',array('pwd'=>md5($new_pwd)),array(
			
				'scond' => array(
					
					array(
					
						'key'=>'login_name',
						'op'=>'eq',
						'od'=>$login_name
					),
					array(
					
						'key'=>'pwd',
						'op'=>'eq',
						'od'=>md5($old_pwd)
					)
				)
			));
		
		}
		
		public function check($login_name,$field,$value){
					
			if($field == 'pwd')
				$value = md5($value);
			
			return $this->exists(array(
			
				'login_name'=>$login_name,
				$field=>$value
			
			));
				
		}
		
		public function login_name_exists($login_name){
			
			return !$this->login_name_not_exists($login_name);
		
		}
		
		public function login_name_not_exists($login_name){
					
			return !$this->exists(array('login_name'=>$login_name));
			
		}
		
		public function mail_not_exits($mail){
		
			return !$this->exists(array('mail'=>$mail));
		
		}
				
		public function login($login_name,$pwd,$keep,
			$___attributes=array(
				'captchaVA',
				'paramsVA'=>array('replies'=>array('login_name'=>'用户名不能为空!','pwd'=>'密码不能为空!'))
			)
		){
		
			if($this->login_name_not_exists($login_name))
				throw new VerifyException(sprintf('登录名"%s"不存在!',$login_name),2);
			
			if(!$this->check($login_name,'pwd',$pwd))
				throw new VerifyException('密码错误!',2);
				
			$user_info = $this->db_client->first_row(SqlClient::generate_query('TB_USER_PROFILES',array(
			
				'id'=>'id',
				'login_name'=>'name',
				'status'=>'status'
			
			),'`login_name`=:login_name'),array('login_name'=>$login_name));
			
			$userInfo['_keep'] = $keep;
			
			$this->user_cache_set($user_info);
						
			return $user_info;
		}
		
		public function log_out(){
			
			return $this->user_cache_remove();
			
		}
		
		public function log_off(){
			
			$userInfo = $this->user_cache_get();
			
			if(!$userInfo) return false;
			
			return $this->db_client->update('TB_USER_PROFILES',array( 
			
				'is_delete' => 1
				
			),'`login_name`=\''.$userInfo.'\' and `is_delete` = 0');
			
		}
		
		protected function exists($params){
		
			$scond = array();

			foreach($params as $key=>$value){
			
				$scond []= array(
				
					'key'=>$key,
					'op'=>'eq',
					'od'=>':'.$key
				);
				
			}
			
			return $this->db_client->count_row(SqlClient::generate_query('TB_USER_PROFILES',1,array(
			
				'rel'=>'and',
				'scond'=>$scond
			
			)),$params)>0;
		
		}
				
		public function test(){
			
			$xml_string = file_get_contents('http://ceshi.cardplus.cn/wx.rest/service/dict/industry');
			$xml_string = trim($xml_string);
			$xml_object = simplexml_load_string($xml_string);
			$xml_arr    = get_object_vars($xml_object);
			
			return $xml_arr;
			
		}
		
	}

}