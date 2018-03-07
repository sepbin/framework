<?php
namespace Sepbin\System\Util\Data;


/**
 * 点语法的字符串和数组的转换
 * @author joson
 *
 */
class DotName
{
	
	
	static public function get( array $data, string $name, $default='' ){
		
		$names = explode('.', $name);
		
		return self::getDomainArr($names, $data, $default);
		
	}
	
	static public function set( array &$data, string $name, $value ){
		
		$names = explode('.', $name);
		
		return self::setDomainArr($names, $data, $value);
		
	}
	
	
	static private function getDomainArr( $arr, $yarr, $default, $i = 0 ){
		
		if ( !isset($yarr[$arr[$i]]) ){
			return $default;
		}
		
		if( count($arr) - 1 == $i ){
			return $yarr[ $arr[$i] ];
		}
		
		return self::getDomainArr($arr, $yarr[ $arr[$i] ], $default, $i+1	);
		
	}
	
	
	static private function setDomainArr( $arr, &$result, $val, $i = 0 ){
		
		if( count($arr)-1 == $i ){
			$result[$arr[$i]] = $val;
			return $val;
		}else{
			self::setDomainArr($arr, $result[$arr[$i]], $val, $i+1 );
		}
		
	}
	
}