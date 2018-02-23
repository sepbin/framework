<?php
namespace Sepbin\System\Http;

use Sepbin\System\Core\Base;
use Sepbin\System\Core\Application;
use Sepbin\System\Util\Data\ArrayXML;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\FactoryConfig;
use Sepbin\System\Util\Factory;

class Response extends Base implements IFactoryEnable
{
	
	
	const DATA_TYPE_HTML = 'html';
	
	const DATA_TYPE_XML = 'xml';
	
	const DATA_TYPE_JSON = 'json';
	
	const DATA_TYPE_TEXT = 'txt';
	
	private $contentType = 'text/html';
	
	/**
	 * 响应编码
	 * @var string
	 */
	public $charset = 'utf8';
	
	
	/**
	 * 页面缓存方式
	 * private、no-cache、max-age、must-revalidate
	 * @var string
	 */
	public $cacheControl = 'no-cache';
	
	
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
	
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):Response{
		
		return Factory::get( Response::class, $config_namespace, $config_file,$config_path );
		
	}
	
	
	
	public function _init( FactoryConfig $config ){
		
		$this->cacheControl = $config->getStr('cache_control','no-cache');
		
		$ext = $config->getStr('content_type','html');
		$this->setContentType( $ext );
		
		if($config->check('expire')){
			$this->expire = $config->getInt('expire');
		}
		
		$this->charset = getApp()->charset;
		
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
		
		$this->contentType = MIME::getMimeType($ext);
		
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
		$this->put( $this->getOut($func) );
	}
	
	
	public function getOut(\Closure $func):string{
		ob_start();
		$func();
		$out = ob_get_contents();
		ob_clean();
		return $out;
	}
	
	/**
	 * 输出缓冲
	 */
	public function flush():void{
		
		$this->sendHeader();
		if( $this->contentType == 'text/xml' || $this->contentType == 'application/json' ){
			
			$otherStr = null;
			$data = array();
			
			foreach ($this->buffer as $item){
				if(empty($item)) continue;
				if( is_array($item) ){
					$data = array_merge( $data, $item );
				}else{
					$item = str_replace(array("\n","\t",'&nbsp;'), '', strip_tags( $item )) ;
					$item = preg_replace('/\s+/', ' ', $item);
					$otherStr .= trim($item).' ';
				}
			}
			
			if( $otherStr !== null ){
				$data['__other_text'] = $otherStr ;
			}
			
			if($this->contentType == 'text/xml'){
				echo ArrayXML::arrayToXmlString($data);
			}else{
				echo json_encode($data,JSON_UNESCAPED_UNICODE);
			}
			
		}elseif ( $this->contentType == 'text/plain' ){
			
			foreach ($this->buffer as $item){
				if(is_string($item)){
					echo strip_tags( $item )." \n";
				}else{
					var_export( $item );
				}
			}
			
		}else if ($this->contentType == 'text/html'){
			
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
		    header("Content-Type:{$this->contentType}; charset={$this->charset}");
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