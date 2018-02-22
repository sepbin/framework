<?php
namespace Sepbin\System\Http\SessionHandler;

use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\Factory;

class Base extends \SessionHandler implements IFactoryEnable
{
    
    protected $expire;
    
    
    
    static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ){
        
        return Factory::get(Base::class, $config_namespace, $config_file, $config_path);
        
    }
    
    public function _init( \Sepbin\System\Util\FactoryConfig $config ){
        
    }
    
    public function setExpire( int $expire ){
        
        $this->expire = $expire;
        
    }
    
}