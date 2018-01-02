<?php
namespace Sepbin\System\Mvc\Exception;

use Sepbin\System\Core\SepException;

class SyntaxException extends SepException
{
	
	protected $msg = '模板语法错误';
	
	protected $code = 1007;
	
}