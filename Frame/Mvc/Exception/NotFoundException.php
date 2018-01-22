<?php
namespace Sepbin\System\Frame\Mvc\Exception;

use Sepbin\System\Core\SepException;

class NotFoundException extends SepException
{
	
	protected $msg = '没有找到派遣对象';
	
	protected $code = 404;
	
}