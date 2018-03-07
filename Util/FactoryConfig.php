<?php
namespace Sepbin\System\Util;

use Sepbin\System\Util\Traits\TGetType;
use Sepbin\System\Util\Exception\FactoryTypeException;
use Sepbin\System\Util\Data\ClassName;
use Sepbin\System\Util\Exception\DenySingletonException;

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
	
	/**
	 * 检查一个配置是否存在
	 * @param string $name
	 * @return bool
	 */
	public function check( string $name ) : bool{
		
		return isset($this->config[$name]);
		
	}
	
	/**
	 * 获取当前使用的配置命名
	 * @return string
	 */
	public function getNamespace(){
		
		return $this->namespace;
		
	}
	
	
	public function getInstance( string $name_pre , string $name, $check_type='', bool $allow_pointer = true ){
		
	    $config_name = substr($name, strrpos($name, '\\')+1);
	    $config_name = ClassName::camelToUnderline($config_name);
	    $config_name = $this->namespace.'.'.$name_pre.'_'.$config_name;
	    
	    if( !$allow_pointer && config()->checkPointer( $config_name ) ){
	        throw ( new DenySingletonException() )->appendMsg( $config_name );
	    }
	    
	    $instance = $name::getInstance( $config_name , $this->file, $this->filePath );
		
		if( $check_type != '' && !$instance instanceof $check_type ){
		    throw ( new FactoryTypeException() )->appendMsg( $name.' 必须继承或实现 '. $check_type );
		}
		
		return $instance;
		
	}
	
	
	/**
	 * 获取配置构造的嵌入类实例
	 * @param string $name
	 * @param string $default
	 * @param string $check_type
	 * @return string
	 */
	public function getClass( string $name, $default='', $check_type='', bool $allow_pointer = true ){
	    
	    $conf = $this->get($name,$default);
	    
	    return $this->getInstance($name, $conf, $check_type, $allow_pointer);
	    
	}
	
}