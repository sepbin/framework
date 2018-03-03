<?php
namespace Sepbin\System\Util\Data;

class Base62
{

    private static $string = "vPh7zZwA2LyU4bGq5tcVfIMxJi6XaSoK9CNp0OWljYTHQ8REnmu31BrdgeDkFs";
    
    
    static public function encode( float $num ) : string{
        $out = '';   
        for($t=floor(log10($num)/log10(62)); $t>=0; $t--) {  
        	$a = floor($num / pow(62, $t));  
            $out = $out.substr(self::$string, $a, 1);   
            $num = $num - ($a * pow(62, $t));   
        }     
        return $out; 

    }
    
    static public function decode( string $str ) : float{
        
        $out = 0;  
        $len = strlen($str) - 1;  
        for($t=0; $t<=$len; $t++) {  
            $out = $out + strpos(self::$string, substr($str, $t, 1)) * pow(62, $len - $t);  
        }
        return substr(sprintf("%f", $out), 0, -7);  

    }


}