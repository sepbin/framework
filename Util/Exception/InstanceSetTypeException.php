<?php
namespace Sepbin\Util\Exception;

use Sepbin\Core\SepException;

class InstanceSetTypeException extends SepException
{
	
	
	protected $msg = '实例集合中的实例类型错误';
	
	protected $code = 1001;
	
	
}