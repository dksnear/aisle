<?php
namespace aisle\proj\doki8\view;

use aisle\core\XString;
use aisle\view\Basic;
use aisle\view\Message;

class UserGrid extends Basic{
	
	protected $order;
	
	protected $user;
	
	public function Render($statements=null,$ret=false){
		
		$statements = parent::Render($statements,true);

		if($statements instanceof Message)
			return $statements->Render(null,$ret);
		
		$out = '';
		$title = '用户统计表';
		$styles = array(
		
			'rowno_style'=>'style="padding:2px 5px 2px 2px;"',
			'nick_name_style'=>'style="padding:2px 5px 2px 2px;"',
			'name_style'=>'style="padding:2px 5px 2px 2px;"',
			'rank_style'=>'style="padding:2px 5px 2px 2px;"',
			'points_style'=>'style="padding:2px 5px 2px 2px;"',
			'article_count_style'=>'style="padding:2px 5px 2px 2px;"',
			'comment_times_style'=>'style="padding:2px 5px 2px 2px;"',
			'subject_times_style'=>'style="padding:2px 5px 2px 2px;"',
			'login_times_style'=>'style="padding:2px 5px 2px 2px;"',
			'cost_times_style'=>'style="padding:2px 5px 2px 2px;"',
			'costs_style'=>'style="padding:2px 5px 2px 2px;"',
			'regist_time_style'=>'style="padding:2px 5px 2px 2px;"'
		
		);
		
		$titles = array(
		
			'rowno_title'=>'行号',
			'nick_name_title'=>'昵称',
			'name_title'=>'用户名',
			'rank_title'=>'等级',
			'points_title'=>'积分',
			'article_count_title'=>'文章数',
			'comment_times_title'=>'评论数',
			'subject_times_title'=>'主题数',
			'login_times_title'=>'登录数',
			'cost_times_title'=>'花费数',
			'costs_title'=>'花费积分',
			'regist_time_title'=>'注册时间'
		);
		
		if(isset($styles[$this->order.'_style']))
			$styles[$this->order.'_style'] = 'style="padding:2px 5px 2px 2px; color:red;"';
		
		if(isset($titles[$this->order.'_title']))
			$title = sprintf('[用户统计TOP5]%s排行表',$titles[$this->order.'_title']);
		
		if($this->user)
			$title = sprintf('[%s]用户统计表',$this->user);
		
		$title = sprintf('%s(%s)',$title,date('Y-m-d'));

		$format = '			
			<div>
				<table style="font-family: sans-serif; text-align: center; font-size: 12px;">
					<caption align="top"><h4>{title}</h4></caption>
					<tr>
						<th {rowno_style}>{rowno_title}</th>
						<th {nick_name_style}>{nick_name_title}</th>
						<th {name_style}>{name_title}</th>
						<th {rank_style}>{rank_title}</th>
						<th {points_style}>{points_title}</th>
						<th {article_count_style}>{article_count_title}</th>
						<th {comment_times_style}>{comment_times_title}</th>
						<th {subject_times_style}>{subject_times_title}</th>
						<th {login_times_style}>{login_times_title}</th>
						<th {cost_times_style}>{cost_times_title}</th>
						<th {costs_style}>{costs_title}</th>
						<th {regist_time_style}>{regist_time_title}</th>
					</tr>
					{rows}
				</table>
			</div>	
		';
		
		$rowFormat = '
		
			<tr style="background-color:gray; text-align:left; color:white;" ><td colspan="12">{badges}</td></tr>
			<tr>
				<td {rowno_style}>{rowno}</td>
				<td {nick_name_style}>{nick_name}</td>
				<td {name_style}>{name}</td>
				<td {rank_style}>{rank}</td>
				<td {points_style}>{points}</td>
				<td {article_count_style}>{article_count}</td>
				<td {comment_times_style}>{comment_times}</td>
				<td {subject_times_style}>{subject_times}</td>
				<td {login_times_style}>{login_times}</td>
				<td {cost_times_style}>{cost_times}</td>
				<td {costs_style}>{costs}</td>
				<td {regist_time_style}>{regist_time}</td>
			</tr>
		
		';
		
		if(isset($statements[0])){
			
			$count =1;
			
			foreach($statements as $item){
				$item['rowno'] = $count++;
				$out.=XString::Render($rowFormat,array_merge($item,$styles),true);	
			}
		}
		
		$out = XString::Render($format,array_merge(array('rows'=>$out,'title'=>$title),$styles,$titles),true);
		
		if($ret) return $out;
		
		echo $out;
		
	}
	
}