<?php
namespace Sepbin\System\Mvc\Exception;

use Sepbin\System\Core\SepException;

class ActionResultErrorException extends SepException
{
	
	protected $msg = '错误的返回类型,ACTION返回的结果必须是Sepbin\System\Mvc\Model类型';
	
	protected $code = 1004;
		
}