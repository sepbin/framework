<?php
namespace Sepbin\System\Db\Exception;

use Sepbin\System\Core\SepException;

class SqlException extends SepException
{
	
	protected $msg = 'Sql执行错误';
	
	protected $code = 1010;
	
}