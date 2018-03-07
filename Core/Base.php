<?php
namespace Sepbin\System\Core;

/**
 * 基础类型
 * @author joson
 *
 */
class Base
{
    
    
    public function _getString(){
        
        return 'Object#'.get_class($this);
        
    }
    
    public function _getClassName(){
        
        return get_class($this);
        
    }
    
    public function _instanceOf( $name ){
    	
    	return $this instanceof $name;
    	
    }
    
    public function __toString(){
    	
    	return 'object#'.get_class($this);
    	
    }
    
    
}