<?php
namespace Sepbin\System\Db\Exception;

use Sepbin\System\Core\SepException;

class ConnectException extends SepException
{
	
	protected $msg = '连接数据库错误';
	
	protected $code = 1031;
	
}