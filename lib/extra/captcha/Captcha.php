<?php
namespace aisle\extra\captcha{

	/**************************
	
	验证码类
	
	by Aphyric 
	
	201410
	 
	**************************/
	
	use \aisle\common as common;

	class Captcha {

		
		// 验证码 
		// 一般模式 array('code'=>'','expire'=>int)
		// 备选模式 array('captcha'=>'','candidate'=>'','code'=>'','expire'=>int)
		private $captcha_code = null;
		
		// 图片句柄
		private $img = null;
				
		// 图片高度
		private $height = null;
		
		// 图片宽度
		private $width = null;
		
		// 验证字符大小
		private $font_size = 20;
		
		// 验证字符数量
		private $font_num = 5;
		
		// 备选字符数量
		private $candidate_font_num = 16;
		
		// 中文 (需要中文字体库支持)
		private $is_zh = false;
		
		// 备选模式
		private $is_candidate = false;
		
		// ttf字体路径
		private $font_family = array(	
		
			'./extra/captcha/gb2312_01.ttf'
		);
		
		// 过期时间 单位(秒)
		private $expire = 60; 
		
		public function __construct($options = null){
		
			$this->_set_property($options,'captcha_code',0);
			$this->_set_property($options,'font_num',1);
			$this->_set_property($options,'font_size',2);
			$this->_set_property($options,'is_zh',3);
			$this->_set_property($options,'is_candidate',4);
			$this->_set_property($options,'candidate_font_num',5);
			$this->_set_property($options,'font_family',6);
			$this->_set_property($options,'width',7,($this->is_candidate ? $this->_auto_width($this->candidate_font_num,$this->font_size,1.5) : $this->_auto_width($this->font_num,$this->font_size)));
			$this->_set_property($options,'height',8,($this->font_size * ($this->is_candidate ? 4 : 2)));
			$this->_set_property($options,'expire',9);		
		}
		
		
		public function __get($property_name){
		
			// 给所有私有变量设置读取器
			if(isset($this->$property_name))
				return $this->$property_name;
			
			return null;
		
		}
		
		public function create_image(){
	
		
			$code = $this->get_code();
			
			$this->img = imagecreate($this->width, $this->height);
				
			if(!$this->is_candidate){
			
				$this->_draw_noise_bg($this->img,$this->font_size*$this->font_num,$this->height/2);
				$this->_draw_text($this->img,$code['code'],$this->font_num,$this->height);
			
			}
			
			if($this->is_candidate){
				
				$captcha_img = imagecreate($this->width,$this->height/2);
				$this->_draw_noise_bg($captcha_img,$this->font_size*$this->font_num,$this->height/2);
				$this->_draw_text($captcha_img,$code['captcha'],$this->font_num,$this->height/2);
				
				
				$candidate_img = imagecreate($this->width,$this->height/2);
				$this->_draw_text($candidate_img,$code['candidate'],$this->candidate_font_num,$this->height/2,1.5);
				
				imagecopy($this->img,$captcha_img,0,0,0,0,$this->width,$this->height/2);
				imagecopy($this->img,$candidate_img,0,$this->height/2,0,0,$this->width,$this->height/2);
				
				imagedestroy($captcha_img);
				imagedestroy($candidate_img);
			
			}
			
			imagepng($this->img);
			imagedestroy($this->img);
		
		}
						
		public function get_code(){
		
			$this->captcha_code = common\_or($this->captcha_code, $this->code($this->font_num,($this->is_candidate ? $this->candidate_font_num : false),$this->is_zh));
			
			return $this->captcha_code;
		
		}
		
		public function code($num = 1,$cadi_num = 0,$zh = true){
		
			$code = array(
			
				'expire'=>$this->expire + time()
			);
			
			if(empty($cadi_num)){
			
				$code['code'] = $zh ? $this->zh_code($num) : $this->en_code($num);		
				return $code;
			}
			
			$num = $num > $cadi_num ? $cadi_num : $num;
			
			$code['captcha'] = array();
			$code['candidate'] = $zh ? $this->zh_code($cadi_num) : $this->en_code($cadi_num);		
			$code['code'] = $this->na_code($num,$cadi_num);
			
			foreach($code['code'] as $pos)
				$code['captcha'][] = $zh ? mb_substr($code['candidate'],$pos,1,'UTF-8') : substr($code['candidate'],$pos,1);

			$code['captcha'] = implode('',$code['captcha']);				
			$code['code'] = implode('',$code['code']);
						
			return $code;
		
		}
		
		// 产生@range范围内@num个不重复的随机数字 
		
		public function na_code($num = 1,$range = 1){
		
			$num = $num > $range ? $range : $num;
		
			$range = range(0,$range);
			
			shuffle($range);
			
			return array_slice($range,0,$num);
		}
		
		// 产生@num个不重复的随机英文字母或数字组成的字符串
		
		public function en_code($num = 1){
		
			$range = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			
			$num = $num > strlen($range) ? strlen($range) : $num;
		
			return substr(str_shuffle($range), 0, $num);
		
		}
		
		// 产生@num个不重复的随机汉字组成的字符串
		
		public function zh_code($num = 1){
		
			$unique = array();
			$codes = array();
			
			// 限定汉字最大数量50
			$num = $num > 50 ? 50 : $num;
			
			// 限定最高循环次数
			$round = $num * 10;
		
			while($num > 0 && $round > 0){
		
				$c1 = substr(str_shuffle('BCD'), 0, 1);
				$c2 = ($c1 == 'D') ? substr(str_shuffle('0123456'), 0, 1) : substr(str_shuffle('0123456789ABCDEF'), 0, 1);
				$c3 = substr(str_shuffle('ABCEF'), 0, 1);
				$c4 = ($c3 == 'A') ? substr(str_shuffle('123456789ABCDEF'), 0, 1) : (($c3 == 'F') ? substr(str_shuffle('0123456789ABCDE'), 0, 1) : substr(str_shuffle('0123456789ABCDEF'), 0, 1));
				$code = '%' . $c1 . $c2 . '%' . $c3 . $c4;
				
				if(!array_key_exists($code,$unique)){
				
					$unique[$code] = true;
					$codes[] = mb_convert_encoding(urldecode($code), 'UTF-8', 'GBK');
					$num--;
				}
				
				$round --;
			}
		
			return implode('', $codes);
		
		}
		
		private function _draw_text($img,$text,$count,$height,$step=1,$font_size=null,$font_color=null){
					
			imagecolorallocate($img,255,255,255);
			
			$font_size = common\_or($font_size,$this->font_size);
		
			for($pos = 0; $pos<$count ; $pos++) {
			
				imagettftext(
					$img,
					$font_size,
					mt_rand(-20, 20), 
					($font_size / 2 + $font_size * $pos) * $step, 
					($height + $font_size) / 2, 
					common\_or($font_color,imagecolorallocate($img, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255))),
					$this->font_family[mt_rand(0, count($this->font_family) - 1)], 
					mb_substr($text, $pos, 1, 'UTF-8'));
					
			};
		
		}
		
		private function _draw_noise_bg($img,$width,$height){
		
			$font_num = $width/$this->font_size+1;
		
			$noise_bg_img1 = imagecreate($width,$height);
			$noise_bg_img2 = imagecreate($width,$height);
			
			$this->_draw_text($noise_bg_img1,$this->en_code($font_num),$font_num,$height,1.2,$this->font_size*2/3);
			$this->_draw_text($noise_bg_img2,$this->en_code($font_num),$font_num,$height,1.2,$this->font_size*2/3);

			imagecopy($img,$noise_bg_img1,$width/4,$height/4,$width/2,$height/2,$width,$height);
			imagecopy($img,$noise_bg_img2,$width*2/3,$height*2/3,$width/2,$height/2,$width,$height);
			
			imagedestroy($noise_bg_img1);
			imagedestroy($noise_bg_img2);
		
		}
		
		
		private function _auto_width($font_num,$font_size,$step=1){
		
			 return ($font_num + 1) * $font_size * $step;
		
		}

		private function _set_property($attrs,$name,$no,$default=null){
					
			$this->$name = common\_or(common\_or(common\_array_get($attrs,$no),common\_array_get($attrs,$name)),common\_or($default,$this->$name));
			
		}

	}
	
}