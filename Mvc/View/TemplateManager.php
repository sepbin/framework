<?php
namespace Sepbin\System\Mvc\View;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\StringUtil;
use Sepbin\System\Mvc\Exception\TemplateFileNoFoundException;
use Sepbin\System\Mvc\AbsController;
use Sepbin\System\Mvc\Exception\CacheCantWriteException;

class TemplateManager extends Base
{
	
	/**
	 * 
	 * @var AbsController
	 */
	public $controller;
	
	
	public $extension = 'html';
	
	
	public $style = 'Default';
	
	
	public $styleDir = '';
	
	public $cacheDir = '';
	
	
	public $parseEngine = 'Sepbin\System\Mvc\View\Syntax\ArtTemplate';
	
	
	public $filename;
	
	public $cacheFilename;
	
	
	private $data;
	
	
	function __construct( AbsController $controler, string $action, array $data, string $style='Default' ){
		
		$this->controller = $controler;
		
		$this->style = $style;
		
		$this->styleDir = APP_DIR.'/View/'.$this->style;
		
		$this->cacheDir = $this->styleDir.'/Cache';
		
		$this->filename = $this->getFilename( $action );
		
		$this->data = $data;
		
		$this->cacheFilename = str_replace( '/'.$this->style.'/' , '/'.$this->style.'/Cache/', $this->filename);
		$this->cacheFilename = str_replace('.'.$this->extension, '.php', $this->cacheFilename);
		
		
	}
	
	
	private function getFilename( string $action_name ){
		
		$controller_name = $this->controller->getModuleName().'/'.$this->controller->getControllerName();
		$filename = $this->styleDir.'/'.$controller_name.'/'.StringUtil::camelToUnderline($action_name).'.'.$this->extension;
		$filename = str_replace('\\', '/', $filename);
		
		return $filename;
		
	}
	
	
	
	
	public function getContent(){
		
		if(!file_exists($this->filename)){
			throw (new TemplateFileNoFoundException())->appendMsg( $this->filename );
		}
		
		$content = file_get_contents($this->filename);
		
		
		if( !file_exists($this->cacheFilename) || filemtime($this->filename) > filemtime($this->cacheFilename) ){
			
			/**
			 * @var BasicSyntax $tempEngine
			 */
			$tempEngine = new $this->parseEngine( $this, $content );
			$tempEngine->setParentFilenames( [$this->filename] );
			
			$content = $tempEngine->getContent();
			
			if( !is_writeable( $this->styleDir ) ){
				throw (new CacheCantWriteException())->appendMsg( $this->styleDir );
			}
			
			if( !is_dir( dirname($this->cacheFilename) ) ){
				mkdir(dirname($this->cacheFilename),0777,true);
			}
			
			file_put_contents($this->cacheFilename, $content);
			
		}
		
		
		
		
		
		return getApp()->getResponse()->getOut(function(){
			include $this->cacheFilename;
		});
		
	}
	
	function __get( $name ){
		
		if(!isset($this->data[$name])){
			
			trigger_error('模型中不包含'.$name.'属性 '.$this->filename, E_USER_WARNING);
			return '';
			
		}
		
		return $this->data[$name];
		
	}
	
	
}