<?php
namespace Sepbin\System\Mvc\Exception;

use Sepbin\System\Core\SepException;

class TemplateFileNoFoundException extends SepException
{
	
	protected $msg = '模板文件未找到';
	
	protected $code = 1006;
	
}