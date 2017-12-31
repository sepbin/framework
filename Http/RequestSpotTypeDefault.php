<?php
namespace Sepbin\System\Http;

class RequestSpotTypeDefault implements IRequestSpotTypeHook
{
	
	
	public function spot( string $request_type, Request $request ):string{
		
		if( isset($_SERVER['argv']) && isset($_SERVER['argc']) ){
			
			return Request::REQUEST_TYPE_CONSOLE;
			
		}
		
		if( (isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
				&& strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest') 
					&& $request->getHttpMethod() != Request::REQUEST_HTTP_GET ){
			
			return Request::REQUEST_TYPE_POST;
					
		}
		
		return Request::REQUEST_TYPE_BROSWER;
		
	}
	
	
}