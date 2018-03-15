<?php
namespace Sepbin\System\Further\AjaxGetStopExtends;


use Sepbin\System\Frame\Hook\IMvcTemplateAdvHook;

class EnableAjaxGetStopExtends
{
    
    
    static $stop = [];
    
    
    
    static public function open( array $stop_controller ){
        
        self::$stop = $stop_controller;
        
        getApp()->registerHook(IMvcTemplateAdvHook::class, AjaxGetStopExtendsHook::class);
        
    }
    
}