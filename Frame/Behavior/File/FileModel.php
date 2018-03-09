<?php
namespace Sepbin\System\Frame\Behavior\File;

use Sepbin\System\Frame\Model;
use Sepbin\System\Util\FileUtil;

class FileModel extends Model
{
    
    
    /**
     * 文件路径
     * 文件存在服务器的实际路径
     * @var string
     */
    public $filename;
    
    /**
     * 文件名称
     * 用户下载时的文件名称
     * @var unknown
     */
    public $name;
    
    
    
    /**
     * 通过内容构造Model
     * @param string $name
     * @param string $data
     */
    static public function content( string $name, string $data ){
        
        $ext = FileUtil::getExtensionName($name);
        $tmp = getTemp();
        $filename = $tmp->write($data, $ext);
        
        $model = new FileModel();
        $model->name = $name;
        $model->filename = $tmp->getFilename( $filename );
        return $model;
        
    }
    
    
    /**
     * 通过文件构造Model
     * @param string $name
     * @param string $filename
     */
    static public function file( string $name, string $filename ){
        
        $model = new FileModel();
        $model->filename = $filename;
        $model->name = $name;
        return $model;
        
    }
    
}