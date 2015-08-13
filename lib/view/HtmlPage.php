<?php
namespace aisle\view;
use aisle\web\Response;

class HtmlPage extends Basic{
	
	protected $pageRoot = './$static_page';
	
	protected $pageExt = 'html';
			
	public function Render($statements=null,$ret = false){
		
		$statements = parent::Render($statements,true);
		
		$path = $this->pageRoot.'/'.$statements.'.'.$this->pageExt;
				
		$content = file_get_contents($path);
		
		echo $content;
		
	}
	
}