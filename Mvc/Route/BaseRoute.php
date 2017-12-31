<?php
namespace Sepbin\System\Mvc\Route;

use Sepbin\System\Mvc\AbsRoute;
use Sepbin\System\Mvc\AbsController;


/**
 * 基本的路由
 * 这个路由使用GET参数作为数据依据，在服务器未启用rewrite的情况下使用
 * 可在开发时图方便，不折腾服务器
 * @author joson
 *
 */
class BaseRoute extends AbsRoute
{
	
	private $controllerKey = 'controller';
	
	private $actionKey = 'action';
	
	
	public function findController( string $default ):string{
		
		return request()->getStr($this->controllerKey,$default);
		
	}
	
	
	public function findAction( string $default, AbsController $controller ): string{
		
		return request()->getStr($this->actionKey,$default);
		
	}
	
}