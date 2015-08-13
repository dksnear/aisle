<?php
namespace aisle\view;

class HtmlList extends Basic{
	
	public function Render($statements=null,$ret=false){
		
		$statements = parent::Render($statements,true);

		if($statements instanceof Message)
			$statements = array($statements->jsonSerialize());
		
		$out = '<div>';
		
		foreach($statements as $row){
			
			$out.='<dl style="border-bottom:1px solid;padding-bottom:15px;font-size:12px;line-height:18px">';
			foreach($row as $key=>$value)
				$out.=sprintf('<dt style="font-weight:bold;line-height:25px;">%s</dt><dd>%s</dd>',$key,$value);
			$out.='</dl>';
		}
		
		$out.= '</div>';
		
		if($ret) return $out;
		
		echo $out;
		
	}
	
}