<?php
namespace Sepbin\System\Util;

class StringUtil
{
	
	static public function substrLast( string $str ):string{
		
		return self::substrLastLength($str, 1);
		
	}
	
	static public function substrLastLength( string $str, int $length ):string{
		
		if( empty($str) ) return '';
		
		return substr($str,0,strlen($str)-$length);
		
	}
	
	
	static public function substrFirst( string $str ):string{
		
		return self::substrFirstLength($str, 1);
		
	}
	
	static public function substrFirstLength( string $str, int $length ):string{
		
		if( empty($str) ) return '';
		
		return substr($str, 0, $length);
		
	}
	
	static public function substrLastCharAfter( string $str, string $needle ):string{
		
		if( empty($str) ) return '';
		
		return substr($str, strrpos($str, $needle)+1 );
		
	}
	
	static public function substrFirstCharBefore( string $str, string $needle ): string{
		
		if( empty($str) ) return '';
		
		return substr($str, 0, strpos($str, $needle));
		
	}
	
	
}