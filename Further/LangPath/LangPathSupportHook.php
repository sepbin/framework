<?php
namespace Sepbin\System\Further\LangPath;

use Sepbin\System\Route\Hook\IRouteHook;

class LangPathSupportHook implements IRouteHook
{
    
    /**
     * 支持的语言
     * @var array
     */
    public $supportLangs = [];
    
    public function routePath( string $path ) : string{
        
        if(empty($path)) return '';
        
        $dot = strpos($path, '/');
        if( $dot === false ){
            if( $this->check($path) ){
                return '';
            }
        }else{
            
            $first = substr($path, 0, strpos($path, '/') );
            if( $this->check($first) ){
                return substr($path, strpos($path, '/')+1);
            }
            
        }
        
        return $path;
        
    }
    
    private function check( string $str ){
        if( !preg_match('/^([a-z]{2,3}_)([a-z]{2})$/', $str, $matches)  ) return false;
        getApp()->request->requestLang = $matches[1].strtoupper($matches[2]);
        return true;
    }
    
    public function routeHost( string $host ) : string{
        
        return $host;
        
    }
    
}