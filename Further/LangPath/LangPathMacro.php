<?php
namespace Sepbin\System\Further\LangPath;

use Sepbin\System\Frame\Mvc\View\AbsMacro;
use Sepbin\System\Frame\Mvc\View\SyntaxUtil;

class LangPathMacro extends AbsMacro
{
    
    
    public function __O_URL( $path = '', string $query='', bool $hold_query=false ){
        
        
        return SyntaxUtil::phpTag( ' \Sepbin\System\Further\LangPath\LangPathHelper::url(
                '.SyntaxUtil::macroVar($path).',
                '.SyntaxUtil::macroVar($query).',
                '.SyntaxUtil::macroVar($hold_query).' ) ', true );
        
    }
    
    public function __O_LANG_URL( $lang_code ){
        if( $lang_code == getApp()->defaultLang ){
            $lang_code = '';
        }else{
            $lang_code = strtolower($lang_code);
        }
        
        return SyntaxUtil::phpTag( ' \Sepbin\System\Http\Url::getUrl(
                \Sepbin\System\Http\Url::prePath( \Sepbin\System\Http\Url::getPath(), '.SyntaxUtil::macroVar($lang_code).' ),
                '.SyntaxUtil::macroString('').',
                true ) ', true);
        
    }
    
    
}