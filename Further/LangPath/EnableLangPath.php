<?php
namespace Sepbin\System\Further\LangPath;

use Sepbin\System\Route\Hook\IRouteHook;
use Sepbin\System\Frame\Hook\ISyntaxHook;



/**
 * 启用url路径语言支持
 * @author joson
 *
 */
class EnableLangPath
{
    
    
    static public function open( array $support_lang ){
        
        
        $lang = new LangPathSupportHook();
        $lang->supportLangs = $support_lang;
        
        getApp()->registerHook( IRouteHook::class, $lang );
        getApp()->registerHook( ISyntaxHook::class , LangPathSyntaxHook::class);
        
    }
    
    
}