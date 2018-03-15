<?php
namespace Sepbin\System\Frame\Exception;

use Sepbin\System\Core\SepException;

class OutputDeniedException extends SepException
{
    
    protected $msg = '拒绝输出';
    
    protected $code = 1013;
    
}