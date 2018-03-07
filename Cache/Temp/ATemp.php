<?php
namespace Sepbin\System\Cache\Temp;

use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\Factory;
use Sepbin\System\Util\Data\Base62;

abstract class ATemp implements IFactoryEnable
{
    
    
    
    static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ){
        
        return Factory::get( static::class, $config_namespace, $config_file, $config_path );
        
    }
    
    
    public function _init( \Sepbin\System\Util\FactoryConfig $config ){
        
        
        
    }
    
    
    public function createName(){
        
        $date = date('mdHis');
        $date = Base62::encode($date);
        
        $mic = Base62::encode(  intval( floatval(microtime()) * 10000000 ) );
        
        
        $rand = mt_rand(10000,99999);
        $rand = Base62::encode($rand);
        return $date.$mic.$rand;
        
    }
    
    abstract public function write( string $key, string $data='' );
    
    abstract public function read( string $key ) : string ;
    
    abstract public function del( string $key );
    
    abstract public function clean( int $expire );
    
    abstract public function getDiskFilename( $key ) : string;
    
    
}