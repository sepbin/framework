<?php
namespace Sepbin\System\Util;

class FileUtil
{
	
	
	
	static public function getExtensionName( string $filename ) : string {
		
		return StringUtil::substrLastCharAfter($filename, '.');
		
	}
	
	
	
	static public function combineFullName( string $filename, string $path ) : string {
		
		if( StringUtil::substrLast($path) == '/' ){
			
			return $path. ltrim( $filename, '/' );
			
		}else{
			
			return $path. '/'. ltrim($filename,'/');
			
		}
		
	}
	
}