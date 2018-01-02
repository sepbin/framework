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
	
	
	
	
	private $tmp = array();
	
	
	function __construct( TemplateManager $manager, string $content ){
		
		$this->content = $content;
		
		$this->manager = $manager;
		
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
		
		
		//解析layout的content标记
		$this->content = preg_replace_callback('/<!--#content-->/', function($matches){
				
			return $this->phpTag(' $this->includeContent() ');
				
		},$this->content);
		
		
		//解析嵌入语法
		$this->content = preg_replace_callback('/<!--#include\s*(.+)?\s*?-->/', function($matches){
			
			$filename = $this->manager->styleDir.'/'.$matches[1];
			$filename = trim($filename);
			
			return "\n".$matches[0]."\n".$this->phpTag('$this->includeContent( \''.$filename.'\' )')."\n\n<!--include end-->\n";
			
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
	
	
	protected function phpTag(string $str):string{
		
		return "<?php $str ?>";
		
	}
	
}