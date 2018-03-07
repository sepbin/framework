<?php
namespace Sepbin\System\Core;

use Sepbin\System\Core\Request;
use Sepbin\System\Core\Hook\IRequestSpotTypeHook;

class RequestSpotTypeDefault implements IRequestSpotTypeHook
{
	
	
	public function spot( string $request_type, \Sepbin\System\Core\Request $request ):string{
		
		
		if(  php_sapi_name() == 'cli'  ){
			
			return Request::REQUEST_TYPE_CONSOLE;
			
		}
		
		if( (isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
				&& strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest') 
					&& $request->getHttpMethod() != Request::REQUEST_HTTP_GET ){
			
			return Request::REQUEST_TYPE_POST;
					
		}
		
		if( !empty($_GET['post']) ){
			
			return Request::REQUEST_TYPE_POST;
			
		}
		
		return Request::REQUEST_TYPE_BROSWER;
		
	}
	
	
}