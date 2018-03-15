<?php
namespace Sepbin\System\Frame\Exception;

use Sepbin\System\Core\SepException;

class AccessDeniedException extends SepException
{
    
    protected $msg = '拒绝访问';
    
    protected $code = 1012;
    
}