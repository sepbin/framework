<?php
namespace Sepbin\System\Frame\Hook;

use Sepbin\System\Frame\Mvc\View\BasicSyntax;

interface ISyntaxHook
{
    
    public function init( BasicSyntax $syntax );
    
}