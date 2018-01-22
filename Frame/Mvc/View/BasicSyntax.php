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
				
			return $this->phpTag( 'echo $this->manage->controller->_t( \''. addslashes(trim( $matches[1] )).'\' )' );
				
		}, $this->content);
		
		$this->content = preg_replace_callback('/__M_([\w_]+)\((.*?)\)/', function($matches){
			$method = 'macro_'.$matches[1];
			$params = explode(',', $matches[2]);
			if(!empty($params)){
				$params = array_map(function($val){
					return trim($val);
				}, $params);
			}
			if( method_exists($this, $method) ){
				return $this->$method(...$params);
			}
			return '';
		}, $this->content);
		
	}
	
	
	private function macro_ACTION( $controller, $action ){
		
		return $this->phpTag(' $this->manage->includeController( '.$this->getVarOrStr($controller).', '.$this->getVarOrStr($action).' ) ');
		
	}
	
	private function macro_VIEW_PATH(){
		
		return $this->manager->stylePath;
		
	}
	
	private function macro_URL( $url='' ){
		
		if( getApp()->httpRewrite ){
			return HTTP_ROOT. $url;
		}
		
		return HTTP_ROOT.'/index.php'. $url;
		
	}
	
	private function macro_INCLUDE( $filename ){
		
		if(empty($filename)) return '';
		
		$fullname = $this->manager->styleDir.'/'.$filename;
		$fullname = trim($fullname);
		
		return "\n<!--include $filename-->\n".$this->phpTag('$this->manage->includeContent( $this, \''.$fullname.'\' )')."\n\n<!--include end-->\n";
	}
	
	
	private function macro_CONTENT(){
		
		return $this->phpTag(' $this->manage->includeContent($this) ');
		
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
	
	private function getVarOrStr( $str ){
		
		if( substr($str, 0, 1) == '$' ){
			return $str;
		}
		
		return "'$str'";
		
	}
	
	protected function phpTag(string $str):string{
		
		return "<?php $str ?>";
		
	}
	
}