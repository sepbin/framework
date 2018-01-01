<?php
namespace Sepbin\System\Mvc;

use Sepbin\System\Core\Base;

class Model extends Base
{
	
// 	const AUTO_TYPE_JSON = 'json';
	
// 	const AUTO_TYPE_XML = 'xml';
	
// 	/**
// 	 * 模型的自动渲染类型
// 	 * 在不符合自定义渲染的条件下，可以把渲染任务交给框架
// 	 * 框架会按照设置的自动渲染类型渲染model
// 	 * @var string
// 	 */
// 	protected $_auto_data_type = 'json';
	
	private $_data = array();
	
	
// 	public function setAutoDataType( string $type ){
		
// 		$this->_auto_data_type = $type;
		
// 	}
	
// 	public function getAutoDataType():string{
		
// 		return $this->_auto_data_type;
		
// 	}
	
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