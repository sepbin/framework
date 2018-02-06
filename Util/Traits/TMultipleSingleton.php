<?php
namespace Sepbin\System\Util\Traits;

trait TMultipleSingleton
{
	
	static private $_instance = array();
	
	static private function _get( $key, \Closure $callback = null ){
		
		if( !isset(self::$_instance[$key]) ){
			
			$name = get_called_class();
			self::$_instance[$key] = new $name;
			
			if( $callback ){
				$callback( self::$_instance[$key] );
			}
			
		}
		
		return self::$_instance[$key];
		
	}
	
}