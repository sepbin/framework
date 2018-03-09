<?php
namespace Sepbin\System\Frame\Exception;
use Sepbin\System\Core\SepException;

class ConsoleException extends SepException
{
    
    protected $msg = 'error';
    
    protected $code = -1;
    
}