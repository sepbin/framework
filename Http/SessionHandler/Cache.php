<?php
namespace Sepbin\System\Http\SessionHandler;

use Sepbin\System\Util\Factory;
use Sepbin\System\Cache\CacheManager;

class Cache extends Base
{
    
    private $manager;
    
    private $prefix;
    
    static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ){
        
        return Factory::get(Cache::class, $config_namespace, $config_file, $config_path);
        
    }
    
    public function _init( \Sepbin\System\Util\FactoryConfig $config ){
        
        $cachename = $config->getStr('cache_config_name');
        $this->manager = CacheManager::getInstance($cachename);
        $this->prefix = $config->getStr('prefix','sses_');
        
    }
    
    public function open($save_path, $session_name){
        
        return true;
        
    }
    
    public function close () {
        
        return true;
        
    }
    
    public function write($session_id, $session_data){
        
        $this->manager->set( $this->getKey($session_id), $session_data, $this->expire );
        
    }
    
    public function read ($session_id) {
        
        return $this->manager->getStr( $this->getKey($session_id) );
        
    }
    
    public function destroy ($session_id) {
        
        $this->manager->delete($this->getKey($session_id));
        return true;
        
    }
    
    public function gc ($maxlifetime) {
        
        return true;
        
    }
    
    private function getKey( $key ){
        
        return $this->prefix.$key;
        
    }
    
    
}