<?php
namespace aisle\core;

use aisle\ex\CoreException;

class XArray extends XType{
	
	public static function Build($meta){
		
		return new self($meta);
	}
	
	public static function Make(&$meta){
		
		return new self(true,null,$meta);
	}
	
	public static function Range(){
		
		return new self(call_user_func_array('range',func_get_args()));
	}
	
	public static function FromObject($obj){
	
		return new self(self::ObjectToArray($obj));
	}
	
	public static function ObjectToArray($obj){
		
		$obj=(array)$obj;
		
		foreach($obj as $k=>$v){
			if( gettype($v)=='resource' ) return;
			if( gettype($v)=='object' || gettype($v)=='array' )
				$obj[$k]=(array)self::ObjectToArray($v);
		}
		return $obj;
	}
	
	public static function ArrayToObject($arr){
		
		if(gettype($arr)!='array') return;
		
		foreach($arr as $k=>$v){
			if( gettype($v)=='array' || gettype($v)=='object' )
				$arr[$k]=(object)self::ArrayToObject($v);
		}
		return (object)$arr;
	}
		
	protected $callableMethodMap = array(
	
		'ChangeKeyCase'=>'array_change_key_case', //	返回其键均为大写或小写的数组
		'Chunk'=>'array_chunk', //	把一个数组分割为新的数组块
		'Combine'=>'array_combine', //	通过合并两个数组来创建一个新数组
		'CountValues'=>'array_count_values', //	用于统计数组中所有值出现的次数
		'Diff'=>'array_diff', //	返回两个数组的差集数组
		'DiffAssoc'=>'array_diff_assoc', //	比较键名和键值，并返回两个数组的差集数组
		'DiffKey'=>'array_diff_key', //	比较键名，并返回两个数组的差集数组
		'DiffUassoc'=>'array_diff_uassoc', //	通过用户提供的回调函数做索引检查来计算数组的差集
		'DiffUkey'=>'array_diff_ukey', //	用回调函数对键名比较计算数组的差集
		'Fill'=>'array_fill', //	用给定的值填充数组
		'Filter'=>'array_filter', //	用回调函数过滤数组中的元素
		'Flip'=>'array_flip', //	交换数组中的键和值
		'Intersect'=>'array_intersect', //	计算数组的交集
		'IntersectAssoc'=>'array_intersect_assoc', //	比较键名和键值，并返回两个数组的交集数组
		'IntersectKey'=>'array_intersect_key', //	使用键名比较计算数组的交集
		'IntersectUassoc'=>'array_intersect_uassoc', //	带索引检查计算数组的交集，用回调函数比较索引
		'IntersectUkey'=>'array_intersect_ukey', //	用回调函数比较键名来计算数组的交集
		'KeyExists'=>'array_key_exists', //	检查给定的键名或索引是否存在于数组中
		'Keys'=>'array_keys', //	返回数组中所有的键名
		'Merge'=>'array_merge', //	把一个或多个数组合并为一个数组
		'MergeRecursive'=>'array_merge_recursive', //	递归地合并一个或多个数组
		'Multisort'=>'array_multisort', //	对多个数组或多维数组进行排序
		'Pad'=>'array_pad', //	用值将数组填补到指定长度
		'Product'=>'array_product', //	计算数组中所有值的乘积
		'Push'=>'array_push', //	将一个或多个单元（元素）压入数组的末尾（入栈）
		'Rand'=>'array_rand', //	从数组中随机选出一个或多个元素，并返回
		// 'Reduce'=>'array_reduce', //	用回调函数迭代地将数组简化为单一的值
		'Reverse'=>'array_reverse', //	将原数组中的元素顺序翻转，创建新的数组并返回
		'Search'=>'array_search', //	在数组中搜索给定的值，如果成功则返回相应的键名
		'Slice'=>'array_slice', //	在数组中根据条件取出一段值，并返回
		'Splice'=>'array_splice', //	把数组中的一部分去掉并用其它值取代
		'Sum'=>'array_sum', //	计算数组中所有值的和
		'Udiff'=>'array_udiff', //	用回调函数比较数据来计算数组的差集
		'UdiffAssoc'=>'array_udiff_assoc', //	带索引检查计算数组的差集，用回调函数比较数据
		'UdiffUassoc'=>'array_udiff_uassoc', //	带索引检查计算数组的差集，用回调函数比较数据和索引
		'Uintersect'=>'array_uintersect', //	计算数组的交集，用回调函数比较数据
		'UintersectAssoc'=>'array_uintersect_assoc', //	带索引检查计算数组的交集，用回调函数比较数据
		'UintersectUassoc'=>'array_uintersect_uassoc', //	带索引检查计算数组的交集，用回调函数比较数据和索引
		'Unique'=>'array_unique', //	删除数组中重复的值
		'Unshift'=>'array_unshift', //	在数组开头插入一个或多个元素
		'Values'=>'array_values', //	返回数组中所有的值
		'Walk'=>'array_walk', //	对数组中的每个成员应用用户函数
		'WalkRecursive'=>'array_walk_recursive', //	对数组中的每个成员递归地应用用户函数
		'Arsort'=>'arsort', //	对数组进行逆向排序并保持索引关系
		'Asort'=>'asort', //	对数组进行排序并保持索引关系
		'Count'=>'count', //	计算数组中的元素数目或对象中的属性个数
		'Krsort'=>'krsort', //	对数组按照键名逆向排序
		'Ksort'=>'ksort', //	对数组按照键名排序
		'Natcasesort'=>'natcasesort', //	用“自然排序”算法对数组进行不区分大小写字母的排序
		'Natsort'=>'natsort', //	用“自然排序”算法对数组排序
		'Rsort'=>'rsort', //	对数组逆向排序
		'Shuffle'=>'shuffle', //	把数组中的元素按随机顺序重新排列
		'Sort'=>'sort', //	对数组排序
		'Uasort'=>'uasort', //	使用用户自定义的比较函数对数组中的值进行排序并保持索引关联
		'Uksort'=>'uksort', //	使用用户自定义的比较函数对数组中的键名进行排序
		'Usort'=>'usort' // 使用用户自定义的比较函数对数组中的值进行排序
	
	);
	
	public function __construct($meta,$prev=null,&$refMeta=null){
		
		$meta === true ? $this->refCompile($refMeta) : $this->compile($meta);
		$this->setPrevious($prev);
	}
	
	public function __call($method,$args){
		
		if(!isset($this->callableMethodMap[$method]))
			throw new CoreException('method "'.$method.'" not exists!');
		
		
		array_unshift($args,0);
		$args[0] = &$this->meta;
			
		return $this->resolveType(call_user_func_array($this->callableMethodMap[$method],$args));
	}
			
	public function Pop($result=false){
		
		$item = array_pop($this->meta);
		
		return !$result ? $this : $this->resolveType($item);
	}
	
	public function Shift($result=false){
		
		$item = array_shift($this->meta);
		
		return !$result ? $this : $this->resolveType($item);
	}
	
	public function Set(){
		
		$args = func_get_args();
		$count = count($args);	
		$arr = &$this->meta;
		
		for($i=0;$i<$count;$i++){
		
			if($i == $count-1){
		
				$arr = $args[$i];				
				break;
			}
		
			// if(!array_key_exists($args[$i],$arr) || !is_array($arr[$args[$i]]))		
			if(!isset($arr[$args[$i]]) || !is_array($arr[$args[$i]]))		
				$arr[$args[$i]] = array();			
					
			$arr = & $arr[$args[$i]];
							
		}
		
		return $this;
	}
	
	public function Get(){
		
		$args = func_get_args();
		$count = count($args);		
		$arr = $this->meta;
		
		for($i=0;$i<$count;$i++){
			
			// if(is_array($arr) && array_key_exists($args[$i],$arr)){
			if(is_array($arr) && isset($arr[$args[$i]])){
				$arr = $arr[$args[$i]];
				continue;
			}
			
			return $this->resolveType(null);
					
		}
		
		return $this->resolveType($arr);
	}
	
	public function Prop($name){
		
		return $this->resolveType($this->meta[$name]);
	}
	
	public function Each($fn){
		
		foreach($this->meta as $key=>$item){
			
			if($fn($item,$key)===false)
				return $this;	
		}
		
		return $this;
	}
	
	public function Map($fn){
		
		$arr=array();
		
		foreach($this->meta as $key=>$item){
			
			$arr []= $fn($item,$key);	
		}
		
		return $this->resolveType($arr);
	}
	
	public function Reduce($fn,$init){
		
		foreach($this->meta as $key=>$item){
			
			$init = $fn($init,$item,$key);		
		}
		
		return $this->resolveType($init);
	}
	
	public function Replace($pattern,$replacement){
		
		$args = func_get_args();
		
		array_splice($args,2,0,$this->meta);
		
		if(is_callable($args[1]))
			return $this->resolveType(call_user_func_array('preg_replace_callback',$args));
		
		return $this->resolveType(call_user_func_array('preg_replace',$args));
		
	}
	
	public function Grep($pattern,$flags=0){
		
		return $this->resolveType(preg_grep($pattern,$this->meta,$flags));
	}
		
	public function ArrayType(){
		
		if(!is_array($this->meta) || empty($this->meta)) return '';
		
		$count = count($this->meta);
		$intersect = array_intersect_key($this->meta,range(0,$count-1));
		
		if(count($intersect) == $count)
			return $this->resolveType('index');
		
		if(empty($intersect))
			return $this->resolveType('associate');
		
		return $this->resolveType('mix');
	}
	
	public function IsIndexArrayType(){
		
		return $this->resolveType($this->ArrayType()->Meta() == 'index');
	}
	
	public function IsAssocArrayType(){
		
		return $this->resolveType($this->ArrayType()->Meta() == 'associate');
	}
	
	public function IsMixArrayType(){
		
		return $this->resolveType($this->ArrayType()->Meta() == 'mix');
	}
	
	public function DeepMerge($target){
		
		return $this->resolveType($this->deepMergeRecursive($this->meta,$target));
	}
	
	public function Contains($value,$strict=false){
		
		return $this->resolveType(in_array($value,$this->meta,$strict));
	}
	
	public function Implode($glue){
		
		return $this->resolveType(implode($glue,$this->meta));
	}
	
	public function Join($glue){
		
		return $this->Implode($glue,$this->meta);
	}
	
	public function JsonEncode($opts=0){
		
		return $this->resolveType(json_encode($this->meta,$opts));
	}
	
	public function ToObject(){
		
		return $this->resolveType(self::ArrayToObject($this->meta));
	}
			
	protected function deepMergeRecursive($a1,$a2){
		
		if(!is_array($a1))
			return $a2;
			
		if(!is_array($a2))
			return $a1;
	
		foreach($a2 as $key=>$value){
		
			if(array_key_exists($key,$a1)){
			
				if(is_array($value) && is_array($a1[$key]))
					$a1[$key] = $this->deepMergeRecursive($a1[$key],$value);
				else if (!is_array($value) && is_array($a1[$key]))
					$a1[$key] [] = $value;
				else if(is_array($value) && !is_array($a1[$key]))
					$a1[$key] = array_merge($value,array($a1[$key]));
				else $a1[$key] = $value;
				
			} else $a1[$key] = $value;
		
		}
		
		return $a1;
	}	
	
	protected function compile($meta){
		
		$this->meta = is_array($meta) ? $meta : array($meta);
		
		return $this;
	}
	
	protected function refCompile(&$meta){
		
		if(!is_array($meta))
			$this->meta = array();
		else $this->meta = &$meta;
			
		return $this;
	}
}

