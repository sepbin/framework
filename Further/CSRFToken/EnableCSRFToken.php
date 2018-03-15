<?php
namespace Sepbin\System\Further\CSRFToken;

use Sepbin\System\Frame\Hook\IMvcDispatch;
use Sepbin\System\Frame\Hook\IMvcModelHook;

class EnableCSRFToken
{
       
    static $tokenName;
    
    static public function open( $cookie_encrypt='', $cookie_encrypt_domain='', $token_name = 'csrf_token' ){
        
        self::$tokenName = 'csrf_token';
        
        $conf = config();
        $conf->set('XSRF_COOKIE.prefix','');
        $conf->set('XSRF_COOKIE.expire',0);
        $conf->set('XSRF_COOKIE.is_encrypt',false);
        
        if( $cookie_encrypt != '' && $cookie_encrypt_domain !='' ){
            $conf->set('XSRF_COOKIE.is_encrypt',true);
            $conf->set('XSRF_COOKIE.encrypt',$cookie_encrypt);
            $conf->set('XSRF_COOKIE.'.$conf->getSubConfName('encrypt',$cookie_encrypt), $cookie_encrypt_domain );
        }   
        
        
        getApp()->registerHook(IMvcDispatch::class, CSRFTokenDispatchHook::class);
        getApp()->registerHook(IMvcModelHook::class, CSRFTokenModelHook::class);
        
    }
    
}