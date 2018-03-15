<?php
namespace Sepbin\System\Util\Exception;

use Sepbin\System\Core\SepException;

class FactoryTypeException extends SepException
{
	
	protected $msg = '工厂接受的类型错误，要生产的类型必须实现IFactoryEnable接口';
	
	protected $code = 1023;
	
}