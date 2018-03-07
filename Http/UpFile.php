<?php
namespace Sepbin\System\Http;


use Sepbin\System\Util\FileUtil;

class UpFile extends AbsUpload
{
    
    private $name;
    
    private $ext;
    
    private $fullname;
    
    private $size;
    
    private $tmpname;
    
    
    /**
     * 检查contentType类型
     * @var bool
     */
    public $checkContentType = true;
    
    
    function __construct( array $file_info ){
        
        $this->fullname = $file_info['name'];
        $this->ext = FileUtil::getExtensionName($this->fullname);
        $this->name = FileUtil::getName($this->fullname);
        $this->size = $file_info['size'];
        $this->tmpname = $file_info['tmp_name'];
        $this->errorCode = $file_info['error'];
        
        $this->check();
        
    }
    
    
    /**
     * 获取扩展名
     * {@inheritDoc}
     * @see \Sepbin\System\Http\AbsUpload::getExtenstion()
     */
    public function getExtenstion():string{
        return $this->ext;
    }
    
    /**
     * 获取文件大小
     * {@inheritDoc}
     * @see \Sepbin\System\Http\AbsUpload::getSize()
     */
    public function getSize():int{
        return $this->size;
    }
    
    
    /**
     * 获取名称，不包含扩展名
     * @return string
     */
    public function getName():string{
        return $this->name;
    }
    
    
    /**
     * 获取名称，包含扩展名
     * @return string
     */
    public function getFullname():string{
        return $this->fullname;
    }
    
    
    
    
    
    
    protected function moveDisk( string $filename ) : bool{
        
        if( is_uploaded_file($this->tmpname) ){
            if( move_uploaded_file($this->tmpname, $filename) ){
                return true;
            }
        }
        
        return false;
        
    }
    
    
    
    
    
    private function check(){
        
        switch ( $this->errorCode ){
            
            case UPLOAD_ERR_INI_SIZE:
                $this->errorMessage = 'upload_max_filesize beyond the php.ini settings';
                return ;
                
            case UPLOAD_ERR_FORM_SIZE:
                $this->errorMessage = 'max_file_size beyond the php.ini settings';
                return ;
                
            case UPLOAD_ERR_PARTIAL:
                $this->errorMessage = 'only part of the file is uploaded';
                return ;
                
            case UPLOAD_ERR_NO_FILE:
                $this->errorMessage = 'no files are uploaded';
                return ;
                
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->errorMessage = 'cannot find location for temporary files';
                return ;
                
            case UPLOAD_ERR_CANT_WRITE:
                $this->errorMessage = 'fail to write to file';
                return ;
                
        }
        
        
        
        if( $this->checkContentType ){
            
            if( empty($this->ext) ) return ;
            
            $fi = new \finfo(FILEINFO_MIME_TYPE); 
            $mime = $fi->file($this->tmpname);
            
            if( $mime != MIME::getMimeType($this->ext) ){
                $this->errorCode = 11;
                $this->errorMessage = 'MIME type error';
                return ;
            }
            
        }
        
        
        
    }
    
    
}