<?php
namespace Sepbin\System\Util\Exception;

use Sepbin\System\Core\SepException;

class InstanceSetTypeException extends SepException
{
	
	
	protected $msg = '实例集合中的实例类型错误';
	
	protected $code = 1001;
	
	
}