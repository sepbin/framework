<?php
namespace Sepbin\System\Frame\Mvc;

use Sepbin\System\Frame\AbsRender;
use Sepbin\System\Frame\Model;
use Sepbin\System\Core\Request;
use Sepbin\System\Frame\Mvc\View\TemplateManager;

class ViewRender extends AbsRender
{
	
	public function get( Model $model ) {
		
		$data = $model->getData();
		
		if( getApp()->getRequest()->getRequestType() == Request::REQUEST_TYPE_BROSWER ){
			return $this->getTemplateContent($data);
		}
		
		getApp()->getResponse()->setContentType( getApp()->defaultDataFormat );
		
		return $data;
		
	}
	
	
	/**
	 * 获取模板的输出内容
	 * @param array $data
	 * @return string
	 */
	protected function getTemplateContent( array $data ) {
		
		$template = new TemplateManager($this->controller, $this->actionName);
		
		if( $template->checkTemplate() ){
			return $template->getContent( $data );
		}
		
		getApp()->getResponse()->setContentType( 'html' );
		
		return $data;
		
	}
	
}