<?php
namespace Sepbin\System\Core;

use Sepbin\System\Core\Exception\NotTypeException;

class Response extends Base
{
    
    /**
     * 
     * @var string
     */
    public $out = ResponseOutDefault::class;
    
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
	    
	    /**
	     * 
	     * @var IResponseHijack $out
	     */
	    if( is_string($this->out) ){
	        $out = new $this->out;
	    }else{
	        $out = $this->out;
	    }
	    
	    if( !$out instanceof IResponseHijack ){
	        throw (new NotTypeException())->appendMsg( get_class($out).' need IResponseHijack' );
	    }
	    
		if( getApp()->request->getRequestType() == Request::REQUEST_TYPE_CONSOLE ){
		    $out->console($this->buffer);
		}
		
		if( getApp()->request->getRequestType() == Request::REQUEST_TYPE_POST ){
			$out->post($this->buffer);
		}
		
		if( getApp()->request->getRequestType() == Request::REQUEST_TYPE_BROSWER ){
		    $out->browser($this->buffer);		    
		}
		
		
	}
	
}