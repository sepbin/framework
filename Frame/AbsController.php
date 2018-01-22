<?php
namespace Sepbin\System\Frame;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\Factory;

abstract class AbsController extends Base implements IFactoryEnable
{
	
	
	public $moduleName;
	
	public $controllerName;
	
	public $actionName;
	
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ){
		
		return Factory::get(static::class, $config_namespace, $config_file, $config_path);
		
	}
	
	public function _init( \Sepbin\System\Util\FactoryConfig $config ){
		
		
	}
	
	/**
	 * 初始化
	 */
	public function _start(){
		
		$this->setLang();
		
	}
	
	public function _end(){
		
	}
	
	
	private function setLang(){
		
		bindtextdomain($this->moduleName, APP_DIR.'/Locale');
		bind_textdomain_codeset($this->moduleName, getApp()->charset);
		
	}
	
	/**
	 * 返回模块域的语言
	 * @param string $message
	 * @param array $data
	 * @return string
	 */
	public function _t( string $message, array $data = array() ):string{
		return _lang($message, $this->moduleName, ...$data);
	}
	
}