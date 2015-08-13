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

// php5.3 不支持JsonSerializable
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

        if (!file_exists($fileName))
            return false;

        if (is_dir($fileName))
            return rmdir($fileName);

        if (is_file($fileName))
            return unlink($fileName);

        return false;

    }

    // 遍历当前文件下的所有文件
    public static function Each($fileName,$depth=-1,$fn) {

        return new self($fileName,$depth,$fn);

    }

    // 获取当前文件下的所有文件
    public static function GetAll($fileName,$depth=-1,$fn) {

        $files = array();

        new self($fileName,$depth,function($file) use($fn,& $files) {

            $files []= is_callable($fn) ? $fn($file) : $file;

        });

        return $files;
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

        is_callable($fn) && $fn($this);

        $this->readDir($this,$depth,$fn,$level);

    }

    public function __get($name) {
		
		if(property_exists($this,$name))
			return $this->$name;

        return null;

    }

    public function ReadFile() {

        // throw
        if ($this->type != self::$FILETYPE['FILE'])
            return false;

        return self::Read($this->fullName);

    }

    public function WriteFile($data,$mode='w') {

        if ($this->type != self::$FILETYPE['FILE'])
            return false;

        return self::Write($this->fullName,$data,$mode);

    }

    public function DeleteFile() {

        return self::Delete($this->fullName);

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
            $file->children[$hFile] = new self($file->fullName.'/'.$hFile,$depth-1,$fn,$level,$file,$file->fileId);

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