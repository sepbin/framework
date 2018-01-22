<?php
namespace Sepbin\System\Util;

use Sepbin\System\Util\Traits\TGetType;

class FactoryConfig
{
	
	use TGetType;
	
	private $config;
	
	private $namespace;
	
	public $file;
	
	public $filePath;
	
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
	
	
	public function getInstance( string $property , string $name ){
		
		return $name::getInstance( $this->namespace.'.'.$property.'_'.strtolower( substr($name, strrpos($name, '\\')+1) ), $this->file, $this->filePath );
		
	}
	
}