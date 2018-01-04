<?php
namespace Sepbin\System\Core\Exception;

use Sepbin\System\Core\SepException;

class NotFoundException extends SepException
{
	
	protected $msg = '没有找到路由';
	
	protected $code = 404;
	
}