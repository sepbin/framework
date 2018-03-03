<?php
namespace Sepbin\System\Cache;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\Factory;
use Sepbin\System\Cache\Storage\Files;
use Sepbin\System\Util\Traits\TGetType;
use Sepbin\System\Cache\Storage\ACache;

class CacheManager extends Base implements IFactoryEnable
{
    
    use TGetType;
    
    /**
     * 
     * @var \Sepbin\System\Cache\Storage\ACache
     */
    private $driver;
    
    private $expire;
    
    static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):CacheManager{
        
        return Factory::get(CacheManager::class, $config_namespace, $config_file, $config_path);
        
    }
    
    public function _init( \Sepbin\System\Util\FactoryConfig $config ){
        
        $this->expire = $config->getInt('expire',1800);
        
        $driver = $config->getStr('driver',Files::class);
        
        $this->driver = $config->getClass('driver',Files::class, ACache::class);
        
    }
    
    
    /**
     * 缓存一个值
     * @param string $key   键值名称
     * @param mixed $value  储存的值
     * @param int $expire   生命周期 -1则继承全局设置 默认为-1
     * @return bool
     */
    public function set( $key, $value, $expire=-1 ){
        
        if( $expire == -1 ) $expire = $this->expire;
        return $this->driver->set( $key,$value, $expire );
        
    }
    
    
    /**
     * 获取一个键值
     * @param string $key
     * @param mixed $def
     * @return mixed
     */
    public function get( $key, $def='' ){
        
        $result = $this->driver->get( $key );
        
        if( empty($result) ) return $def;
        
        return $result;
        
    }
    
    
    /**
     * 删除一个键值
     * @param string $key
     * @return bool
     */
    public function delete( $key ){
        
        return $this->driver->delete( $key );
        
    }
    
    
    /**
     * 检查一个键是否存在
     * @param string $key
     */
    public function exists( $key ) : bool{
        
        return true;
        
    }
    
    
    /**
     * 调用原始方法
     * 很多储存方法都是调用的php原始类作的二次封装，比如memcache,redis等，它们都储存有原始类，如果你需要调用其他更原始的方法，请使用此方法
     * 如memcache对象 请查看php手册Memcache类
     * @param string $method 方法名
     * @param mixed ...$params 调用时的传参
     * @return mixed
     */
    public function call( $method, ...$params ){
        
        return $this->driver->call( $method, ...$params );
        
    }
    
}