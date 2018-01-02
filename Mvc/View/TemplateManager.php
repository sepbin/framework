<?php
namespace Sepbin\System\Mvc\View;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\StringUtil;
use Sepbin\System\Mvc\Exception\TemplateFileNoFoundException;
use Sepbin\System\Mvc\AbsController;
use Sepbin\System\Mvc\Exception\CacheCantWriteException;


/**
 * 模板管理
 * @author joson
 * 
 */
class TemplateManager extends Base
{
	
	/**
	 * 
	 * @var AbsController
	 */
	public $controller;
	
	public $dev = false;
	
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
	 * 使用的语法解析引擎
	 * @var string
	 */
	public $parseEngine = 'Sepbin\System\Mvc\View\Syntax\ArtTemplate';
	
	
	
	
	public $styleDir;
	
	public $filename;
	
	public $layoutFilename;
	
	
	
	/**
	 * 主模板是否已被解析，防止循环<!--#content-->标记
	 * @var string
	 */
	public $isLayoutParse = false;
	
	
	/**
	 * 模型数据
	 * @var array
	 */
	private $data;
	
	
	function __construct( AbsController $controler, string $action, array $data, string $style='Default' ){
		
		$this->controller = $controler;
		
		$this->style = $style;
		
		$this->styleDir = APP_DIR.'/View/'.$this->style;
		
		$this->cacheDir = $this->styleDir.'/Cache';
		
		$this->filename = $this->getFilename( $action );
		
		$this->layoutFilename = $this->styleDir.'/'.$this->layoutName.'.'.$this->extension;
		
		$this->data = $data;
		
	}
	
	
	private function getFilename( string $action_name ) : string {
		
		$controller_name = $this->controller->getModuleName().'/'.$this->controller->getControllerName();
		$filename = $this->styleDir.'/'.$controller_name.'/'.StringUtil::camelToUnderline($action_name).'.'.$this->extension;
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
		
		if(!file_exists($filename)){
			throw (new TemplateFileNoFoundException())->appendMsg( $filename );
		}
		
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
		
		file_put_contents( $cacheFilename , $content);
		
	}
	
	
	
	
	/**
	 * 根据文件储存日期来判断缓存是否过期
	 * @param string $filename
	 */
	private function basisCacheCallParseEngine( string $filename ){
		
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
	public function getContent(){
		
		$this->basisCacheCallParseEngine( $this->layoutFilename );
		
		return getApp()->getResponse()->getOut(function(){
			
			include  $this->getCacheFilename($this->layoutFilename) ;
			
		});
		
	}
	
	
	
	/**
	 * 提供给视图文件使用的 嵌套方法
	 * @param string $filename
	 */
	public function includeContent( string $filename='' ){
		
		if( $filename == '' ){
			$filename = $this->filename;
		}else{
			$filename .= '.'.$this->extension;
		}
		
		$this->basisCacheCallParseEngine( $filename );
		
		include $this->getCacheFilename( $filename );
		
	}
	
	
	/**
	 * 魔术方法，用于支持模板文件的$this->调用
	 * @param string $name
	 * @return mixed
	 */
	function __get( $name ){
		
		if(!isset($this->data[$name])){
			trigger_error('模型中不包含'.$name.'属性 '.$this->filename, E_USER_WARNING);
			return '';	
		}
		
		return $this->data[$name];
		
	}
	
}