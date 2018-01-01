<?php
namespace Sepbin\System\Http;

use Sepbin\System\Core\Base;
use Sepbin\System\Core\Application;
use Sepbin\System\Util\Data\ArrayXML;

class Response extends Base
{
	
	public $content_type = 'text/html';
	
	private $buffer = array();
	
	
	function __construct( Application $app ){
		
		ob_start();
		
	}
	
	private function sendHeader(){
		
	}
	
	public function appendHeader(){
		
	}
	
	
	/**
	 * 
	 * @param string|array $buffer
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
				$data['___other_text'] = $otherStr;
			}
			
			if($this->content_type == 'text/xml'){
				echo ArrayXML::arrayToXmlString($data);
			}else{
				echo json_encode($data);
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
		
		ob_flush();
		flush();
		
	}
	
}