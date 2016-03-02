<?php
namespace aisle\view;

class HtmlTable extends Basic{
	
	public function Render($statements=null,$ret=false){
				
		$statements = parent::Render($statements,true);

		if($statements instanceof Message)
			$statements = array($statements->jsonSerialize());
		
		$out = '';
		
		if(isset($statements[0])){
			
			$out = '<table style="text-align:left;"><tr>';
			
			foreach(array_keys($statements[0]) as $key)				
				$out.=sprintf('<th style="padding:2px 10px 2px 2px;">%s</th>',$key);

			$out.= '</tr>';
			
			foreach($statements as $row){
				
				$out.='<tr>';
				foreach($row as $cell)
					$out.=sprintf('<td style="padding:2px 10px 2px 2px;">%s</td>',$cell);
				$out.='</tr>';
			}
			
			$out.='</table>';
			
		}
		
		if($ret) return $out;
		
		echo $out;
		
	}
	
}