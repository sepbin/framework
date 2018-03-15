<?php
namespace Sepbin\System\Further\CSRFToken;

use Sepbin\System\Frame\Hook\IMvcDispatch;
use Sepbin\System\Util\Data\GUID;
use Sepbin\System\Http\Cookie;

class CSRFTokenDispatchHook implements IMvcDispatch
{
    
    
    public function dispatchBefore( string $module, string $controller, string $action ) : bool{
        
        $s = getSession();
        if( $s->getStr(EnableCSRFToken::$tokenName) == '' ){
            $guid = GUID::create();
            $s->set(EnableCSRFToken::$tokenName, $guid);
            
            $c = Cookie::getInstance('XSRF_COOKIE');
            $c->set('XSRF-TOKEN', $guid);
        }
//         $s->del(EnableCSRFToken::$tokenName);
        return true;
        
    }
    
    
    public function dispatchAfter( $result ) : bool{
        
        return true;
        
    }
    
    
}