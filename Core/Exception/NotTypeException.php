<?php
namespace Sepbin\System\Core\Exception;

use Sepbin\System\Core\SepException;

class NotTypeException extends SepException
{
    
    protected $msg = '不是期待的类型';
    
    protected $code = 1003;
    
}