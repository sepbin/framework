<?php
namespace Sepbin\System\Cache\Storage;


use Sepbin\System\Util\IFactoryEnable;


abstract class ACache implements IFactoryEnable
{
    
    
    abstract public function set( $key, $value, $expire );
    
    abstract public function call( $name, ...$params );
    
    abstract public function delete( $key );
    
    abstract public function get( $key);
    
    
}