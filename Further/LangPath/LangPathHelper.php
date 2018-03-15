<?php
namespace Sepbin\System\Further\LangPath;

use Sepbin\System\Http\Url;

class LangPathHelper
{
    
    static public function url( string $path = '', $query=[], bool $hold_query = false ){
        
        if( getApp()->request->requestLang != getApp()->defaultLang ){
            $lang = getApp()->request->requestLang;
            $lang = strtolower($lang);
            $path = Url::prePath($path, $lang);
        }
        
        return Url::getUrl( $path, $query, $hold_query );
        
    }
    
}