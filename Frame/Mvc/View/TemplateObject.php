<?php
namespace Sepbin\System\Frame\Mvc\View;

class TemplateObject
{
	
	private $filename;
	
	/**
	 * 
	 * @var TemplateManager
	 */
	private $manage;
	
	private $_data;
	
	function __construct( TemplateManager $manage ,$filename, array $data ){
		
		$this->manage = $manage;
		$this->filename = $filename;
		$this->_data = $data;
		
	}
	
	
	
	public function getView(){
		
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
		
		if(!isset($this->_data[$name])){
			trigger_error('模型中不包含'.$name.'属性 '.$this->filename, E_USER_WARNING);
			return '';
		}
		
		return $this->_data[$name];
		
	}
	
}