<?php
namespace Sepbin\System\Core;

class AppInfo extends Base
{
    
    /**
     * 
     * @var \Sepbin\Core\AppInfoItem[]
     */
    static $log = array();
    
    static public function Record($code, $name, $msg){
        
        $item = new AppInfoItem();
        
        $item->code = $code;
        $item->name = $name;
        $item->msg = $msg;
        
        self::$log[] = $item;
        
    }
    
}

class AppInfoItem
{
    
    public $code;
    
    public $name;
    
    public $msg;
    
}