<?php
namespace Sepbin\System\Frame\Mvc\View;

class SyntaxUtil
{
	
    
    
	static public function phpTag( string $str, bool $echo=false ):string{
		
		if($echo) return "<?php echo $str ?>";
		return "<?php $str ?>";
		
	}
	
	
	
	static public function macroVar( $str):string{
	    
	    
	    if( is_array($str) ){
	        $str = array_map( function($val){
	            return self::macroVar($val);
	        } , $str);
	        return '[ '.implode(',', $str).' ]';
	    }
	    
		
		if( is_numeric($str) ){
			return $str;
		}
		
		if( in_array(strtolower($str), ['true','false','null']) ){
			return $str;
		}
		
		
		if( substr($str, 0, 2) == '$_' ){
			return 'request()->get(\''.substr($str, 2).'\')';
		}
		
		if( substr($str, 0, 1) == '$' ){
			return $str;
		}
		
		if( substr($str, 0, 1) == '[' && substr($str, strlen($str)-1) == ']' ){
		    
		    $s = substr($str, 1, strlen($str) - 2 );
		    $s = explode('|', $s);
		    
		    return self::macroVar($s);
		    
		}
		
		
		
		return self::macroString($str);
		
	}
	
	
	
	static public function macroString( string $str ){
	    
	    return "'$str'";
	    
	}
	
	
}