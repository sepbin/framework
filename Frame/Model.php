<?php
namespace Sepbin\System\Frame;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\HookRun;
use Sepbin\System\Frame\Hook\IMvcModelHook;

class Model extends Base
{
	
	private $_data = array();
	
	
	function __construct(){
		
		HookRun::void(IMvcModelHook::class, 'modelCreate', $this);
		
	}
	
	
	/**
	 * 获取模型的原始数据
	 * @return array
	 */
	public function getData() : array{
		
		$r = new \ReflectionClass($this);
		$vars = $r->getProperties(\ReflectionProperty::IS_PUBLIC);
		$data = array();
		foreach ($vars as $item){
			$name = $item->name;
			$data[ $item->name ] = $this->$name;
		}
		return array_merge($data, $this->_data);
		
	}
	
	
	public function __set($name,$value){
		
		$this->_data[$name] = $value;
		
	}
	
	public function __get($name){
		
		if(isset($this->data[$name])) return $this->_data[$name];
			
	}
	
	
	
}