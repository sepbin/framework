<?php
namespace Sepbin\System\Http;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\FactoryConfig;
use Sepbin\System\Util\Factory;

class HttpResponse extends Base implements IFactoryEnable
{
	
	const DATA_TYPE_HTML = 'html';
	
	const DATA_TYPE_XML = 'xml';
	
	const DATA_TYPE_JSON = 'json';
	
	const DATA_TYPE_TEXT = 'txt';
	
	
	/**
	 * mime格式
	 * @var string
	 */
	public $contentType = '';
	
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
	
	
	/**
	 * HTTP协议版本
	 * @var string
	 */
	public $httpVersion = '1.1';
	
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):HttpResponse{
		
		return Factory::get( HttpResponse::class, $config_namespace, $config_file,$config_path );
		
	}
	
	
	
	public function _init( FactoryConfig $config ){
		
		$this->cacheControl = $config->getStr('cache_control','no-cache');
		
		$this->httpVersion = $config->getStr('http_version','1.1');
		
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
	public function addHeader( string $header ){
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
	 * 发送HTTP头
	 */
	public function sendHeader(){
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
			header('HTTP/'.$this->httpVersion.' '.$this->status);
		}
		
	}
	
	
}