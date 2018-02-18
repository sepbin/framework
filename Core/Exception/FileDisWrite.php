<?php
namespace Sepbin\System\Core\Exception;

use Sepbin\System\Core\SepException;

class FileDisWrite extends SepException
{
	
	protected $msg = '文件或目录不可写，请更改权限';
	
	protected $code = 1020;
	
}