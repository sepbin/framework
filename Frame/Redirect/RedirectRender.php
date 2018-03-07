<?php
namespace Sepbin\System\Frame\Redirect;

use Sepbin\System\Core\Request;
use Sepbin\System\Frame\AbsRender;
use Sepbin\System\Frame\Model;

class RedirectRender extends AbsRender
{
	
	
	public function get( Model $model ){
		
		if( getApp()->request->getRequestType() == Request::REQUEST_TYPE_CONSOLE ){
			
			return getApp()->runRoute($model->host ,$model->redirectUrl );
			
		}
		
		$url = $model->getUrl();
		
		
		if( getApp()->request->getRequestType() == Request::REQUEST_TYPE_BROSWER ){
			
			
			getHttp()->status = $model->httpStatus;
			getHttp()->addHeader('Location: '. $url );
			
			
			return '';
			
		}
		
		
		if( getApp()->request->getRequestType() == Request::REQUEST_TYPE_POST ){
			
			getHttp()->status = $model->httpStatus;
			return $model->getData();
			
		}
		
		return '';
		
		
	}
	
	
}