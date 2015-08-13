<?php
namespace aisle\project\demo;

use aisle\core\Trace;
use aisle\db\Statement;
use aisle\db\driver\MysqlClient;

class DbController{
	
	public function OpRange(){
		
		$statement = new Statement('table',null,null);		
		Trace::WriteLine($statement->OpRanges('union','unionall'));
		
	}
	
	public function Query1(){
		
		$client = new MysqlClient();
		
		$client
			->Query('TB_USER_PROFILES')
			// ->Insert(
				// array('id',null,null,true),
				// array('login_name',null,null,true),
				// array('status',null,1)
			
			// )
			// ->Update(
			
				// array('id',null,null,true),
				// array('login_name',null,null,true),
				// array('status',null,1)
			
			// )
			->Select(
				// array('id','id'),
				db\Field::Build('id')->Alias('cd')->TableName('kk')->Aggregate('count'),
				array('login_name','name'),
				array('status','status')
			)
			->Where()
			->And_(true)
			->Gt(array('id',null,1))
			->Gt(db\Field::Build('id')->Value(1))
			->Lt(array('id',null,10),'and')
			->P()
			->And_()
			->NotBetween(array('status'),100,200)
			->Or_()
			->Eq(array('login_name',null,'bb'))
			->Ins(array('login_name'),array('cc','dd','ee'),'or')
			->T()
			->GroupBy(array('id'),array('status'))
			->Having()
			->Gt(array('id',null,1,false,null,'sum'))
			->Exists('TB_USER_STATUS','and')
			->Select(1)
			->Where()
			->Lte(array('id',null,2))
			->T()->P()->T()
			->Desc(array('id'),array('status'))
			->Asc(array('login_name'))
			->Limit(3,3)	
			->Compile();
		
	}
		
	public function Query2(){
		
		$client = new MysqlClient();
		
		// select `dic`.`key` as `category`,`dic`.`value` as `name`, count(`link`.`category`) as `count` from 
		// ( select `key`,`value`,`order` from `TB_DICTIONARY` where `name`='link_category') as `dic` left join 
		// ( select `category` from `TB_LINK_POOL` ) as `link` on `dic`.`key`=`link`.`category`  
		// group by `dic`.`key` order by `dic`.`order` asc;
		
		$client
			->Connect(array('dbname'=>'db_accurate_search','password'=>'123456'))
			->Query()
			->Select(
				db\Field::Build('key')->TableName('dic')->Alias('category'),
				db\Field::Build('value')->TableName('dic')->Alias('name'),
				db\Field::Build('category')->TableName('link')->Alias('count')->Aggregate('count')
			)
			->Join('TB_DICTIONARY','dic',true)
				->Select(db\Field::Build('key'),db\Field::Build('value'),db\Field::Build('order'))
				->Where()->Eq(db\Field::Build('name')->Value('link_category'))->T()->P()
			->LeftJoin('TB_LINK_POOL','link')
				->Select(db\Field::Build('category'))->P()
			->On()->Eq(db\Field::Build('key')->TableName('dic')->Value(db\Field::Build('category')->TableName('link')))->T()
			->GroupBy(db\Field::Build('key')->TableName('dic'))
			->Asc(db\Field::Build('order')->TableName('dic'))
			->Compile()
			->Run()
			->JsonEncode()
			->WriteLine();
		
	}
			
		

	
}