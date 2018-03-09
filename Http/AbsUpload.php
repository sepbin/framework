<?php
namespace Sepbin\System\Http;

use Sepbin\System\Util\Data\UniqueName;
use Sepbin\System\Core\Exception\FileCantWriteException;

abstract class AbsUpload
{
    
    
    protected $errorCode = 0;
    
    protected $errorMessage = '';
    
    /**
     * 最大上传大小
     * @var integer
     */
    public $maxSize = 2048;
    
    /**
     * 允许的后缀名
     * @var array
     */
    public $allowExtension = [];
    
    
    
    
    
    /**
     * 储存至磁盘
     * 当不指明$filename时，会自动生成唯一的文件名
     * @param string $path
     * @param string $filename
     * @return boolean|string  失败时会返回false,成功时会返回储存的路径
     */
    public function saveToDisk( string $path, string $filename='' ){
        
        if( !$this->check() ) return false;
        
        if( !is_dir($path) ){
            if( !mkdir( $path, 0777, true ) ){
                throw (new FileCantWriteException())->appendMsg( $path );
            }
        }
        
        if( !is_writeable($path) ){
            throw (new FileCantWriteException())->appendMsg( $path );
        }
        
        if( $filename == '' ){
            $filename = UniqueName::timeBased();
        }
        
        $filename = rtrim($path.'/').'/'.$filename;
        
        if( !$this->moveDisk($filename) ) return false;
        
        return $filename;
        
    }
    
    
    /**
     * 储存到临时目录，并返回键名
     * @return boolean|string 失败时会返回false,成功会返回键名
     */
    public function saveToTemp(){
        
        if( !$this->check() ) return false;
        
        $temp = getTemp();
        $key = $temp->getKey( $this->getExtenstion() );
        
        if( !$this->moveDisk($temp->getFilename($key)) ) return false;
        
        return $key;
        
    }
    
    
    
    public function getErrorCode(){
        
        return $this->errorCode;
        
    }
    
    public function getErrorMessage(){
        
        return $this->errorMessage;
        
    }
    
    private function check(){
        
        if( $this->errorCode != 0 ) return false;
        
        if( $this->maxSize > 0 && $this->getSize() > $this->maxSize ){
            $this->errorCode = 10;
            $this->errorMessage = 'File size beyond '. number_format($this->maxSize/1024, 2 ).'M';
            return false;
        }
        
        if( !empty($this->allowExtension) && !in_array($this->getExtenstion(), $this->allowExtension) ){
            $this->errorCode = 11;
            $this->errorMessage = 'file types that are not allowed to upload';
            return false;
        }
        
        return true;
        
    }
    
    abstract public function getSize() : int;
    abstract public function getExtenstion() : string;
    abstract protected function moveDisk( string $filename ) : bool;
    
}