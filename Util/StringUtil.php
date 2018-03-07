<?php
namespace Sepbin\System\Util;

class StringUtil
{
	
    /**
     * 获取字符串的最后一个字符
     * @param string $str
     * @return string
     */
	static public function substrLast( string $str ):string{
		
		return self::substrLastLength($str, 1);
		
	}
	
	/**
	 * 获取字符串最后一个长度的字符
	 * @param string $str
	 * @param int $length
	 * @return string
	 */
	static public function substrLastLength( string $str, int $length ):string{
		
		if( empty($str) ) return '';
		
		return \mb_substr($str,\mb_strlen($str)-$length);
		
	}
	
	
	
	/**
	 * 获取字符串第一个字符
	 * @param string $str
	 * @return string
	 */
	static public function substrFirst( string $str ):string{
		
		return self::substrFirstLength($str, 1);
		
	}
	
	
	/**
	 * 获取字符串一个长度的字符
	 * @param string $str
	 * @param int $length
	 * @return string
	 */
	static public function substrFirstLength( string $str, int $length ):string{
		
		if( empty($str) ) return '';
		
		return \mb_substr($str, 0, $length);
		
	}
	
	
	/**
	 * 从最后搜索到的某个字符开始，获取之后的字符串
	 * @param string $str
	 * @param string $needle
	 * @return string
	 */
	static public function substrLastCharAfter( string $str, string $needle ):string{
		
		if( empty($str) ) return '';
		$dot = \mb_strrpos($str, $needle);
		if( $dot === false ) return '';
		
		return \mb_substr($str, $dot+1 );
		
	}
	
	
	/**
	 * 从最后搜索到的某个字符开始，获取之前的字符串
	 * @param string $str
	 * @param string $needle
	 * @return string
	 */
	static public function substrLastCharBefore( string $str, string $needle ): string{
	    
	    if( empty($str) ) return '';
	    $dot = \mb_strrpos($str, $needle);
	    if( $dot === false ) return $str;
	    return \mb_substr($str, 0, $dot);
	}
	
	
	/**
	 * 从第一个搜索到的某个字符开始，获取之前的字符串
	 * @param string $str
	 * @param string $needle
	 * @return string
	 */
	static public function substrFirstCharBefore( string $str, string $needle ): string{
		
		if( empty($str) ) return '';
		$dot = \mb_strpos($str, $needle);
		if($dot === false) $str;
		return \mb_substr($str, 0, $dot);
		
	}
	
	
}