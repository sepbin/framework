<?php
namespace Sepbin\System\Http;

use Sepbin\System\Util\Traits\TGetType;
use Sepbin\System\Core\Base;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\Factory;
use Sepbin\System\Http\Exception\SessionHandlerError;

class Session extends Base implements IFactoryEnable
{
    
    use TGetType;
    
    /**
     * session的cookie名称
     * @var string
     */
    private $name;
    
    /**
     * session为files储存时的储存路径
     * @var string
     */
    private $savePath = '';
    
    
    /**
     * session回收触发百分比
     * @var float
     */
    private $gcPercent = 0.01;
    
    /**
     * 超时时间
     * @var integer
     */
    private $expire = 1800;
    
    
    /**
     * 是否启用cookie存放会话id
     * @var bool
     */
    private $useCookie = true;
    
    private $saveHandler;
    
    
    static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):Session{
        
        $config_namespace = 'session';
        return Factory::get(Session::class, $config_namespace,$config_file,$config_path);
         
    }
    
    public function _init( \Sepbin\System\Util\FactoryConfig $config ){
        
        $this->name = $config->getStr('name','sep_uid');
        $this->savePath = $config->getStr('savePath');
        $this->gcPercent = $config->getFloat('gc_percent',0.01);
        $this->expire = $config->getInt('expire',1800);
        $this->useCookie = $config->getBool('use_cookie',true);
        $this->saveHandler = $config->getStr('save_handler', \Sepbin\System\Http\SessionHandler\Base::class);
        
        $option = array(
            'name' => $this->name,
            'save_path' => $this->savePath,
            'gc_divisor' => (1/$this->gcPercent).'',
            'gc_probability' => 1,
            'gc_maxlifetime' => $this->expire,
            'use_cookies' => $this->useCookie,
        );
        
        $handler = $config->getInstance('save_handler', $this->saveHandler);
        $handler->setExpire( $this->expire );
        if( !$handler instanceof \SessionHandler ){
            throw ( new SessionHandlerError() )->appendMsg( $this->saveHandler );
        }
        
        session_set_save_handler( $handler , true );
        session_start( $option );
        
    }
    
    public function get( string $name, $default='' ){
        
        return isset($_SESSION[$name])?$_SESSION[$name]:'';
        
    }
    
    public function set( string $name, $value ){
        
        $_SESSION[$name] = $value;
        
    }
    
}
