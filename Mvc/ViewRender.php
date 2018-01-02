<?php
namespace Sepbin\System\Mvc;

use Sepbin\System\Mvc\View\Template;
use Sepbin\System\Mvc\View\TemplateManager;

class ViewRender
{
	
	
	protected $controller;
	
	protected $action_name;
	
	
	public function setRouteInfo( AbsController $controller, string $action_name ){
		
		$this->controller = $controller;
		
		$this->action_name = $action_name;
		
	}
	
	
	
	public function get( Model $model ) {
		
		$data = $this->getModelData($model);
		
		return $this->getTemplateContent($data);
		
	}
	
	/**
	 * 把模型转义成原始数据
	 * @param Model $model
	 * @return array
	 */
	protected function getModelData( Model $model ) : array{
		
		$vars = get_object_vars($model);
		$sets = $model->getData();
		
		return array_merge($vars,$sets);
		
	}
	
	
	/**
	 * 获取模板的输出内容
	 * @param array $data
	 * @return string
	 */
	protected function getTemplateContent( array $data ): string {
		
		$template = new TemplateManager($this->controller, $this->action_name, $data);
		
		return $template->getContent();
		
	}
	
}