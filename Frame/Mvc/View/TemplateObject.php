<?php
namespace Sepbin\System\Frame\Mvc\View;

use Sepbin\System\Util\HookRun;
use Sepbin\System\Frame\Hook\IMvcTemplateObjectHook;

class TemplateObject
{
	
	
	private $filename;
	
	
	/**
	 * 
	 * @var TemplateManager
	 */
	private $manage;
	
	private $_module;
	
	private $_controller;
	
	private $_action;
	
	public $_data;
	
	
	function __construct( TemplateManager $manage ,$filename, array $data ){
		
		$this->manage = $manage;
		$this->filename = $filename;
		$this->_data = json_decode( json_encode( $data ) );
		
		$this->_module = $manage->controller->moduleName;
		$this->_controller = $manage->controller->controllerName;
		$this->_action = $manage->controller->actionName;
		
	}
	
	
	
	public function getView(){
		
		HookRun::void(IMvcTemplateObjectHook::class, 'tplObjectInit', $this);
		
		return getApp()->getResponse()->getOut(function(){
			$this->include($this->filename);
		});
		
	}
	
	
	public function include( $file ){
		include $file;
	}
	
	/**
	 * 魔术方法，用于支持模板文件的$this->调用
	 * @param string $name
	 * @return mixed
	 */
	function __get( $name ){
		
		if(!isset($this->_data->$name)){
			trigger_error('模型中不包含'.$name.'属性 '.$this->filename, E_USER_WARNING);
			return '';
		}
		
		return $this->_data->$name;
		
	}
	
	function __set( $name, $value ){
		
		$this->_data->$name = $value;
		
	}
	
	
}