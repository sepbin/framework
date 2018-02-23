<?php
namespace Sepbin\System\Util;

use Sepbin\System\Core\Base;

/**
 * 单例管理器
 * @author joson
 *
 */
class InstanceManager extends Base
{
	
	private $instances = array();
	
	
	
	static public function getInstance() : InstanceManager {
		
		static $instance = null;
		
		if( $instance == null ){
			$instance = new InstanceManager();
		}
		
		return $instance;
		
	}
	
	/**
	 * 获取类的单例
	 * @param string $class_name 类名称
	 */
	public function get( string $class_name ){
		
		if( !isset( $this->instances[$class_name] ) ){
			
			$this->instances[$class_name] = new $class_name;
			
		}
		
		return $this->instances[$class_name];
		
	}
	
}