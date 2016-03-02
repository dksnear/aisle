<?php
namespace aisle\proj\doki8;

use aisle\web\Controller;
use aisle\db\Field;

class QueryController extends Controller{
	
	public function PointHistory(){
				
		return $this->dbm
			->Query('point_history')
			->Select(
			
				Field::Build('user_name'),
				Field::Build('record_num'),
				Field::Build('article_id'),
				Field::Build('point'),
				Field::Build('desc'),
				Field::Build('record_time')
			)
			->Where()
			->Eq(Field::Build('user_name')->Value('dksnear'))->T()
			->Limit(0,2)
			->Compile()
			->Run()->Meta();
	}
	
	public function Sum(){
		
		return $this->dbm
			->Query('point_history')
			->Select(	
				Field::Build('user_name'),
				Field::Build('point')->Alias('point_sum')->Aggregate('sum')
			)
			->GroupBy(Field::Build('user_name'))
			->Desc(Field::Build('point_sum'))
			->Compile()
			->Run()->Meta();	
	}
	
	public function UserDealCost($name='dksnear'){
		
		return $this->view('dealcostgrid',array('statements' => $this->dbm->DirectQuery("
		
			select `ph`.`article_id`,`ar`.`title`,`ar`.`category_desc`,`ph`.`point`,`ph`.`record_time` from
			(select `user_name`,`article_id`,`point`,`record_time`,`desc` from `point_history` where `user_name`='$name' and `point`< 0 and `article_id`>0) as `ph`
			left join (select `title`,`id`,`category_desc` from `article`) as `ar` on `ph`.`article_id`=`ar`.`id`
			
		")->Run()->Meta(),'user'=>$name));
	}
	
	// disabled
	public function UserPublishCost($name='dksnear'){
		
		return $this->dbm->DirectQuery("
		
			select `ph`.`article_id`,`ar`.`title`,`ar`.`category_desc`,`ph`.`point`,`ph`.`record_time` from
			(select `user_name`,`article_id`,`point`,`record_time`,`desc` from `point_history` where `user_name`='$name' and `point`< 0 and `desc`='发布文章使用心动币') as `ph`
			left join (select `title`,`id`,`category_desc` from `article`) as `ar` on `ph`.`article_id`=`ar`.`id`
			
		")->Run()->Meta();
	}
	
	public function User($name='dksnear'){
		
		return $this->view('usergrid',array('statements' => $this->dbm->DirectQuery("
		
			select `p`.*,`a`.`article_count`,`ph1`.`comment_times`,`ph2`.`subject_times`,`ph3`.`login_times`,`ph4`.`cost_times`,`ph4`.`costs` from 
			(select `name`,`nick_name`,`rank`,`badges`,date_format(`regist_time`,'%Y-%m-%d') as 'regist_time',`points` from `profile` where `name`='$name') as `p`
			left join (select `author`,count(1) as 'article_count' from `article` group by `author`) as `a` on `a`.`author`=`p`.`name`
			join (select `user_name`,count(1) as 'comment_times' from `point_history` where `desc`='评论奖励' group by `user_name` ) as `ph1` on `ph1`.`user_name` = `p`.`name`
			join (select `user_name`,count(1) as 'subject_times' from `point_history` where `desc`='回复主题奖励' group by `user_name` ) as `ph2` on `ph2`.`user_name` = `p`.`name`
			join (select `user_name`,count(1) as 'login_times' from `point_history` where `desc`='每日登录奖励' group by `user_name` ) as `ph3` on `ph3`.`user_name` = `p`.`name`
			join (select `user_name`,count(1) as 'cost_times',sum(`point`) as 'costs' from `point_history` where `point`<0 group by `user_name` ) as `ph4` on `ph4`.`user_name` = `p`.`name`
			
		")->Run()->Meta(),'user'=>$name));
		
	}
	
	public function AllUser(){
		
		return $this->view('usergrid',$this->dbm->DirectQuery("
		
			select `p`.*,`a`.`article_count`,`ph1`.`comment_times`,`ph2`.`subject_times`,`ph3`.`login_times`,`ph4`.`cost_times`,`ph4`.`costs` from 
			(select `name`,`nick_name`,`rank`,`badges`,date_format(`regist_time`,'%Y-%m-%d') as 'regist_time',`points` from `profile` order by `points` desc limit 0,20) as `p`
			left join (select `author`,count(1) as 'article_count' from `article` group by `author`) as `a` on `a`.`author`=`p`.`name`
			join (select `user_name`,count(1) as 'comment_times' from `point_history` where `desc`='评论奖励' group by `user_name` ) as `ph1` on `ph1`.`user_name` = `p`.`name`
			join (select `user_name`,count(1) as 'subject_times' from `point_history` where `desc`='回复主题奖励' group by `user_name` ) as `ph2` on `ph2`.`user_name` = `p`.`name`
			join (select `user_name`,count(1) as 'login_times' from `point_history` where `desc`='每日登录奖励' group by `user_name` ) as `ph3` on `ph3`.`user_name` = `p`.`name`
			join (select `user_name`,count(1) as 'cost_times',sum(`point`) as 'costs' from `point_history` where `point`<0 group by `user_name` ) as `ph4` on `ph4`.`user_name` = `p`.`name`
			order by `p`.`points` desc
			
		")->Run()->Meta(),true);
		
		
	}
	
	public function Deal($name='dksnear'){
		
		return $this->view('dealgrid',array('statements'=>$this->dbm->DirectQuery("
		
			select `ar`.`title`,`ph`.*,`ar`.`category_desc`,`ar`.`comment_count` as `comments`,`ar`.`view_count` as `views`, date_format(`ar`.`publish_time`,'%Y-%m-%d') as 'publish_time',format(`ph`.`sum`/`pr`.`points`*100,2) as 'points_rate' from
			(select `article_id` as `id`,sum(`point`) as `sum`,format(avg(`point`),1) as `avg`,count(1) as `count`,group_concat(distinct `point`) as `probable` from `point_history` where `point`>0 and `article_id`>0 group by `article_id`) as `ph`
			join `article` as `ar` on `ar`.`id`=`ph`.`id` 
			join (select `points`,`name` from `profile` where `name`='$name') as `pr` on `ar`.`author`=`pr`.`name`
			order by `ph`.`sum` desc;
		
		")->Run()->Meta(),'user'=>$name));
	}
	
	// *
	public function AllDeal(){
		
		return $this->view('dealgrid',$this->dbm->DirectQuery("
		
			select `ar`.`title`,`ph`.*,`ar`.`category_desc`,`ar`.`comment_count` as `comments`,`ar`.`view_count` as `views`, date_format(`ar`.`publish_time`,'%Y-%m-%d') as 'publish_time',format(`ph`.`sum`/`pr`.`points`*100,2) as 'points_rate' from
			(select `article_id` as `id`,sum(`point`) as `sum`,format(avg(`point`),1) as `avg`,count(1) as `count`,group_concat(distinct `point`) as `probable` from `point_history` where `point`>0 and `article_id`>0 group by `article_id`) as `ph`
			join `article` as `ar` on `ar`.`id`=`ph`.`id` 
			join (select `name`,`points` from `profile` order by `points` desc limit 0,5 ) as `pr` on `ar`.`author`=`pr`.`name`
			order by `pr`.`points` desc,`ph`.`sum` desc;
		
		")->Run()->Meta(),true);
	}
	
	public function ReportDeal($order='sum'){
		
		return $this->view('dealgrid',array('statements'=> $this->dbm->DirectQuery("
		
			select `ar`.`title`,`ph`.*,`ar`.`category_desc`,`ar`.`comment_count` as 'comments',`ar`.`view_count` as 'views', date_format(`ar`.`publish_time`,'%Y-%m-%d') as 'publish_time',format(`ph`.`sum`/`pr`.`points`*100,2) as 'points_rate' from
			(select `article_id` as 'id',sum(`point`) as 'sum',format(avg(`point`),1) as 'avg',count(1) as 'count',group_concat(distinct `point`) as 'probable' from `point_history` where `point`>0 and `article_id`>0 group by `article_id`) as `ph`
			join `article` as `ar` on `ar`.`id`=`ph`.`id` 
			join (select `name`,`points` from `profile`) as `pr` on `ar`.`author`=`pr`.`name`
			order by `$order` desc limit 0,5;
		
		")->Run()->Meta(),'order'=>$order));
		
	}
	
	public function AllReportDeal(){
			
		return $this->view('basic',array_reduce(array('sum','count','views','comments'),function($ret,$order){
			
			return $ret.$this->ReportDeal($order)->Render(null,true);
			
		}),true);
	
	}
	
	public function ReportUser($order='points'){
		
		return $this->view('usergrid',array('statements' => $this->dbm->DirectQuery("
		
			select `p`.*,`a`.`article_count`,`ph1`.`comment_times`,`ph2`.`subject_times`,`ph3`.`login_times`,`ph4`.`cost_times`,`ph4`.`costs` from 
			(select `name`,`nick_name`,`rank`,`badges`,date_format(`regist_time`,'%Y-%m-%d') as 'regist_time',`points` from `profile`) as `p`
			left join (select `author`,count(1) as 'article_count' from `article` group by `author`) as `a` on `a`.`author`=`p`.`name`
			join (select `user_name`,count(1) as 'comment_times' from `point_history` where `desc`='评论奖励' group by `user_name` ) as `ph1` on `ph1`.`user_name` = `p`.`name`
			join (select `user_name`,count(1) as 'subject_times' from `point_history` where `desc`='回复主题奖励' group by `user_name` ) as `ph2` on `ph2`.`user_name` = `p`.`name`
			join (select `user_name`,count(1) as 'login_times' from `point_history` where `desc`='每日登录奖励' group by `user_name` ) as `ph3` on `ph3`.`user_name` = `p`.`name`
			join (select `user_name`,count(1) as 'cost_times',sum(`point`) as 'costs' from `point_history` where `point`<0 group by `user_name` ) as `ph4` on `ph4`.`user_name` = `p`.`name`
			order by `$order` desc limit 0,5
			
		")->Run()->Meta(),'order'=>$order));
		
	}
	
	public function AllReportUser(){
		
		return $this->view('basic',array_reduce(array('points','article_count','comment_times','subject_times','login_times'),function($ret,$order){
			
			return $ret.$this->ReportUser($order)->Render(null,true);
			
		}),true);
	}
	
	// *
	public function AllReport(){
		
		return $this->view('basic',$this->AllReportDeal()->Render(null,true).$this->AllReportUser()->Render(null,true),true);
	}
	
	public function Profile($name='dksnear'){
		
		return $this->view('basic',$this->User($name)->Render(null,true).$this->Deal($name)->Render(null,true).$this->UserDealCost($name)->Render(null,true),true);
	}
	
}