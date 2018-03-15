<?php
namespace Sepbin\System\Further\LangPath;



use Sepbin\System\Frame\Mvc\View\BasicSyntax;
use Sepbin\System\Frame\Hook\ISyntaxHook;

class LangPathSyntaxHook implements ISyntaxHook
{
    
    public function init( BasicSyntax $syntax ){
        
        $syntax->addMacro( LangPathMacro::class );
        
    }
    
}