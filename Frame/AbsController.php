<?php
namespace Sepbin\System\Frame;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\Factory;
use Sepbin\System\Util\Data\ClassName;

abstract class AbsController extends Base implements IFactoryEnable
{
	
	
	public $moduleName;
	
	public $controllerName;
	
	public $actionName;
	
	public $_isStart = false;
	
	private $langModuleName;
	
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
		
		$this->_isStart = true;
		
	}
	
	
	private function setLang(){
		
	    $this->langModuleName = ClassName::camelToUnderline($this->moduleName);
		bindtextdomain($this->langModuleName, APP_DIR.'/Locale');
		bind_textdomain_codeset($this->langModuleName, getApp()->charset);
		
	}
	
	protected function result( $status, $msg=NULL ){
	    
	    $model = new ResultModel();
	    $model->status = $status;
	    $model->msg = $msg;
	    
	    return $model;
	    
	}
	
	/**
	 * 返回模块域的语言
	 * @param string $message
	 * @param array $data
	 * @return string
	 */
	public function _t( string $message, array $data = [] ):string{
	    return _lang($message, $this->langModuleName, ...$data);
	}
	
}