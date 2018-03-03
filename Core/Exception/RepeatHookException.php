<?php
namespace Sepbin\System\Core\Exception;

use Sepbin\System\Core\SepException;

class RepeatHookException extends SepException
{
	
	protected $msg = '重复的HOOK';
	
	protected $code = 1002;
	
}