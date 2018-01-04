<?php
namespace Sepbin\System\Mvc;

use Sepbin\System\Core\Base;


abstract class AbsController extends Base
{
	
	public $moduleName;
	
	public $controllerName;
	
	protected $lang;
	
	function __construct(){
		
		$this->setLang();
		
	}
	
	/**
	 * 
	 * @return Model
	 */
	protected function createModel():Model{
		
		return new Model();
		
	}
	
	
	private function setLang(){
		
		bindtextdomain($this->getModuleName(), APP_DIR.'/Locale');
		bind_textdomain_codeset($this->getModuleName(), getApp()->charset);
		
	}
	
	
	/**
	 * 获取模块名称
	 */
	public function getModuleName(){
		
		return $this->controllerName;
		
	}
	
	/**
	 * 获取控制器名称
	 * @return mixed
	 */
	public function getControllerName(){
		
		return $this->controllerName;
		
	}
	
	
	/**
	 * 返回模块域的语言
	 * @param string $message
	 * @param array $data
	 * @return string
	 */
	public function _t( string $message, array $data = array() ):string{
		
		return _lang($message, $this->getModuleName(), ...$data);
		
	}
	
}