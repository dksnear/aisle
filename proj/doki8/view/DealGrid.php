<?php
namespace aisle\proj\doki8\view;

use aisle\core\XString;
use aisle\view\Basic;
use aisle\view\Message;

class DealGrid extends Basic{
	
	protected $order;
	
	protected $user;
	
	public function Render($statements=null,$ret=false){
		
		$statements = parent::Render($statements,true);

		if($statements instanceof Message)
			return $statements->Render(null,$ret);
		
		$out = '';
		$title = '文章交易统计表';
		$styles = array(
		
			'rowno_style'=>'style="padding:2px 5px 2px 2px;"',
			'id_style'=>'style="padding:2px 5px 2px 2px;"',
			'category_desc_style'=>'style="padding:2px 5px 2px 2px;"',
			'sum_style'=>'style="padding:2px 5px 2px 2px;"',
			'count_style'=>'style="padding:2px 5px 2px 2px;"',
			'avg_style'=>'style="padding:2px 5px 2px 2px;"',
			'probable_style'=>'style="padding:2px 5px 2px 2px;"',
			'points_rate_style'=>'style="padding:2px 5px 2px 2px;"',
			'comments_style'=>'style="padding:2px 5px 2px 2px;"',
			'views_style'=>'style="padding:2px 5px 2px 2px;"',
			'publish_time_style'=>'style="padding:2px 5px 2px 2px;"'
		
		);
		
		$titles = array(
		
			'rowno_title'=>'行号',
			'id_title'=>'文章标识',
			'category_desc_title'=>'文章类别',
			'sum_title'=>'交易额',
			'count_title'=>'交易数',
			'avg_title'=>'均价',
			'probable_title'=>'交易价',
			'points_rate_title'=>'积分占有率',
			'comments_title'=>'评论数',
			'views_title'=>'查看数',
			'publish_time_title'=>'发布时间'
		);
		
		if(isset($styles[$this->order.'_style']))
			$styles[$this->order.'_style'] = 'style="padding:2px 5px 2px 2px; color:red;"';
		
		if(isset($titles[$this->order.'_title']))
			$title = sprintf('[文章交易统计TOP5]%s排行表',$titles[$this->order.'_title']);
		
		if($this->user)
			$title = sprintf('[%s]文章交易统计表',$this->user);

		$title = sprintf('%s(%s)',$title,date('Y-m-d'));
			
		$format = '			
			<div>
				<table style="font-family: sans-serif; text-align: center; font-size: 12px;">
					<caption align="top"><h4>{title}</h4></caption> 
					<tr>
						<th {rowno_style}>{rowno_title}</th>
						<th {id_style}>{id_title}</th>
						<th {category_desc_style}>{category_desc_title}</th>
						<th {sum_style}>{sum_title}</th>
						<th {count_style}>{count_title}</th>
						<th {avg_style}>{avg_title}</th>
						<th {probable_style}>{probable_title}</th>
						<th {points_rate_style}>{points_rate_title}</th>
						<th {comments_style}>{comments_title}</th>
						<th {views_style}>{views_title}</th>
						<th {publish_time_style}>{publish_time_title}</th>
					</tr>
					{rows}
				</table>
			</div>	
		';
		
		$rowFormat = '
		
			<tr style="background-color:gray; text-align:left; color:white;" ><td colspan="11">{title}</td></tr>
			<tr>
				<td {rowno_style}>{rowno}</td>
				<td {id_style}>{id}</td>
				<td {category_desc_style}>{category_desc}</td>
				<td {sum_style}>{sum}</td>
				<td {count_style}>{count}</td>
				<td {avg_style}>{avg}</td>
				<td {probable_style}>{probable}</td>
				<td {points_rate_style}>{points_rate}%</td>
				<td {comments_style}>{comments}</td>
				<td {views_style}>{views}</td>
				<td {publish_time_style}>{publish_time}</td>
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