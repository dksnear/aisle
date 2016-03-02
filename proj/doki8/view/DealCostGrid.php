<?php
namespace aisle\proj\doki8\view;

use aisle\core\XString;
use aisle\view\Basic;
use aisle\view\Message;

class DealCostGrid extends Basic{
	
	protected $user;
	
	public function Render($statements=null,$ret=false){
		
		$statements = parent::Render($statements,true);

		if($statements instanceof Message)
			return $statements->Render(null,$ret);
		
		$out = '';
		$title = sprintf('[%s]文章购入统计表(%s)',$this->user,date('Y-m-d'));
			
		$format = '			
			<div>
				<table style="font-family: sans-serif; text-align: center; font-size: 12px;">
					<caption align="top"><h4>{title}</h4></caption> 
					<tr>
						<th style="padding:2px 5px 2px 2px;">行号</th>
						<th style="padding:2px 5px 2px 2px;">文章标识</th>
						<th style="padding:2px 5px 2px 2px;">分类</th>
						<th style="padding:2px 5px 2px 2px;">交易额</th>
						<th style="padding:2px 5px 2px 2px;">交易时间</th>
					</tr>
					{rows}
				</table>
			</div>	
		';
		
		$rowFormat = '
		
			<tr style="background-color:gray; text-align:left; color:white;" ><td colspan="5">{title}</td></tr>
			<tr>
				<td style="padding:2px 5px 2px 2px;">{rowno}</td>
				<td style="padding:2px 5px 2px 2px;">{article_id}</td>
				<td style="padding:2px 5px 2px 2px;">{category_desc}</td>
				<td style="padding:2px 5px 2px 2px;">{point}</td>
				<td style="padding:2px 5px 2px 2px;">{record_time}</td>
			</tr>	
		';
		
		if(isset($statements[0])){
			
			$count =1;
			
			foreach($statements as $item){
				$item['rowno'] = $count++;
				$out.=XString::Render($rowFormat,$item,true);	
			}
		}
		
		$out = XString::Render($format,array('rows'=>$out,'title'=>$title),true);
		
		if($ret) return $out;
		
		echo $out;
		
	}
	
}