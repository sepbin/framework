<?php
namespace Sepbin\System\Frame;

use Sepbin\System\Core\Base;


abstract class AbsRender extends Base
{
	
	protected $controller;
	
	protected $actionName;
	
	/**
	 * 请求方式
	 * @var string
	 */
	public $requestType;
	
	
	function __construct(){
		
		$this->requestType = getApp()->getRequest()->getRequestType();
		
	}
	
	public function setRouteInfo( \Sepbin\System\Frame\AbsController $controller, string $action_name ){
		
		$this->controller = $controller;
		
		$this->actionName = $action_name;
		
	}
	
	abstract public function get( Model $model );
	
}