<?php
namespace Sepbin\System\Http\Exception;

use Sepbin\System\Core\SepException;

class SessionHandlerError extends SepException
{
    
    protected $msg = 'Session handler的类型错误，必须继承至 \SessionHandler';
    
    protected $code = 1025;
    
}