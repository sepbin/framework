<?php
namespace Sepbin\System\Util;

class FactoryConfig extends AbsGetType
{
		
	private $config;
	
	private $namespace;
	
	function __construct( string $namespace, array $config ){
		
		$this->config = $config;
		
		$this->namespace = $namespace;
		
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
	
	
	public function getNamespace(){
		return $this->namespace;
	}
	
}