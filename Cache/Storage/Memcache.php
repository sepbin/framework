<?php
namespace Sepbin\System\Cache\Storage;


use Sepbin\System\Util\Factory;

class Memcache extends ACache
{
    
    private $memcached;
    
    static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):Memcache{
        
        return Factory::get(Memcache::class, $config_namespace, $config_file, $config_path);
        
    }
    
    
    public function _init( \Sepbin\System\Util\FactoryConfig $config ){
        
        $this->memcached = new \Memcache();
        $servers = $config->get('server','');
        
        if( is_array($servers) ){
            foreach ($servers as $item){
                $this->addServer($item);
            }
        }else{
            $this->addServer($servers);
        }
        
        //大值自动压缩
        $threshold = $config->getInt('threshold', 0);
        $minSaving =  $config->getFloat('min_saving',0.2);
        if( $threshold > 0 ){
            $this->memcached->setcompressthreshold( $threshold, $minSaving );
        }
        
        
    }
    
    public function set( $key, $value, $expire ){
        
        return $this->memcached->set( $key, $value, MEMCACHE_COMPRESSED, $expire );
        
    }
    
    public function get( $key ){
        
        return $this->memcached->get( $key );
        
    }
    
    public function delete( $key ){
        
        return $this->memcached->delete( $key );
        
    }
    
    public function call( $name, ...$params ){
        
        return $this->memcached->$name( ...$params );
        
    }
    
    private function addServer( string $str ){
        $params = explode(':', $str);
        
        $host = isset($params[0])?$params[0]:'localhost';
        $port = isset($params[1])?$params[1]:11211;
        $weight = isset($params[2])?$params[2]:1;
        
        $this->memcached->addserver($host, $port, true, $weight);
        
    }
    
}