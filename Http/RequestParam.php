<?php
namespace Sepbin\System\Http;

use Sepbin\System\Util\AbsGetType;

class RequestParam extends AbsGetType
{
	
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
	
	
}