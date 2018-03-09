<?php
namespace Sepbin\System\Cache\Temp;

use Sepbin\System\Core\Exception\FileCantWriteException;

class LocaleFile extends ATemp
{
    
    private $tmpPath;
    
    
    public function _init( \Sepbin\System\Util\FactoryConfig $config ){
        
        $this->tmpPath = $config->getStr('tmp_path', DOCUMENT_ROOT.'/data/tmp');
        
        if( !is_dir($this->tmpPath) ){
            
            if( !mkdir($this->tmpPath,0777,true) ){
                throw (new FileCantWriteException())->appendMsg($this->tmpPath);
            }
            
        }
        
        if( !is_writeable($this->tmpPath) ){
            
            throw (new FileCantWriteException())->appendMsg($this->tmpPath);
            
        }
        
    }
    
    public function write( string $key, string $data='' ){
        
        file_put_contents( $this->getFilename($key) , $data);
        
    }
    
    public function read( string $key ) : string {
        
        return file_get_contents( $this->getFilename($key) );
        
    }
    
    
    public function del( string $key ){
        
        @unlink( $this->getFilename($key) );
        
    }
    
    public function getDiskFilename( $key ) : string{
        
        return $this->tmpPath.'/'.$key;
        
    }
    
    public function clean( int $expire ){
        
        $time = time();
        $dir = dir($this->tmpPath);
        
        while ( false != ($file = $dir->read()) ){
            if( $file != '.' && $file != '..' ){
                
                $filename = $this->tmpPath.'/'.$file;
                if( filemtime( $filename ) + $expire < $time ){
                    
                    @unlink( $filename );
                    
                }
                
            }
        }
        
        $dir->close();
        
    }
    
    
    private function getFilename( $key ) : string{
        return $this->tmpPath.'/'.$key;
    }
    
}