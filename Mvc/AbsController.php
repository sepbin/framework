<?php
namespace Sepbin\System\Mvc;

use Sepbin\System\Core\Base;


abstract class AbsController extends Base
{
	
	private $moduleName;
	
	private $controllerName;
	
	protected $lang;
	
	function __construct(){
		
		$controller_name = get_class($this);
		$controller_name = ltrim($controller_name,'SepApp\Application');
		$controller_name = rtrim($controller_name,'Controller');
		
		$controller_name = explode('\\', $controller_name);
		$this->moduleName = $controller_name[0];
		$this->controllerName = $controller_name[1];
		
		$this->setLang();
		
	}
	
	/**
	 * 
	 * @return Model
	 */
	protected function createModel():Model{
		
		return new Model();
		
	}
	
	protected function setLang(){
		
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