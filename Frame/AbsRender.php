<?php
namespace Sepbin\System\Frame;

use Sepbin\System\Core\Base;

abstract class AbsRender extends Base
{
	
	protected $controller;
	
	protected $actionName;
	
	
	public function setRouteInfo( \Sepbin\System\Frame\AbsController $controller, string $action_name ){
		
		$this->controller = $controller;
		
		$this->actionName = $action_name;
		
	}
	
	abstract public function get( Model $model );
	
}