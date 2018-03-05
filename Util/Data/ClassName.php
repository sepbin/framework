<?php
namespace Sepbin\System\Util\Data;

/**
 * 类名称规则的相互转换
 * @author joson
 *
 */
class ClassName
{
	
	/**
	 * 骆驼式命名转下划线命名
	 * @param unknown $name
	 * @return string
	 */
	static public function camelToUnderline(string $name):string{
	    
	    $name = lcfirst($name);
	    
 		$tmp = array();
		for ( $i=0; $i<strlen($name);$i++ ){
			$ord = ord($name[$i]);
			if( $ord > 64 && $ord < 91 ){
				$tmp[ $name[$i] ] = '_'.strtolower($name[$i]);
			}
		}
		
		if( count($tmp) > 0 ){
			$name = strtr($name, $tmp);
		}
		
		return $name;
		
	}
	
	/**
	 * 下划线命名转骆驼式命名
	 * @param string $name
	 * @return string
	 */
	static public function underlineToCamel(string $name, bool $ucfirst=false):string{
		
	    if( $ucfirst ) $name = ucfirst($name);
	    
		while ( false != ($pos = strpos($name, '_')) ){
			$char = substr($name, $pos+1,1);
			if( strlen($char) == 1 ){
				$name = str_replace('_'.$char, strtoupper($char), $name);
			}
		}
		
		return $name;
	}
	
}