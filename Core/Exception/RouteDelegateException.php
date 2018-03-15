<?php
namespace Sepbin\System\Core\Exception;

use Sepbin\System\Core\SepException;

class RouteDelegateException extends SepException
{
	
	protected $msg = '路由的代理执行类型错误，必须实现IRouteEnable接口';
	
	protected $code = 1006;
	
}