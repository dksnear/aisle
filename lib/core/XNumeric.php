<?php
namespace aisle\core;

class XNumeric extends XType{
	
	public static function Build($meta){
		
		return new self($meta);
	}

	protected $callableMethodMap = array(
	
		'Abs'=>'abs', // 绝对值 
		'Acos'=>'acos', // 反余弦 
		'Acosh'=>'acosh', // 反双曲余弦 
		'Asin'=>'asin', // 反正弦 
		'Asinh'=>'asinh', // 反双曲正弦 
		'Atan'=>'atan', // 反正切 
		'Atan2'=>'atan2', // 两个参数的反正切 
		'Atanh'=>'atanh', // 反双曲正切 
		'BaseConvert'=>'base_convert', // 在任意进制之间转换数字 
		'Bindec'=>'bindec', // 把二进制转换为十进制 
		'Ceil'=>'ceil', // 向上舍入为最接近的整数 
		'Cos'=>'cos', // 余弦 
		'Cosh'=>'cosh', // 双曲余弦 
		'Decbin'=>'decbin', // 把十进制转换为二进制 
		'Dechex'=>'dechex', // 把十进制转换为十六进制 
		'Decoct'=>'decoct', // 把十进制转换为八进制 
		'Deg2rad'=>'deg2rad', // 将角度转换为弧度 
		'Exp'=>'exp', // 返回 Ex 的值 
		'Expm1'=>'expm1', // 返回 Ex - 1 的值 
		'Floor'=>'floor', // 向下舍入为最接近的整数 
		'Fmod'=>'fmod', // 返回除法的浮点数余数 
		'Getrandmax'=>'getrandmax', // 显示随机数最大的可能值 
		'Hexdec'=>'hexdec', // 把十六进制转换为十进制 
		'Hypot'=>'hypot', // 计算直角三角形的斜边长度 
		'IsFinite'=>'is_finite', // 判断是否为有限值 
		'IsInfinite'=>'is_infinite', // 判断是否为无限值 
		'IsNan'=>'is_nan', // 判断是否为合法数值 
		'LcgValue'=>'lcg_value', // 返回范围为 (0, 1) 的一个伪随机数 
		'Log'=>'log', // 自然对数 
		'Log10'=>'log10', // 以 10 为底的对数 
		'Log1p'=>'log1p', // 返回 log(1 + number) 
		'Max'=>'max', // 返回最大值 
		'Min'=>'min', // 返回最小值 
		'MtGetrandmax'=>'mt_getrandmax', // 显示随机数的最大可能值 
		'MtRand'=>'mt_rand', // 使用 Mersenne Twister 算法返回随机整数 
		'MtSrand'=>'mt_srand', // 播种 Mersenne Twister 随机数生成器 
		'Octdec'=>'octdec', // 把八进制转换为十进制  
		'Pow'=>'pow', // 返回 x 的 y 次方 
		'Rad2deg'=>'rad2deg', // 把弧度数转换为角度数 
		'Rand'=>'rand', // 返回随机整数 
		'Round'=>'round', // 对浮点数进行四舍五入 
		'Sin'=>'sin', // 正弦 
		'Sinh'=>'sinh', // 双曲正弦 
		'Sqrt'=>'sqrt', // 平方根 
		'Srand'=>'srand', // 播下随机数发生器种子 
		'Tan'=>'tan', // 正切 
		'Tanh'=>'tanh' // 双曲正切 
	);
	
	protected function compile($meta){
		
		$this->meta = is_object($meta) ? 0 : (float)$meta;
		
		return $this;
	}
}

