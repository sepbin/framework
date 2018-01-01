<?php
namespace Sepbin\System\Mvc\Exception;

use Sepbin\System\Core\SepException;

class RenderErrorException extends SepException
{
	
	protected $msg = '错误的渲染器类型,渲染器比如继承至Sepbin\System\Mvc\ViewRender';
	
	protected $code = 1005;
		
}