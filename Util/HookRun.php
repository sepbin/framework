<?php
namespace Sepbin\System\Util;

class HookRun
{
	
	
	static public function void( string $name, string $method_name, ...$params ){
		
		getApp()->hook($name, $method_name, InstanceSet::CALL_VOID, ...$params);
		
	}
	
	
	static public function tunnel( string $name, string $method_name, ...$params ){
		
		return getApp()->hook($name, $method_name, InstanceSet::CALL_TUNNEL, ...$params);
		
	}
	
	static public function array( string $name, string $method_name, ...$params ) : array{
		
		return getApp()->hook($name, $method_name, InstanceSet::CALL_ARRAY, ...$params);
		
	}
	
	static public function strict( string $name, string $method_name, ...$params  ):bool{
		
		return getApp()->hook($name, $method_name, InstanceSet::CALL_BOOL_STRICT, ...$params);
		
	}
	
}