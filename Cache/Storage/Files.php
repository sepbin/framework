<?php
namespace Sepbin\System\Cache\Storage;

use Sepbin\System\Util\Factory;
use Sepbin\System\Core\Exception\FileCantWriteException;

class Files extends ACache
{
    
    
    private $saveDir;
    
    
    static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):Files{
        
        return Factory::get(Files::class, $config_namespace, $config_file, $config_path);
        
    }
    
    
    public function _init( \Sepbin\System\Util\FactoryConfig $config ){
        
        $this->saveDir = $config->getStr('save_path', DATA_DIR.'/cache');
        
        if( !is_dir($this->saveDir) ){
            if( !is_writable( dirname($this->saveDir) ) ){
                 throw (new FileCantWriteException())->appendMsg( dirname($this->saveDir) );
            }
            mkdir($this->saveDir,0755,true);
        }
        
    }
    
    public function set( $key, $value, $expire ){
        
        $str = '<?php ';
        
        $str.= 'return [';
        
        $str.= ' \'expire\' =>  '.$expire.',';
        
        $data = serialize($value);
        $data = addcslashes($data,"'");
        
        $str.= ' \'data\' => \''.$data.'\' ';
        
        $str.= ']';
        
        $str.= '?>';
        
        $filename = $this->getFilename($key);
        
        $result = file_put_contents( $filename , $str);
        
        return $result === false?false:true;
        
    }
    
    public function call( $name, ...$params ){
        
        return $this->$name(...$params);
        
    }
    
    public function delete( $key ){
        
        return @unlink( $this->getFilename($key) );
        
    }
    
    
    public function get( $key ){
        
        if( !file_exists( $this->getFilename($key) ) ){
            return false;
        }
        
        $savetime = filemtime( $this->getFilename($key) );
        
        $datas = include $this->getFilename($key);
        
        $expire = $datas['expire'];
        
        $data = unserialize( $datas['data'] );
        
        if( $expire!=0 && time() > $savetime+$expire ){
            @unlink( $this->getFilename($key) );
            return null;
        }
        
        return $data;
        
    }
    
    private function getFilename( $key ){
        
        return $this->saveDir.'/'.$key.'.php';
        
    }
    
}