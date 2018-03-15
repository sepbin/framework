<?php
namespace Sepbin\System\Further\AjaxGetStopExtends;

use Sepbin\System\Frame\Hook\IMvcTemplateAdvHook;
use Sepbin\System\Core\Request;

class AjaxGetStopExtendsHook implements IMvcTemplateAdvHook
{
    
    
    public function allowExtends( string $module, string $controller, string $action ):bool{
        
        
        
        
        if( (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest')
            && getApp()->request->getHttpMethod() == Request::REQUEST_HTTP_GET ){
            
                
            foreach (EnableAjaxGetStopExtends::$stop as $item){
                    
                if( !is_array($item) ) continue;
                    
                if( $item[0] == $module && $item[1] == $controller && $item[2] == $action ){
                    return false;
                }
                    
            }
            
                
                
        }
        
        return true;
        
    }
    
    
}