<?php
namespace Sepbin\System\Util\Traits;

trait TSingleton
{
    
    static public function getInstance( ...$params ){
        
        static $instance;
        
        if( $instance == null ){
            
            $name = static::class;
            $instance = new $name ( ...$params ) ;
            
        }
        
        return $instance;
        
    }
    
}