<?php
namespace Sepbin\System\Frame\Mvc;

use Sepbin\System\Frame\AbsRender;
use Sepbin\System\Frame\Model;
use Sepbin\System\Core\Request;
use Sepbin\System\Frame\Mvc\View\TemplateManager;
use Sepbin\System\Frame\ResultModel;

class ViewRender extends AbsRender
{
	
	public function get( Model $model ) {
		
		$data = $model->getData();
		
		if( !$model instanceof ResultModel ){
    		if( $this->requestType == Request::REQUEST_TYPE_BROSWER ){
    			return $this->getTemplateContent($data);
    		}
		}
		
		if( $this->requestType == Request::REQUEST_TYPE_CONSOLE ){
			return $data;
		}
		
		return $data;
		
	}
	
	
	/**
	 * 获取模板的输出内容
	 * @param array $data
	 * @return string
	 */
	protected function getTemplateContent( array $data ) {
		
		$template = TemplateManager::getInstance();
		$template->setController($this->controller, $this->actionName);
		
		if( $template->checkTemplate() ){
		    $this->responseFormat = 'html';
			return $template->getContent( $data );
		}
		
		return $data;
		
	}
	
}