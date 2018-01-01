<?php
namespace Sepbin\System\Http;

use Sepbin\System\Core\Base;
use Sepbin\System\Core\Application;
use Sepbin\System\Util\Data\ArrayXML;

class Response extends Base
{
	
	
	
	const DATA_TYPE_HTML = 'html';
	
	const DATA_TYPE_XML = 'xml';
	
	const DATA_TYPE_JSON = 'json';
	
	const DATA_TYPE_TEXT = 'txt';
	
	
	private $content_type = 'text/html';
	
	/**
	 * 响应编码
	 * @var string
	 */
	private $charset = 'utf8';
	
	
	/**
	 * 页面缓存方式
	 * private、no-cache、max-age、must-revalidate
	 * @var string
	 */
	private $cacheControl = 'no-cache';
	
	
	/**
	 * 缓存相对超时时间
	 * @var int
	 */
	public $expire;
	
	
	/**
	 * 缓存绝对超时时间
	 * @var int
	 */
	public $exprieAbsolute;
	
	
	/**
	 * 响应状态码
	 * @var integer
	 */
	public $status = 200;
	
	
	private $buffer = array();
	
	
	function __construct( Application $app ){
		
		ob_start();
		
	}
	
	
	/**
	 * 加入HTTP信息头
	 * @param string $header
	 */
	public function addHeader( string $header ):void{
		header($header);
	}
	
	
	/**
	 * 设置文档类型
	 * @param string $ext 类型扩展名
	 */
	public function setContentType( string $ext ){
		
		$this->content_type = ContentType::getMimeType($ext);
		
	}
	
	
	/**
	 * 压入内容到输出缓冲
	 * @param mixed $buffer
	 */
	public function put( $buffer ){
		
		if( !is_string($buffer) && !is_array($buffer) ){
			$buffer = var_export($buffer,true);
		}
		
		$this->buffer[] = $buffer;
		
	}
	
	
	/**
	 * 把回调过程的输出存进输出缓冲，暂不输出
	 * @param \Closure $fun
	 * @return string
	 */
	public function bufferOut(\Closure $func):void{
		ob_start();
		$func();
		$this->put( ob_get_contents() );
		ob_clean();
	}
	
	
	/**
	 * 输出缓冲
	 */
	public function flush():void{
		
		$this->sendHeader();
		if( $this->content_type == 'text/xml' || $this->content_type == 'application/json' ){
			
			$otherStr = null;
			$data = array();
			
			foreach ($this->buffer as $item){
				if( is_array($item) ){
					$data = array_merge( $data, $item );
				}else{
					$otherStr .= $item.' ';
				}
			}
			
			if( $otherStr !== null ){
				$data['__other_text'] = str_replace(array("\n","\t",'&nbsp;'), ' ', strip_tags( $otherStr )) ;
			}
			
			if($this->content_type == 'text/xml'){
				echo ArrayXML::arrayToXmlString($data);
			}else{
				echo json_encode($data,JSON_UNESCAPED_UNICODE);
			}
			
		}elseif ( $this->content_type == 'text/plain' ){
			
			foreach ($this->buffer as $item){
				if(is_string($item)){
					echo strip_tags( $item )." \n";
				}else{
					var_export( $item );
				}
			}
			
		}else{
			
			foreach ($this->buffer as $item){
				if(is_string($item)){
					echo $item." \n";
				}else{
					echo '<pre>';
					var_export($item);
					echo '</pre>';
				}
			}
			
		}
		$this->buffer = array();
		ob_flush();
		flush();
		
	}
	
	
	
	
	/**
	 * 发送HTTP头
	 */
	private function sendHeader(){
		if (isset($this->charset)){
			header("Content-Type:{$this->content_type}; charset={$this->charset}");
		}
		if (isset($this->cacheControl)){
			header("Cache-control:{$this->cacheControl}");
		}
		if (isset($this->expire)){
			header("Expires: " . gmdate("D, d M Y H:i:s",time()+$this->expire) . "GMT");
		}
		if (isset($this->exprieAbsolute)){
			header("Expires: " . gmdate("D, d M Y H:i:s",$this->exprieAbsolute) . "GMT");
		}
		
		if ( isset($this->status) ){
			header('HTTP/1.1 '.$this->status.' '.HttpStatus::getStatus($this->status));
		}
		
	}
	
	
}