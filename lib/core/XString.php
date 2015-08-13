<?php
namespace aisle\core;

class XString extends XType{
	
	public static function Build($meta){
		
		return new self($meta);
	}
	
	public static function Format($format){
		
		$args = func_get_args();
		$i = 1;
		$len = count($args);
		$patterns = array();
		$replacements = array();
		
		for(;$i<$len;$i++){
			
			$patterns []= '/\{'.($i-1).'\}/s';
			$replacements []= $args[$i];
		}
		
		$format = preg_replace($patterns,$replacements,$format);
			
		return new self($format);
	}
	
	public static function Render($format,$data,$meta=false){
		
		$patterns = array();
		$replacements = array();
		
		foreach($data as $key=>$value){
			
			$patterns []= '/\{'.$key.'\}/s';
			$replacements []= $value;
		}
		
		$format = preg_replace($patterns,$replacements,$format);
			
		return $meta ? $format : new self($format);
			
	}
	
	public static function GUID($hyphen=null,$meta=false){
		
		$charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen =  isset($hyphen) ? $hyphen : chr(45);		
		$guid = implode($hyphen,array(substr($charid, 0, 8),substr($charid, 8, 4),substr($charid,12, 4),substr($charid,16, 4),substr($charid,20,12)));	
		
		return $meta ? $guid : new self($guid);
	}
		
	protected $callableMethodMap = array(
	
		'Addcslashes'=>'addcslashes', //	在指定的字符前添加反斜杠
		'Addslashes'=>'addslashes', //	在指定的预定义字符前添加反斜杠
		'Bin2hex'=>'bin2hex', //	把 ASCII 字符的字符串转换为十六进制值
		'Chr'=>'chr', //	从指定的 ASCII 值返回字符
		'ChunkSplit'=>'chunk_split', //	把字符串分割为一连串更小的部分
		'ConvertCyrString'=>'convert_cyr_string', //	把字符由一种 Cyrillic 字符转换成另一种
		'ConvertUudecode'=>'convert_uudecode', //	对 uuencode 编码的字符串进行解码
		'ConvertUuencode'=>'convert_uuencode', //	使用 uuencode 算法对字符串进行编码
		'CountChars'=>'count_chars', //	返回字符串所用字符的信息
		'Crc32'=>'crc32', //	计算一个字符串的 32-bit CRC
		'Crypt'=>'crypt', //	单向的字符串加密法 (hashing)
		'Echo'=>'echo', //	输出字符串
		'Hebrev'=>'hebrev', //	把希伯来文本从右至左的流转换为左至右的流
		'Hebrevc'=>'hebrevc', //	同上，同时把(\n) 转为	<br />
		'HtmlEntityDecode'=>'html_entity_decode', //	把 HTML 实体转换为字符
		'Htmlentities'=>'htmlentities', //	把字符转换为 HTML 实体
		'HtmlspecialcharsDecode'=>'htmlspecialchars_decode', //	把一些预定义的 HTML 实体转换为字符
		'Htmlspecialchars'=>'htmlspecialchars', //	把一些预定义的字符转换为 HTML 实体
		'Levenshtein'=>'levenshtein', //	返回两个字符串之间的 Levenshtein 距离
		'Ltrim'=>'ltrim', //	从字符串左侧删除空格或其他预定义字符
		'Md5'=>'md5', //	计算字符串的 MD5 散列
		'Md5File'=>'md5_file', //	计算文件的 MD5 散列
		'Metaphone'=>'metaphone', //	计算字符串的 metaphone 键
		'MoneyFormat'=>'money_format', //	把字符串格式化为货币字符串
		'Nl2br'=>'nl2br', //	在字符串中的每个新行之前插入 HTML 换行符
		'Ord'=>'ord', //	返回字符串第一个字符的 ASCII 值
		'ParseStr'=>'parse_str', //	把查询字符串解析到变量中
		'QuotedPrintableDecode'=>'quoted_printable_decode', //	解码 quoted-printable 字符串
		'Quotemeta'=>'quotemeta', //	在字符串中某些预定义的字符前添加反斜杠
		'Rtrim'=>'rtrim', //	从字符串的末端开始删除空白字符或其他预定义字符
		'Sha1'=>'sha1', //	计算字符串的 SHA-1 散列
		'Sha1File'=>'sha1_file', //	计算文件的 SHA-1 散列
		'SimilarText'=>'similar_text', //	计算两个字符串的匹配字符的数目
		'Soundex'=>'soundex', //	计算字符串的 soundex 键
		'Sprintf'=>'sprintf', //	把格式化的字符串写写入一个变量中
		'Sscanf'=>'sscanf', //	根据指定的格式解析来自一个字符串的输入
		'Pad'=>'str_pad', //	把字符串填充为新的长度
		'Repeat'=>'str_repeat', //	把字符串重复指定的次数
		'Rot13'=>'str_rot13', //	对字符串执行 ROT13 编码
		'Shuffle'=>'str_shuffle', //	随机地打乱字符串中的所有字符
		'WordCount'=>'str_word_count', //	计算字符串中的单词数
		'Casecmp'=>'strcasecmp', //	比较两个字符串（对大小写不敏感）
		'Chr'=>'strchr', //	搜索字符串在另一字符串中的第一次出现strstr() 的别名
		'Cmp'=>'strcmp', //	比较两个字符串（对大小写敏感）
		'Coll'=>'strcoll', //	比较两个字符串（根据本地设置）
		'Cspn'=>'strcspn', //	返回在找到任何指定的字符之前，在字符串查找的字符数
		'IpTags'=>'strip_tags', //	剥去 HTML、XML 以及 PHP 的标签
		'Ipcslashes'=>'stripcslashes', //	删除由 addcslashes() 函数添加的反斜杠
		'Ipslashes'=>'stripslashes', //	删除由 addslashes() 函数添加的反斜杠
		'Ipos'=>'stripos', //	返回字符串在另一字符串中第一次出现的位置(大小写不敏感)
		'Istr'=>'stristr', //	查找字符串在另一字符串中第一次出现的位置(大小写不敏感)
		'Len'=>'strlen', //	返回字符串的长度
		'Natcasecmp'=>'strnatcasecmp', //	使用一种“自然”算法来比较两个字符串（对大小写不敏感）
		'Natcmp'=>'strnatcmp', //	使用一种“自然”算法来比较两个字符串（对大小写敏感）
		'Ncasecmp'=>'strncasecmp', //	前 n 个字符的字符串比较（对大小写不敏感）
		'Ncmp'=>'strncmp', //	前 n 个字符的字符串比较（对大小写敏感）
		'Pbrk'=>'strpbrk', //	在字符串中搜索指定字符中的任意一个
		'Pos'=>'strpos', //	返回字符串在另一字符串中首次出现的位置（对大小写敏感）
		'Rchr'=>'strrchr', //	查找字符串在另一个字符串中最后一次出现的位置
		'Rev'=>'strrev', //	反转字符串
		'Ripos'=>'strripos', //	查找字符串在另一字符串中最后出现的位置(对大小写不敏感)
		'Rpos'=>'strrpos', //	查找字符串在另一字符串中最后出现的位置(对大小写敏感)
		'Spn'=>'strspn', //	返回在字符串中包含的特定字符的数目
		'Str'=>'strstr', //	搜索字符串在另一字符串中的首次出现（对大小写敏感）
		'Tok'=>'strtok', //	把字符串分割为更小的字符串
		'Tolower'=>'strtolower', //	把字符串转换为小写
		'Toupper'=>'strtoupper', //	把字符串转换为大写
		'Tr'=>'strtr', //	转换字符串中特定的字符
		'Substr'=>'substr', //	返回字符串的一部分
		'SubstrCompare'=>'substr_compare', //	从指定的开始长度比较两个字符串
		'SubstrCount'=>'substr_count', //	计算子串在字符串中出现的次数
		'SubstrReplace'=>'substr_replace', //	把字符串的一部分替换为另一个字符串
		'Trim'=>'trim', //	从字符串的两端删除空白字符和其他预定义字符
		'Ucfirst'=>'ucfirst', //	把字符串中的首字符转换为大写
		'Ucwords'=>'ucwords', //	把字符串中每个单词的首字符转换为大写
		'Vfprintf'=>'vfprintf', //	把格式化的字符串写到指定的输出流
		'Vprintf'=>'vprintf', //	输出格式化的字符串
		'Vsprintf'=>'vsprintf', //	把格式化字符串写入变量中
		'Wordwrap'=>'wordwrap', //	按照指定长度对字符串进行折行处理
		'Unserialize'=>'unserialize', // 反序化
		'FileGetContents'=>'file_get_contents' // 读取文件内容
	);
	
	public function Replace($pattern,$replacement){
		
		$args = func_get_args();
		
		array_splice($args,2,0,$this->meta);
		
		if(is_callable($args[1]))
			return $this->resolveType(call_user_func_array('preg_replace_callback',$args));
		
		return $this->resolveType(call_user_func_array('preg_replace',$args));
		
	}
	
	public function Test($pattern){
		
		return $this->resolveType(preg_match($pattern,$this->meta));
		
	}
	
	public function Match($pattern){
		
		$args = func_get_args();
		$matches = null;
		array_splice($args,1,0,$this->meta);
		array_splice($args,2,0,0);
		$args[2] = &$matches;
		
		call_user_func_array('preg_match',$args);
		
		return $this->resolveType($matches);
	}
	
	public function MatchAll($pattern){
		
		$args = func_get_args();
		$matches = null;
		array_splice($args,1,0,$this->meta);
		array_splice($args,2,0,0);
		$args[2] = &$matches;
		
		call_user_func_array('preg_match_all',$args);
		
		return $this->resolveType($matches);
	}
	
	public function Split($pattern){
		
		$args = func_get_args();
		array_splice($args,1,0,$this->meta);
		
		return $this->resolveType(call_user_func_array('preg_split',$args));
	}
	
	public function Explode($delimiter){
		
		return $this->resolveType(explode($delimiter,$this->meta));
	}
	
	public function ToCharset($inCharset,$outCharset){
		
		return $this->resolveType(iconv($inCharset,$outCharset,$this->meta));
	}
	
	public function Escape() {  
	
		$token = '';  
		
		for ($i =0;$i <strLen($this->meta);$i++) {
			if (ord($this->meta[$i]) >=127) {
				//$temp =bin2hex(iconv('gb2312','ucs-2',substr($this->meta,$i,2)));
				$token.='%u'.bin2hex(iconv('gb2312','ucs-2',substr($this->meta,$i,2)));
				$i++;
			} else {
				$token.='%'.dechex(ord($this->meta[$i]));
			}

		}
		
		return $this->resolveType($token);  
	} 
	
	public function Unescape() {  
	
		$str=rawurldecode($this->meta);
		preg_match_all('/%u.{4}|&#x.{4};|&#\d+;|.+/U',$str,$r);
		$ar=$r[0];
		foreach($ar as $k=>$v) {
			if (substr($v,0,2)=='%u')
				$ar[$k]=iconv('UCS-2','GBK',pack('H4',substr($v,-4)));
			elseif(substr($v,0,3)=='&#x')
				$ar[$k]=iconv('UCS-2','GBK',pack('H4',substr($v,3,-1)));
			elseif(substr($v,0,2)=='&#') {
				$ar[$k]=iconv('UCS-2','GBK',pack('n',substr($v,2,-1)));
			}
		}

		return $this->resolveType(implode('',$ar));
	}
	
	public function DesEncrypt($key){
		
		$vector = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM);
		$en_str = mcrypt_encrypt(MCRYPT_DES, $key, $this->meta, MCRYPT_MODE_ECB,$vector);
		
		return $this->resolveType(base64_encode($en_str));
		
	}
	
	public function DesDecrypt($key){
		
		$en_str = base64_decode($this->meta);
		$vector = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM);
		
		return $this->resolveType(mcrypt_decrypt(MCRYPT_DES,$key,$en_str,MCRYPT_MODE_ECB,$vector));
		
	}
	
	public function JsonDecode(){
			
		return $this->resolveType(json_decode($this->meta,true));
	}
	
	protected function compile($meta){
		
		$this->meta = is_array($meta) || is_object($meta) ? '' : (string)$meta;
		
		return $this;
	}
	
}

