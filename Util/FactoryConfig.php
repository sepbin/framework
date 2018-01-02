<?php
namespace Sepbin\System\Util;

class FactoryConfig extends AbsGetType
{
		
	private $config;
	
	function __construct( array $config ){
		
		$this->config = $config;
		
	}
	
	
	public function get( string $name, $default='' ){
		
		if( isset($this->config[$name]) ){
			return $this->config[$name];
		}
		
		return $default;
		
	}
	
	public function check( string $name ){
		
		return isset($this->config[$name]);
		
	}
	
	
}