<?php
namespace Sepbin\System\Frame\Mvc\Exception;

use Sepbin\System\Core\SepException;

class CacheCantWriteException extends SepException
{
	
	protected $msg = '视图缓存目录不可写';
	
	protected $code = 1007;
	
}