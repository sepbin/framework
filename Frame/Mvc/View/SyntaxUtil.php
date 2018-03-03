<?php
namespace Sepbin\System\Frame\Mvc\View;

class SyntaxUtil
{
	
	static public function phpTag( string $str, bool $echo=false ):string{
		
		if($echo) return "<?php echo $str ?>";
		return "<?php $str ?>";
		
	}
	
	
	static public function macroVar( string $str):string{
		
		if( is_numeric($str) ){
			return $str;
		}
		
		if( strtolower($str) == 'true' || strtolower($str) == 'false' ){
			return $str;
		}
		
		if( substr($str, 0, 2) == '$_' ){
			return 'request()->get(\''.substr($str, 2).'\')';
		}
		
		if( substr($str, 0, 1) == '$' ){
			return $str;
		}
		
		
		return "'$str'";
		
	}
	
	
}