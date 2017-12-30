<?php
namespace Sepbin\System\Core;

/**
 * 基础类型
 * @author joson
 *
 */
class Base
{
    
    
    public function getString(){
        
        return 'Object#'.get_class($this);
        
    }
    
    public function getClassName(){
        
        return get_class($this);
        
    }
    
//     public function __debugInfo(){
//     	return [
//     			'a' => 'c'
//     	];
//     }
    
    
}