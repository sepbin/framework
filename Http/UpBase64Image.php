<?php
namespace Sepbin\System\Http;

class UpBase64Image extends AbsUpload
{
    
    private $content;
    
    private $ext;
    
    public $allowExtension = [ 'pjpeg','jpeg','jpg','gif','bmp','png' ];
    
    function __construct( string $base64_str ){
        
        $result = null;
        if(!preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_str, $result)){
            return false;
        }
        
        $this->ext = strtolower( $result[2] );
        $this->content = base64_decode(str_replace($result[1], '', $base64_str));
        
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
        return strlen( $this->base64Str );
    }
    
    
    protected function moveDisk( string $filename ) : bool{
        if( file_put_contents($filename, $this->content) ){
            return true;
        }
        return false;
    }
    
    
    
}