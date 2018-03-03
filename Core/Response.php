<?php
namespace Sepbin\System\Core;

use Sepbin\System\Http\HttpResponse;
use Sepbin\System\Util\Data\ArrayXML;

class Response extends Base
{
	
	private $buffer = [];
	
	
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
	public function bufferOut(\Closure $func){
		
		$this->put( $this->getOut($func) );
		
	}
	
	
	/**
	 * 获取回调过程的输出
	 * @param \Closure $func
	 * @return string
	 */
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
	public function flush(){
		
		if( getApp()->request->getRequestType() == Request::REQUEST_TYPE_CONSOLE ){
			
			foreach ($this->buffer as $item){
				if( is_string($item) ){
					echo strip_tags( $item )." \n";
				}else{
					var_export( $item );
				}
			}
			
			return ;
			
		}
		
		$http = HttpResponse::getInstance('http');
		
		if( getApp()->request->getRequestType() == Request::REQUEST_TYPE_POST ){
			
			$http->setContentType( getApp()->defaultDataFormat );
			
			$otherStr = null;
			$data = array();
			
			foreach ($this->buffer as $item){
				if(empty($item)) continue;
				if( is_array($item) ){
					$data = array_merge( $data, $item );
				}else{
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
			
		}
		
		if( getApp()->request->getRequestType() == Request::REQUEST_TYPE_BROSWER ){
			$http->setContentType('html');
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
		
		
	}
	
}