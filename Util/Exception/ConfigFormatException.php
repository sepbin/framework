<?php
namespace Sepbin\System\Util\Exception;

use Sepbin\System\Core\SepException;

class ConfigFormatException extends SepException
{
	
	protected $msg = '获取配置的格式错误';
	
	protected $code = 1021;
	
}