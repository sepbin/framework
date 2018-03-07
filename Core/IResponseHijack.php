<?php
namespace Sepbin\System\Core;

interface IResponseHijack
{
    
    
    public function browser( array $buffer );
    
    public function post( array $buffer );
    
    public function console( array $buffer );
    
    
}