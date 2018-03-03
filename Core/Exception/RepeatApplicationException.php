<?php
namespace Sepbin\System\Core\Exception;

use Sepbin\System\Core\SepException;

class RepeatApplicationException extends SepException
{
    
    protected $msg = '重复的Application实例';
    
    protected $code = 1099;
    
}