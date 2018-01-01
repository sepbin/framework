<?php
namespace Sepbin\System\Mvc\Auto;


use Sepbin\System\Mvc\ViewRender;
use Sepbin\System\Mvc\Model;
use Sepbin\System\Http\Request;


/**
 * 渲染时，通过Request的$requestType属性来决定是否返回模板内容
 * 
 * @author joson
 *
 */
class AutoViewRender extends ViewRender
{
	
	
	public function get( Model $model ) {
		
		$data = $this->getModelData($model);
		
		if( getApp()->getRequest()->getRequestType() == Request::REQUEST_TYPE_BROSWER ){
			return $this->getTemplateContent($data);
		}
		
		return $data;
		
	}
	
	
}