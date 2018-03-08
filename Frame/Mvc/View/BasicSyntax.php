<?php
namespace Sepbin\System\Frame\Mvc\View;


use Sepbin\System\Util\HookRun;
use Sepbin\System\Frame\Hook\ISyntaxHook;

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
	
	protected $macro = array();
	
	
	private $tmp = array();
	
	
	function __construct( TemplateManager $manager, string $content ){
		
		$this->content = $content;
		
		$this->manager = $manager;
		
		$this->addMacro(BasicMacro::class);
		
		HookRun::void(ISyntaxHook::class, 'init', $this);
		
	}
	
	
	/**
	 * 增加宏
	 * @param string $name
	 */
	public function addMacro( string $macro_class_name ){
	    
	    $instance = new $macro_class_name( $this->manager );
	    
	    $r = new \ReflectionClass( $instance );
	    $methods = $r->getMethods();
	    
	    foreach ( $methods as $item ){
	        if($item->class == $macro_class_name && in_array( substr($item->name, 0,3) , ['__O','__M','__D']) ){
	            $this->macro[ $item->name ] = $instance;
	        }
	    }
	    
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
			
			return SyntaxUtil::phpTag( '$this->manage->_t( \''. addslashes(trim( $matches[1] )).'\' )', true );
            
		}, $this->content);
		
		$this->content = preg_replace_callback('/__(O|M|D)_([\w_]+)\((.*?)\)/', function($matches){
			$method = $matches[1].'_'.$matches[2];
			$params = explode(',', $matches[3]);
			if(!empty($params)){
				$params = array_map(function($val){
					return trim($val);
				}, $params);
			}
			
			$method = '__'.$method;
			
			if( isset($this->macro[$method]) ){
			    
			    return $this->macro[$method]->$method( ...$params );
			    
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