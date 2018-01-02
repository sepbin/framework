<?php
namespace Sepbin\System\Mvc;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\HookRun;
use Sepbin\System\Mvc\Hook\IMvcModelHook;

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
		
		return $this->_data;
		
	}
	
	
	public function __set($name,$value){
		
		$this->_data[$name] = $value;
		
	}
	
	public function __get($name){
		
		if(isset($this->data[$name])) return $this->_data[$name];
			
	}
	
	
	
}