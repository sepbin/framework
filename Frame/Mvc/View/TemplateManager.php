<?php
namespace Sepbin\System\Frame\Mvc\View;

use Sepbin\System\Core\Base;
use Sepbin\System\Frame\Mvc\Exception\TemplateFileNoFoundException;
use Sepbin\System\Frame\AbsController;
use Sepbin\System\Util\HookRun;
use Sepbin\System\Frame\Hook\IMvcTemplateHook;
use Sepbin\System\Frame\Mvc\View\Syntax\ArtTemplate;
use Sepbin\System\Util\Data\ClassName;
use Sepbin\System\Util\Factory;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Frame\Action;
use Sepbin\System\Core\Exception\FileCantWriteException;
use Sepbin\System\Frame\Hook\IMvcTemplateAdvHook;



/**
 * 模板管理
 * @author joson
 * 
 */
class TemplateManager extends Base implements IFactoryEnable
{
	
	/**
	 * 使用模板的控制器
	 * @var AbsController
	 */
	public $controller;
	
	public $action;
	
	
	/**
	 * 开发模式是否开启,如果为true将永远不会缓存
	 * @var string
	 */
	public $dev = true;
	
	
	/**
	 * 模板文件的扩展名
	 * @var string
	 */
	public $extension = 'html';
	
	
	/**
	 * 使用的样式
	 * @var string
	 */
	public $style = '';
	
	
	
	/**
	 * 使用的语法解析引擎
	 * @var string
	 */
	public $parseEngine;
	
	
	/**
	 * 是否忽略模板继承
	 * @var bool
	 */
	public $ignoreParent = false;
	
	
	
	public $styleDir;
	
	public $stylePath;
	
	public $filename;
	
	public $tplFile;
	
	public $isParent = false;
	public $parentFilename = '';
	public $parentModule = '';
	public $parentController = '';
	public $parentAction = '';
	public $parentParams = array();
	public $extendContent = array();
	
	private $langDomain;
	
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):TemplateManager{
		
	    if( $config_namespace == null ) $config_namespace = 'template';
	    return Factory::get( TemplateManager::class, $config_namespace, $config_file, $config_path );
		
	}
	
	public function _init( \Sepbin\System\Util\FactoryConfig $config ){
		
		$this->style = $config->get('style','default');
		$this->styleDir = PUBLIC_DIR.'/view/'.$this->style;
		$this->extension = $config->get('ext_name','html');
		$this->parseEngine = $config->get('parse_engine',ArtTemplate::class);
		$this->cacheDirName = $config->get('cache_dir', DATA_DIR.'/template');
		$this->dev = $config->get('dev',false);
		$this->cacheDirName = rtrim($this->cacheDirName,'/');
		$this->stylePath = HTTP_ROOT.'/view/'.$this->style;
		
		if( !is_dir($this->cacheDirName) ){
		    if( !is_writable( dirname($this->cacheDirName) ) ) throw (new FileCantWriteException())->appendMsg( dirname($this->cacheDirName) );
		    mkdir( $this->cacheDirName, 0777, true );
		}
		
		
		
	}
	
	public function setController( AbsController $controller, string $action ){
		
		$this->controller = $controller;
		$this->action = $action;
		
		
		HookRun::void(IMvcTemplateHook::class, 'tplManagerInit', $this);
		
		$this->tplFile = $this->getControllerTplFile( $this->controller->moduleName, $this->controller->controllerName, $this->action );
		$this->filename = $this->getFilename( $this->tplFile );
		$this->langDomain = 'view_'. ClassName::camelToUnderline( $this->controller->moduleName );
		
		bindtextdomain($this->langDomain , PUBLIC_DIR.'/'.$this->stylePath.'/locale');
		bind_textdomain_codeset( $this->langDomain, getApp()->charset);
		
	}
	
	
	
	/**
	 * 获取一个控制器对应的模板文件路径
	 * @param \Sepbin\System\Frame\AbsController $controller
	 * @param string $action_name
	 * @return string
	 */
	public function getFilename( $tpl_path ) : string {
	    
	    $filename = $this->styleDir.'/'.$tpl_path;
		return realpath($filename);
		
	}
	
	public function getControllerTplFile( string $module_name, string $controller_name, string $action_name ) : string{
	    return ClassName::camelToUnderline( $module_name ).'/'
	        . ClassName::camelToUnderline( $controller_name ).'/'
	        . ClassName::camelToUnderline($action_name).'.'.$this->extension;
	}
	
	
	
	
	/**
	 * 获取文件相对应的缓存文件路径
	 * @param string $filename
	 * @return string
	 */
	private function getCacheFilename( string $filename ) : string{
		
	    $name = str_replace( PUBLIC_DIR.'/view', '', $filename);
		$name = str_replace('.'.$this->extension, '.php', $name);
		
		$name = $this->cacheDirName . $name;
		return $name;
		
	}
	
	
	/**
	 * 调用设置的解析引擎进行模板渲染
	 * @param string $content
	 */
	private function callParseEngine( string $filename ) {
		
		$content = file_get_contents($filename);
		
		/**
		 * @var BasicSyntax $tempEngine
		 */
		$tempEngine = new $this->parseEngine( $this, $content );
		
		$content = $tempEngine->getContent();
		
		$cacheFilename = $this->getCacheFilename($filename);
		$cacheDir = dirname($cacheFilename);
		
		if( !is_dir( $cacheDir ) ){
			mkdir($cacheDir,0777,true);
		}
		
		$content = HookRun::tunnel(IMvcTemplateHook::class, 'tplCacheBefore', $content);
		
		file_put_contents( $cacheFilename , $content);
		
	}
	
	
	
	
	/**
	 * 根据文件储存日期来判断缓存是否过期，过期再执行解析过程
	 * @param string $filename
	 */
	public function basisCacheCallParseEngine( string $filename ){
		
		$cacheFilename = $this->getCacheFilename($filename);
		
		if( $this->dev
		|| !file_exists( $cacheFilename )
		|| filemtime($filename) > filemtime($cacheFilename) ){
			
		    
			$this->callParseEngine( $filename );
			
		}
		
	}
	
	
	/**
	 * 获取内容
	 * @return string
	 */
	public function getContent( array $data) : string{
		
		if(!file_exists($this->filename)){
			throw (new TemplateFileNoFoundException())->appendMsg( $this->filename );
		}
		
		$this->basisCacheCallParseEngine( $this->filename );
		$object = new TemplateObject($this, $this->getCacheFilename($this->filename), $data);
		$content = $object->getView();
		
		
		if( !$this->ignoreParent ){
			while( $this->isParent ){
			    
				if( !HookRun::strict(IMvcTemplateAdvHook::class, 'allowExtends', $this->parentModule, $this->parentController, $this->parentAction) ){
				    foreach ($this->extendContent as $item){
				        putBuffer( $item );
				    }
				    break;
				    
				}
				
				$parentData = array();
				$dispatchClass = 'SepApp\Application\\'.$this->parentModule .'\\'.$this->parentController.'Controller' ;
				
				if( class_exists($dispatchClass) && method_exists($dispatchClass, $this->parentAction.'Action') ){

					$instance = Factory::get($dispatchClass);
					$instance->moduleName = $this->parentModule;
					$instance->controllerName = $this->parentController;
					$instance->actionName = $this->parentAction;
					
					$parentData = call_user_func_array( array($instance, $this->parentAction.'Action'), $this->parentParams );
					$parentData = $parentData->getData();
					
				}
				
				$parentFilename = $this->parentFilename;
				
				$this->basisCacheCallParseEngine( $parentFilename );
				$this->isParent = false;
				$this->parentModule = '';
				$this->parentController = '';
				$this->parentAction = '';
				$this->parentFilename = '';
				$this->parentParams = [];
				
				$object = new TemplateObject($this, $this->getCacheFilename($parentFilename), $parentData);
				$content = $object->getView();
				
			}
		}
		
		$content = HookRun::tunnel(IMvcTemplateHook::class, 'tplViewBefore', $content);
		
		return $content;
		
	}
	
	
	
	/**
	 * 检测模板文件是否存在
	 * @return boolean
	 */
	public function checkTemplate(){
		
		$result = file_exists($this->filename);
		if(!$result){
			trigger_error('不存在的模板文件'.$this->filename, E_USER_ERROR );
		}
		
		return $result;
		
	}
	
	
	
	/**
	 * 提供给视图文件使用的 嵌套方法
	 * @param string $filename
	 */
	public function includeContent( TemplateObject $object ,string $filename='' ){
		
		if( $filename == '' ){
			$filename = $this->filename;
		}else{
			$filename .= '.'.$this->extension;
		}
		
		$this->basisCacheCallParseEngine( $filename );
		
		$object->include( $this->getCacheFilename( $filename ) );
		
	}
	
	
	/**
	 * 嵌入某个控制器的action
	 * @param string $controller_name
	 * @param string $action_name
	 */
	public function includeController( string $module_name, string $controller_name, string $action_name,...$params ){
		
	    $this->ignoreParent = true;
		$dispatchClass = 'SepApp\Application\\'. $module_name .'\\'.$controller_name.'Controller' ;
		/**
		 * 
		 * @var AbsController $controller
		 */
		$controller = Factory::get( $dispatchClass );
		$controller->moduleName = $module_name;
		$controller->controllerName = $controller_name;
		$controller->actionName = $action_name;
		$action = $action_name.'Action';
		$filename = $this->getFilename( $this->getControllerTplFile(  $controller->moduleName, $controller->controllerName, $action_name ) );
		$this->basisCacheCallParseEngine( $filename );
		$object = new TemplateObject($this, $this->getCacheFilename($filename), $controller->$action(...$params)->getData());
		$object->include( $this->getCacheFilename($filename) );
		$this->ignoreParent = false;
		
	}
	
	
	public function putExtendContent( $name, $value, $append=true ){
		
		if(!$append || !isset($this->extendContent[ $name ])){
			$this->extendContent[ $name ] = $value;
		}else{
			$this->extendContent[ $name ] = str_replace('__PARENT__', $value, $this->extendContent[ $name ]);
		}
		
	}
	
	
	public function getExtendContent( $name ){
		
		if(isset( $this->extendContent[ $name ] )) return $this->extendContent[$name];
		
		return '';
		
	}
	
	public function _t( string $message, array $data = [] ){
	    
	    return _lang($message, $this->langDomain, $data);
	    
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