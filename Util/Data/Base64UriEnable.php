<?php
namespace Sepbin\System\Util\Data;

class Base64UriEnable
{
	
	static public function encode( string $data ):string{
		
		return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode(serialize($data)));
		
	}
	
	static public function decode( string $string ):string{
		
		$data = str_replace(array('-', '_'), array('+', '/'), $string);
		$mod4 = strlen($data) % 4;
		($mod4) && $data .= substr('====', $mod4);
		return unserialize(base64_decode($data));
		
	}
	
}