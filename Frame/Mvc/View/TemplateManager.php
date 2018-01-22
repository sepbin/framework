<?php
namespace Sepbin\System\Frame\Mvc\View;

use Sepbin\System\Core\Base;
use Sepbin\System\Frame\Mvc\Exception\TemplateFileNoFoundException;
use Sepbin\System\Frame\AbsController;
use Sepbin\System\Frame\Mvc\Exception\CacheCantWriteException;
use Sepbin\System\Util\HookRun;
use Sepbin\System\Frame\Hook\IMvcTemplateHook;
use Sepbin\System\Frame\Mvc\View\Syntax\ArtTemplate;
use Sepbin\System\Util\Data\ClassName;
use Sepbin\System\Util\Factory;


/**
 * 模板管理
 * @author joson
 * 
 */
class TemplateManager extends Base
{
	
	/**
	 * 使用模板的控制器
	 * @var AbsController
	 */
	public $controller;
	
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
	public $style = 'Default';
	
	
	/**
	 * 使用的layout名称
	 * @var string
	 */
	public $layoutName = 'layout';
	
	
	/**
	 * 缓存目录名称
	 * @var string
	 */
	public $cacheDirName = 'Cache';
	
	
	/**
	 * 使用的语法解析引擎
	 * @var string
	 */
	public $parseEngine = ArtTemplate::class;
	
	
	/**
	 * 是否使用layout
	 * @var bool
	 */
	public $isLayout = true;
	
	
	
	public $styleDir;
	
	public $stylePath;
	
	public $filename;
	
	public $layoutFilename;
	
	
	/**
	 * 主模板是否已被解析，防止循环<!--#content-->标记
	 * @var string
	 */
	public $isLayoutParse = false;
	
	
	
	function __construct( \Sepbin\System\Frame\AbsController $controller, string $action, string $style='Default' ){
		
		HookRun::void(IMvcTemplateHook::class, 'tplManagerInit', $this);
		
		$this->controller = $controller;
		
		$this->style = $style;
		
		$this->styleDir = APP_DIR.'/View/'.$this->style;
		
		$this->stylePath = HTTP_ROOT.'/application/View/'.$this->style;
		
		$this->cacheDir = $this->styleDir.'/'.$this->cacheDirName;
		
		$this->filename = $this->getFilename( $controller, $action );
		
		$this->layoutFilename = $this->styleDir.'/'.$this->layoutName.'.'.$this->extension;
		
	}
	
	
	private function getFilename( \Sepbin\System\Frame\AbsController $controller, string $action_name ) : string {
		
		$controller_name = $controller->moduleName.'/'.$controller->controllerName;
		$filename = $this->styleDir.'/'.$controller_name.'/'.ClassName::camelToUnderline($action_name).'.'.$this->extension;
		$filename = str_replace('\\', '/', $filename);
		
		return $filename;
		
	}
	
	
	
	private function getCacheFilename( string $filename ) : string{
		
		$filename = str_replace( '/'.$this->style.'/' , '/'.$this->style.'/Cache/', $filename);
		
		return str_replace('.'.$this->extension, '.php', $filename);
		
	}
	
	
	/**
	 * 调用设置的解析引擎进行模板渲染
	 * @param string $content
	 */
	private function callParseEngine( string $filename ) : void{
		
		$content = file_get_contents($filename);
		
		/**
		 * @var BasicSyntax $tempEngine
		 */
		$tempEngine = new $this->parseEngine( $this, $content );
		
		$content = $tempEngine->getContent();
		
		if( !is_writeable( $this->styleDir ) ){
			throw (new CacheCantWriteException())->appendMsg( $this->styleDir );
		}
		
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
	public function getContent( array $data){
		
		if(!file_exists($this->filename)){
			throw (new TemplateFileNoFoundException())->appendMsg( $this->filename );
		}
		
		if( $this->isLayout && file_exists($this->layoutFilename) ){
			
			$this->basisCacheCallParseEngine( $this->layoutFilename );
			
			$object = new TemplateObject($this, $this->getCacheFilename($this->layoutFilename), $data);
			
			return $object->getView();
			
		}else{
			
			trigger_error('缺少layout文件'.$this->layoutFilename, E_USER_ERROR);
			
			$this->basisCacheCallParseEngine( $this->filename );
			
			$object = new TemplateObject($this, $this->getCacheFilename($this->filename), $data);
			
			return $object->getView();
			
		}
		
		
	}
	
	
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
	
	
	public function includeController( string $controller_name, string $action_name ){
		
		$controller = Factory::get($controller_name);
		$action = $action_name.'Action';
		
		$filename = $this->getFilename($controller, $action_name);
		$this->basisCacheCallParseEngine( $filename );
		$object = new TemplateObject($this, $this->getCacheFilename($filename), $controller->$action()->getData());
		
		$object->include( $this->getCacheFilename($filename) );
		
// 		$c = $this->getContent( $controller->$action()->getData(), false );
// 		var_dump($c);
// 		var_dump($controller->$action());
		
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