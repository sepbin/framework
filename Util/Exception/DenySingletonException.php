<?php
namespace Sepbin\System\Util\Exception;

use Sepbin\System\Core\SepException;

class DenySingletonException extends SepException
{
    
    protected $msg = '不允许子类使用单例';
    
    protected $code = 1015;
    
}