<?php
namespace Sepbin\System\Core;

abstract class AbsExceptionListen
{
    
    public $errno;
    
    public $errstr;
    
    public $errfile;
    
    public $errline;
    
    
    abstract public function do();
    
}