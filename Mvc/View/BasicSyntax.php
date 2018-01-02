<?php
namespace Sepbin\System\Mvc\View;

class BasicSyntax
{
	
	/**
	 * 
	 * @var TemplateManager
	 */
	protected $manager;
	
	protected $content;
	
	
	
	/**
	 * 嵌入的父级文件
	 * 此属性用于防止include死循环
	 * @var array
	 */
	private $parentFilenames = array();
	
	
	
	private $tmp = array();
	
	
	function __construct( TemplateManager $manager, string $content ){
		
		$this->content = $content;
		
		$this->manager = $manager;
		
	}
	
	
	public function setParentFilenames( array $filenames ){
		$this->parentFilenames = $filenames;
	}
	
	/**
	 * 检查是否包含父级文件
	 * @param string $include_file
	 */
	protected function checkIncludeLoop( string $include_file ){
		
		if( in_array($include_file, $this->parentFilenames) ){
			
			
			trigger_error('陷入include死循环 '.$this->parentFilenames[ count($this->parentFilenames)-1 ].'试图include '.$include_file,E_USER_ERROR);
			return false;
			
		}
		
		return true;
		
	}
	
	
	protected function parse(){}
	
	
	
	
	private function before(){
		
		
		//解析客户端执行
		$key = 0;
		$this->content = preg_replace_callback('/<!--#client\s*-->.*?<!--#client end\s*-->/s', function($matches) use($key){
			$key++;
			$this->tmp[$key] = $matches[0];
			return "<!--#temp$key#-->";
		}, $this->content);
		
		
		//解析嵌入语法
		$this->content = preg_replace_callback('/<!--#include\s*(.+)?\s*?-->/', function($matches){
			
			$filename = $this->manager->styleDir.'/'.$matches[1];
			
			if( !file_exists($filename) ){
				
				trigger_error('没有找到要嵌入的文件 '.$filename, E_USER_ERROR);
				
			}elseif( $this->checkIncludeLoop( $filename ) ){
				
				
				$syntax = new BasicSyntax($this->manager, file_get_contents($filename));
				
				$syntax->setParentFilenames( array_merge( $this->parentFilenames, [$filename] ) );
				
				return $matches[0]."\n".$syntax->getContent()."\n<!--#include end-->";
				
			}
			
			return '\n<!--#include end-->';
			
		}, $this->content);
		
		//解析多语言标记
		$this->content = preg_replace_callback('/<t>(.+?)<\/t>/', function($matches){
			
			return $this->manager->controller->_t( trim($matches[1]) );
			
		}, $this->content);
		
		//代码注释
		$this->content = preg_replace('/<!--#notes.+?-->/s', '<!--notes code-->', $this->content);
		
	}
	
	
	
	private function after(){
		
		foreach ($this->tmp as $key=>$val){
			$this->content = str_replace("<!--#temp$key#-->", $val, $this->content);
		}
		
	}
	
	
	
	public function getContent() : string {
		
		$this->before();
		
		$this->parse();
		
		$this->after();
		
		return $this->content;
		
	}
	
}