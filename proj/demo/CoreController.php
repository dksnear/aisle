<?php
namespace aisle\proj\demo;

use aisle\core\Trace;
use aisle\core\XType;
use aisle\core\XString;
use aisle\core\XArray;
use aisle\cache\FileCache;
use aisle\web\Controller;

class CoreController extends Controller{
	
	public function Welcome(){
		
		return 'Welcome To Aisle!';
		
	}
	
	public function XType(){
		
		XType::Build('http://caipiao.163.com/award/jxssc/#from=kjdt')
			->FileGetContents()
			->MatchAll('/data-win-number=(?:"|\')([\d\s]+?)(?:"|\')\sdata-period="\d{8}(\d{3})"/')
			->Action(function($matches){
								
				return core\XType::Build(array())->Action(function($result) use($matches){
					
					return $matches->Get(1)->Walk(function($match,$i) use($result,$matches){
					
						$result->Set($i,array($matches->Get(2,$i)->Meta(),preg_replace('/\s/','',$match)));
						
					})->Action(function() use($result){ 
					
						return $result;  
					});
				
				});
				
			})
			->WriteLine();
		
	}
	
	public function XArray(){
		
		$ar = array(1,2,3,4,5);
		
		$m = XArray::Make($ar)->Write()->Unshift(7,8)->Previous()->Usort(function($a,$b){
			
			if ($a == $b) return 0;
			return ($a < $b) ? -1 : 1;
			
		})->P()->Merge(array(9,10))->Write()->P()->Meta();
			
		Trace::WriteLine($m);
		
		XArray::Build(array(
		
			'boolean'=>'XType',	
			'integer'=>'XType',
			'double'=>'XType',	
			'string'=>'XString',	
			'array'=>'XArray',	
			'object'=>'XType',	
			'resource'=>'XType',	
			'NULL'=>'XType',
			'unknown type'=>'XType'
			
		))->Get('string')->P()->Serialize()->Escape()->WriteLine();
		
	}
	
	public function XString(){
		
		XString::Format('xx{0},{1}','$xx0','$xx1')->WriteLine();
			
		XString::Build('123@ksd.kl.com')->Action(function($s){
			
			  Trace::WriteLine($s->Test('/^(\w)+@(\w)+((\.\w+)+)$/') ? 'true':'false');
		});
		
	}

	public function FileCache(){
		
		// $cache = new FileCache();
		// $cache->Connect();
		// $cache->Set('tt','11');
		// $cache->Set('tt2','dd');
		// $cache->Set('tt3','dd3');
		
		// return $cache->Get('tt3');
		
		$this->cachem->Set('tt3','dd3');
		
		return $this->cachem->Get('tt3');
	}
	
	
}