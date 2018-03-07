<?php
namespace Sepbin\System\Core;


use Sepbin\System\Util\Traits\TGetType;
use Sepbin\System\Http\UpFile;
use Sepbin\System\Http\UpBase64Image;

class RequestParam
{
	
	use TGetType;
	
	private $param = array();
	
	function __construct( array ...$params ){
		
		if(!empty($params)){
			foreach ($params as $item){
				$this->param = array_merge($this->param, $item);
			}
		}
		
	}
	
	public function appendParam( array $param ){
		
		$this->param = array_merge($this->param, $param);
		
	}
	
	public function get( string $name, $default='' ){
		
		if( isset($this->param[$name]) ){
			return $this->param[$name];
		}
		
		return $default;
		
	}
	
	public function put( string $name, $value ){
		
		$this->param[$name] = $value;
		
	}
	
	
	
	/**
	 * 获取一个文件
	 * @param string $name
	 * @return NULL|\Sepbin\System\Http\UpFile
	 */
	public function getFile( string $name ){
	    
	    if( !isset($_FILES[$name]) ) return null;
	    return new UpFile($_FILES[$name]);
	    
	}
	
	/**
	 * 获取一个通过Base64上传的图片文件
	 * @param string $name
	 */
	public function getBase64Image( string $name ){
	   
	    $content = $this->getStr($name);
	    if( empty($content) ) return null;
	    return new UpBase64Image($content);
	    
	}
	
}