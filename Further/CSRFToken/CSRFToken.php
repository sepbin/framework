<?php
namespace Sepbin\System\Further\CSRFToken;

use Sepbin\System\Frame\Exception\AccessDeniedException;

class CSRFToken
{
    
    static public function verify(){
        
        $s = getSession();
        $verify = $s->getStr(EnableCSRFToken::$tokenName);
        if( $verify == '' ){
            throw (new AccessDeniedException())->appendMsg( 'token verify error' );
        }
        
        $token = request()->getStr('_token');
        
        if( $token == '' && isset($_SERVER['X-CSRF-TOKEN']) ){
            $token = $_SERVER['X-CSRF-TOKEN'];
        }
        
        if( $token == '' && isset($_SERVER['X-XSRF-TOKEN']) ){
            $token = $_SERVER['X-XSRF-TOKEN'];
        }
        
        if( $token == '' || $verify != $token ){
            throw (new AccessDeniedException())->appendMsg( 'token verify error' );
        }
        
        $s->del(EnableCSRFToken::$tokenName);
        
        return true;
        
    }
    
}