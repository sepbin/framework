<?php
namespace Sepbin\System\Core\Exception;

use Sepbin\System\Core\SepException;

class ExtensionException extends SepException
{
	
	protected $msg = '缺少扩展';
	
	protected $code = 1011;
	
}