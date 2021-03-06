<?php
namespace aisle\core;

/*************

usecase1:

$f = new File('./services',1);

usecase2:

File::Each('./',-1,function($file){
	print_r(str_repeat(' ',$file->level*2).$file->name."\n");
});

**************/

class File implements \JsonSerializable {

    // 文件类型定义
    public static $FILETYPE = array('DIRECTORY'=>'D','FILE'=>'F','SPECIAL'=>'S','RESERVE'=>'R');

    // 文件id
    protected $fileId;

    // 父目录id
    protected $directoryId = null;

    // 文件类型
    protected $type;

    // 文件完整路径
    protected $fullName;

    // 文件名
    protected $name = null;

    // 目录中的文件(如果当前文件是目录)
    protected $children = null;

    // 父目录对象 aisle\File object
    protected $directory = null;

    // 相对深度
    protected $level = 1;

    public static function Read($fileName) {

        if (!is_readable($fileName))
            return false;

        $file = fopen($fileName,'r');

        if (!$file)
            return false;

        $size = filesize($fileName);

        $data = fread($file,!$size ? 1 : $size);

        fclose($file);

        return $data;

    }
	
    public static function Write($fileName,$data,$mode='w') {

        if (!in_array($mode,array('w','a','x')))
            return false;

        $dir = dirname($fileName);

        if (!is_dir($dir))
            $dir = mkdir($dir,0777,true);

        if (!$dir) return false;

        $file = fopen($fileName,$mode);

        if (!$file || !is_writable($fileName)) return false;

        $count = fwrite($file,$data);
        fclose($file);

        return $count;

    }

    public static function Delete($fileName) {
		
		if(is_dir($fileName)){
			
			self::Each($fileName,1,function($file) use ($fileName){
				
				if($fileName == $file->fullName)
					return;
							
				self::Delete($file->fullName);
			});
			
			return rmdir($fileName);
		}

        if (file_exists($fileName))
            return unlink($fileName);

        return false;

    }
	
	public static function Copy($src,$dest,$filter=null,$force=true){
		
		if(is_dir($src)){
			
			$succeed = false;
			$favailable = is_callable($filter);
		
			self::Each($src,-1,function($srcFile) use($src,$dest,$filter,$force,$favailable,&$succeed){
				
				if($srcFile->type == self::$FILETYPE['DIRECTORY'])
					return;
				
				$rp = str_replace($src,'',$srcFile->fullName);
				$src = $srcFile->fullName;
				$dest = $dest.$rp;
				
				if($favailable){
					
					$ret = $filter($rp,$src,$dest);
					if($ret === false)
						return false;
					if(!$ret)
						return;
				} 
	
				$succeed = self::Copy($src,$dest,$filter,$force);
				
				if(!$succeed) return false;
				
			});
			
			return $succeed;
		}
		
		
		if(!file_exists($src))
			return false;
		
		if(!$force && file_exists($dest))
			return false;
		
		$dir = dirname($dest);
		
		if(!is_dir($dir)){
			
			if(!mkdir($dir,0777,true))
				return false;
		}
		
		return copy($src,$dest);
	}
	
	public static function Move($src,$dest,$filter=null,$force=true){
		
		if(is_dir($src)){
			
			$succeed = false;
			$favailable = is_callable($filter);
		
			self::Each($src,-1,function($srcFile) use($src,$dest,$filter,$force,$favailable,& $succeed){
				
				if($srcFile->type == self::$FILETYPE['DIRECTORY'])
					return;
	
				$rp = str_replace($src,'',$srcFile->fullName);
				$src = $srcFile->fullName;
				$dest = $dest.$rp;
				
				if($favailable){
					
					$ret = $filter($rp,$src,$dest);
					if($ret === false)
						return false;
					if(!$ret)
						return;
				} 
	
				$succeed = self::Move($src,$dest,$filter,$force);
				
				if(!$succeed) return false;
				
			});
			
			if($favailable)
				return $succeed;
			
			return self::Delete($src) && $succeed;
		}
		
		
		if(!file_exists($src))
			return false;
		
		if(!$force && file_exists($dest))
			return false;
		
		$dir = dirname($dest);
		
		if(!is_dir($dir)){
			
			if(!mkdir($dir,0777,true))
				return false;
		}
		
		return rename($src,$dest);
	}
	
    // 遍历当前文件下的所有文件
    public static function Each($fileName,$depth=-1,$fn) {

        return new self(self::FixName($fileName),$depth,$fn);

    }

    // 获取当前文件下的所有文件
    public static function GetAll($fileName,$depth=-1,$fn) {

        $files = array();

        new self(self::FixName($fileName),$depth,function($file) use($fn,& $files) {

            $files []= is_callable($fn) ? $fn($file) : $file;

        });

        return $files;
    }
	
	// !untested
	// 同步2个目录中的差异文件
	// @src 源目录
	// @dest 目标目录
	// @filters 文件名排除正则列表(匹配到的文件将不做任何处理)
	// @flags 1:非递归同步  2:双向同步(不能和减持模式同时使用) 4:同步目标文件夹中不存在的文件(增持模式) 
	//        8:以修改时间较早的文件为源 16:关闭md5校验 
	//        32:删除目标文件夹中存在但源文件夹不存在的文件(减持模式)(不能和增持模式同时使用)
	//        64:模拟文件操作 不影响实际文件系统	
	// #return false | array('count'=>'被修改的文件数目','rpath'=>'被修改文件的相对路径列表','files'=>'被修改的文件集合')
	// -1(减持) 0(失败) 1(同步) 2(增持)
	public static function Sync2($src,$dest,$filters=array(),$flags=0){
		
		$src = self::FixName($src);
		$dest = self::FixName($dest);
		
		if(!is_dir($src))
			return false;
		
		if(!is_dir($dest)){
			
			if(!mkdir($dest,0777,true))
				return false;
		}
		
		$out = array('count'=>0,'rpath'=>array(),'files'=>array());
		
		$filters = empty($filters) ? $filters : (is_array($filters) ? $filters : array($filters));
		
		if((4 & $flags) == 4)
			$flags = $flags^32;
		
		if((32 & $flags) == 32){
			
			$flags = $flags^2;
			$tempd = $src;
			$src = $dest;
			$dest = $tempd;
		}
		
		self::Each($src,(1 & $flags) == 1 ? 1 : -1,function($srcFile) use($src,$dest,$filters,$flags,& $out){
			
			if($srcFile->type == self::$FILETYPE['DIRECTORY'])
				return;
			
			$srcFile = $srcFile->fullName;
			$rpath = str_replace($src,'',$srcFile);
			$destFile = $dest.$rpath;
			$simulate = (64 & $flags) == 64;
	
			if(!empty($filters)){
				
				$matches = false;
				
				foreach($filters as $filter){
							
					if(preg_match($filter,$srcFile)){
						
						$matches = true;
						break;
					}
				}
				
				if($matches) return false;
				
			}
			
			if(!file_exists($destFile)){
				
				if((32 & $flags) == 32){
							
					$out['files'][$srcFile] = -1 * self::Delete($srcFile,$simulate); 
					$out['count']++;
					$out['rpath'][$rpath] = 1;
				}
				
				if((4 & $flags) == 4){
					
					$out['files'][$destFile] = 2 * self::Copy($srcFile,$destFile,false,$simulate);
					$out['count']++;
					$out['rpath'][$rpath] = 1;
				}
				
				return;
			}
			
			if((16 & $flags) != 16 && md5_file($srcFile) == md5_file($destFile))
				return;
			
			if((8 & $flags) == 8){
				
				$tempf = $srcFile;
				$srcFile = $destFile;
				$destFile = $tempf;
			}
			
			$sut = filemtime($srcFile);
			$dut = filemtime($destFile);
			
			if($sut == $dut) return;
			
			$out['files'][$sut > $dut ? $destFile : $srcFile] = $sut > $dut ? self::Copy($srcFile,$destFile,false,$simulate): self::Copy($destFile,$srcFile,false,$simulate);		
			$out['count']++;
			$out['rpath'][$rpath] = 1;
						
		});
		
		if((2 & $flags) == 2){
			
			$rout = self::Sync2($dest,$src,$filters,$flags^2);
			$out['count'] = $out['count'] + $rout['count'];
			$out['files'] = array_merge($out['files'],$rout['files']);
			$out['rpath'] = array_merge($out['rpath'],$rout['rpath']);
		}
		
		return $out;
		
		
	}
	
	// !untested
	// 同步多个目录中的差异文件
	public static function Sync($dirs,$filters=array(),$flags=0){
		
		if(!is_array($dirs))
			return false;
		
		$len = count($dirs);
		
		if($len < 2)
			return false;
		
		if($len == 2)
			return self::Sync2($dirs[0],$dirs[1]);
		
		$out = array('count'=>0,'rpath'=>array(),'files'=>array());
		
		for($i = 1; $i < $len; $i ++){
			
			$cur_out = self::Sync2($dirs[0],$dirs[$i],$filters,$flags);
			if(empty($cur_out))
				continue;
			$out['count'] = $out['count'] + $cur_out['count'];
			$out['files'] = array_merge($out['files'],$cur_out['files']);
			$out['rpath'] = array_merge($out['rpath'],$cur_out['rpath']);
		}
		
		$src = self::FixName($dirs[0]);
		$simulate = (64 & $flags) == 64;

		for($i = 1; $i < $len; $i ++){
			
			$dest = self::FixName($dirs[$i]);
			
			foreach($out['rpath'] as $k=>$v){
				
				$srcFile = $src.$k;
				$destFile = $dest.$k;
				
				if(!file_exists($srcFile))
					continue;
				
				if(!file_exists($destFile)){
					
					if((4 & $flags) == 4){
						
						$out['files'][$destFile] = 2 * self::Copy($srcFile,$destFile,false,$simulate);
						$out['count']++;
					}
					
					continue;
				}
				
				if((16 & $flags) != 16 && md5_file($srcFile) == md5_file($destFile))
					continue;
				
				$out['files'][$destFile] = self::Copy($srcFile,$destFile,false,$simulate);
				$out['count']++;
			}
		}
		
		if($simulate) 
			$out['count'] = $out['count']/2;
		
		return $out;	
		
	}
	
	public static function FixName($path){
		
		if(is_string($path))
			return preg_replace('/^\\\\|^\/|\\\\$|\/$/','',preg_replace('/\\\\{2,}|\/{2,}/',DIRECTORY_SEPARATOR,preg_replace('/\\\\|\//',DIRECTORY_SEPARATOR,$path)));
		if(is_array($path))
			return self::FixName(implode(DIRECTORY_SEPARATOR,$path));
		
		return $path;
	}
	
    // @file_name 目标文件完整路径
    // @depth(int) 读取深度 如果为-1则读取所有层级
    // @fn 读取进行时委托 $fn($currentFile)
    // @level 相对深度
    // @directory 父目录文件对象(系统设定 应用构造忽略)
    // @directory_id 父目录id(系统设定 应用构造忽略)
    public function __construct($fileName,$depth = -1,$fn = null,$level = 1,$directory = null,$directoryId = null) {


        if (!file_exists($fileName))
            return;

        $this->fileId = uniqid();
        $this->fullName = $fileName;
        $this->name = basename($this->fullName);
        $this->level = $level;
        $this->directory = $directory;
        $this->directoryId = $directoryId;
        $this->type = self::$FILETYPE['SPECIAL'];

        if (is_file($this->fullName))
            $this->type = self::$FILETYPE['FILE'];

        if (is_dir($this->fullName)) {
            $this->type = self::$FILETYPE['DIRECTORY'];
            $this->children = array();
        }
		
		if(is_callable($fn)){
						
			if($fn($this) === false)
				return;
		}
		
        $this->readDir($this,$depth,$fn,$level);

    }

    public function __get($name) {
		
		if(property_exists($this,$name))
			return $this->$name;

        return null;

    }

    public function FileRead() {

        // throw
        if ($this->type != self::$FILETYPE['FILE'])
            return false;

        return self::Read($this->fullName);

    }

    public function FileWrite($data,$mode='w') {

        if ($this->type != self::$FILETYPE['FILE'])
            return false;

        return self::Write($this->fullName,$data,$mode);

    }

    public function FileDelete() {

        return self::Delete($this->fullName);

    }
	
	public function FileCopy($dest,$filter=null,$force=true){
		
		return self::Copy($this->fullName,$dest,$filter,$force);
	}
	
	public function FileMove($dest,$filter=null,$force=true){
		
		return self::Move($this->fullName,$dest,$filter,$force)
	}

    // 读取目录
    // @file 目标文件对象
    // @depth(int) 读取深度 如果为-1则读取所有层级
    // @fn 读取进行时委托 $fn($currentFile)
    // @level 相对深度
    protected function readDir($file,$depth = -1,$fn = null,$level = 1) {

        if ($file->type != self::$FILETYPE['DIRECTORY'])
            return;

        if ($depth === 0) return;

        $hDir = opendir($file->fullName);
        $level = $level + 1;

        while ($hFile = readdir($hDir))
        {
            if ($hFile=='.'||$hFile=='..')
                continue;
            $file->children[$hFile] = new self(self::FixName(array($file->fullName,$hFile)),$depth-1,$fn,$level,$file,$file->fileId);

        }

        closedir($hDir);

    }


	// implements JsonSerializable
    public function jsonSerialize() {

        return array(

		   'id'=>$this->fileId,
		   'directory_id'=>$this->directoryId,
		   'name'=>$this->name,
		   'full_name'=>$this->fullName,
		   'type'=>$this->type,
		   'directory'=>$this->directory ? $this->directory->fullName : null,
		   'children'=>$this->children
	   );

    }

}