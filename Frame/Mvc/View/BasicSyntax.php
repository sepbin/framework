<?php
namespace Sepbin\System\Frame\Mvc\View;


/**
 * 基础语义解析
 * @author joson
 *
 */
class BasicSyntax
{
	
	/**
	 * 
	 * @var TemplateManager
	 */
	protected $manager;
	
	
	protected $content;
	
	
	private $tmp = array();
	
	
	function __construct( TemplateManager $manager, string $content ){
		
		$this->content = $content;
		
		$this->manager = $manager;
		
	}
	
	
	protected function parse(){}
	
	
	
	private function before(){
		
		//代码注释
		$this->content = preg_replace('/<!--#notes.+?-->/s', '<!--notes code-->', $this->content);
		
		//解析客户端执行
		$key = 0;
		$this->content = preg_replace_callback('/<!--#client\s*-->.*?<!--#client end\s*-->/s', function($matches) use($key){
			$key++;
			$this->tmp[$key] = $matches[0];
			return "<!--#temp$key#-->";
		}, $this->content);
		
		//解析多语言标记
		$this->content = preg_replace_callback('/<t>(.+?)<\/t>/', function($matches){
			
			return SyntaxUtil::phpTag( '$this->manage->controller->_t( \''. addslashes(trim( $matches[1] )).'\' )', true );
			
		}, $this->content);
		
		$this->content = preg_replace_callback('/__(O|M|D)_([\w_]+)\((.*?)\)/', function($matches){
			$method = $matches[1].'_'.$matches[2];
			$params = explode(',', $matches[3]);
			if(!empty($params)){
				$params = array_map(function($val){
					return trim($val);
				}, $params);
			}
			
			$class = 'Sepbin\System\Frame\Mvc\View\Macro\\'.$method;
			if( class_exists($class) ){
				return $class::parse( $this->manager, ...$params );
			}
			
			return '';
		}, $this->content);
		
	}
	
	
	private function after(){
		
		foreach ($this->tmp as $key=>$val){
			$this->content = str_replace("<!--#temp$key#-->", $val, $this->content);
		}
		
	}
	
	public function getContent() : string {
		
		if( empty($this->content) ) return '';
		
		$this->before();
		
		$this->parse();
		
		$this->after();
		
		return $this->content;
		
	}
	
	
}