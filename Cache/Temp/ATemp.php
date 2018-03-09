<?php
namespace Sepbin\System\Cache\Temp;

use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\Factory;
use Sepbin\System\Util\Data\UniqueName;

abstract class ATemp implements IFactoryEnable
{
    
    
    
    static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ){
        
        return Factory::get( static::class, $config_namespace, $config_file, $config_path );
        
    }
    
    
    public function _init( \Sepbin\System\Util\FactoryConfig $config ){
        
        
        
    }
    
    
    public function createName(){
        
        return UniqueName::timeBased('mdHis');
        
    }
    
    abstract public function write( string $key, string $data='' );
    
    abstract public function read( string $key ) : string ;
    
    abstract public function del( string $key );
    
    abstract public function clean( int $expire );
    
    abstract public function getDiskFilename( $key ) : string;
    
    
}